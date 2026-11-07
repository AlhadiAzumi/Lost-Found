<?php

namespace Tests\Feature;

use App\Mail\CampusAdminInvitation;
use App\Models\Campus;
use App\Models\CampusApplication;
use App\Models\Category;
use App\Models\Claim;
use App\Models\Handover;
use App\Models\Item;
use App\Models\User;
use App\Support\CampusContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class LostAndFoundWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_assigns_the_database_supported_user_role(): void
    {
        $this->post(route('register'), [
            'name' => 'New Student',
            'email' => 'student@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('catalog.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'student@example.com',
            'role' => 'mahasiswa',
            'campus_id' => Campus::query()->where('slug', config('campus.default_slug'))->value('id'),
        ]);
    }

    public function test_found_reports_are_pending_and_store_the_security_answer_as_a_hash(): void
    {
        $user = User::factory()->create();
        $category = $this->createCategory();

        $this->actingAs($user)->post(route('reports.store'), [
            'type' => 'found',
            'title' => 'Blue Wallet',
            'category_id' => $category->id,
            'location' => 'Library',
            'date_event' => now()->toDateString(),
            'description' => 'Found near the entrance.',
            'security_question' => 'What is inside the wallet?',
            'security_answer' => 'a campus card',
        ])->assertSessionHasNoErrors();

        $item = Item::where('title', 'Blue Wallet')->firstOrFail();

        $this->assertSame('pending', $item->status);
        $this->assertTrue(Hash::check('a campus card', $item->getRawOriginal('security_answer')));
        $this->assertTrue($item->matchesSecurityAnswer(' A CAMPUS CARD '));
        $this->assertArrayNotHasKey('security_answer', $item->toArray());
    }

    public function test_pending_reports_cannot_be_viewed_by_other_users(): void
    {
        $item = $this->createFoundItem();
        $item->update(['status' => 'pending']);

        $this->get(route('items.show', ['item' => $item]))->assertNotFound();

        $this->actingAs($item->user)
            ->get(route('items.show', ['item' => $item]))
            ->assertOk();
    }

    public function test_admin_pages_show_navigation_to_handover_processing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        foreach (['admin.dashboard', 'admin.reports', 'admin.claims', 'admin.handovers'] as $route) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee(route('admin.handovers'))
                ->assertSee('Serah Terima');
        }
    }

    public function test_http_error_pages_use_the_branded_layout(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($user)
            ->get(route('admin.claims', ['campus' => config('campus.default_slug')]))
            ->assertForbidden()
            ->assertSee('Halaman ini khusus petugas kampus')
            ->assertSee('Pilih portal kampus')
            ->assertDontSee('Illuminate\\');

        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('Sepertinya barang ini tersesat')
            ->assertSee('Pilih portal kampus')
            ->assertDontSee('Illuminate\\');

        $this->assertStringContainsString('Kami sedang membereskan sesuatu', view('errors.500')->render());
    }

    public function test_campus_portals_isolate_accounts_and_lost_and_found_records(): void
    {
        $mainCampus = Campus::query()->where('slug', config('campus.default_slug'))->firstOrFail();
        $otherCampus = Campus::create([
            'name' => 'Kampus Kedua',
            'slug' => 'kampus-kedua',
            'is_active' => true,
        ]);
        $mainUser = User::factory()->create([
            'email' => 'same-user@example.com',
            'password' => Hash::make('password'),
        ]);

        app(CampusContext::class)->set($mainCampus);
        $this->createFoundItem('Kampus Utama Item');

        $this->post(route('login', ['campus' => $otherCampus->slug]), [
            'email' => $mainUser->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        app(CampusContext::class)->set($otherCampus);
        $otherItem = $this->createFoundItem('Kampus Kedua Item');
        $this->assertSame($otherCampus->id, $otherItem->campus_id);
        $otherClaimant = User::factory()->create();
        $otherClaim = Claim::create([
            'item_id' => $otherItem->id,
            'user_id' => $otherClaimant->id,
            'security_answer' => 'jawaban',
            'status' => 'completed',
        ]);
        Handover::create([
            'claim_id' => $otherClaim->id,
            'item_id' => $otherItem->id,
            'claimed_by' => $otherClaimant->id,
            'handed_over_by' => $otherItem->user_id,
            'handover_date' => now(),
        ]);

        $this->get(route('catalog.index', ['campus' => $mainCampus->slug]))
            ->assertOk()
            ->assertSee('Kampus Utama Item')
            ->assertDontSee('Kampus Kedua Item');
        $this->get(route('home', ['campus' => $mainCampus->slug]))
            ->assertOk()
            ->assertSee('Kampus Utama Item')
            ->assertDontSee('Kampus Kedua Item');
        $this->get(route('catalog.index', ['campus' => $otherCampus->slug]))
            ->assertOk()
            ->assertSee('Kampus Kedua Item')
            ->assertDontSee('Kampus Utama Item');
        $this->get(route('home', ['campus' => $otherCampus->slug]))
            ->assertOk()
            ->assertSee('Kampus Kedua Item')
            ->assertDontSee('Kampus Utama Item');
        $this->get(route('items.show', [
            'campus' => $mainCampus->slug,
            'item' => $otherItem,
        ]))->assertNotFound();

        $mainAdmin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($mainAdmin)
            ->get(route('admin.reports', ['campus' => $otherCampus->slug]))
            ->assertForbidden();

        $this->get(route('admin.claims', ['campus' => $mainCampus->slug]))
            ->assertOk()
            ->assertDontSee('Kampus Kedua Item');
        $this->get(route('admin.handovers', ['campus' => $mainCampus->slug]))
            ->assertOk()
            ->assertDontSee('Kampus Kedua Item');
    }

    public function test_campus_directory_and_provisioning_command_create_a_portal(): void
    {
        $this->artisan('campus:create', [
            'name' => 'Politeknik Contoh',
            'slug' => 'politeknik-contoh',
        ])->assertExitCode(0);

        $this->get(route('campuses.index'))
            ->assertOk()
            ->assertSee('Pilih Kampus')
            ->assertDontSee(route('catalog.index'))
            ->assertSee('Politeknik Contoh')
            ->assertSee('Ajukan portal kampus baru')
            ->assertSee('Email pemohon');
        $this->get(route('campuses.directory'))
            ->assertOk()
            ->assertSee('Pilih Kampus')
            ->assertDontSee(route('catalog.index'));
        $this->get(route('home', ['campus' => 'politeknik-contoh']))
            ->assertOk()
            ->assertSee('Lost &amp; Found', false);
    }

    public function test_campus_admin_can_be_promoted_only_after_registration_in_that_portal(): void
    {
        $campus = Campus::create([
            'name' => 'Politeknik Admin',
            'slug' => 'politeknik-admin',
            'is_active' => true,
        ]);
        app(CampusContext::class)->set($campus);
        $user = User::factory()->create(['email' => 'admin@politeknik-admin.ac.id']);

        $this->artisan('campus:promote-admin', [
            'slug' => $campus->slug,
            'email' => $user->email,
        ])->assertExitCode(0);

        $this->assertSame('admin', $user->fresh()->role);
    }

    public function test_platform_admin_can_manage_campuses_from_the_web(): void
    {
        config(['platform.admin_email' => 'platform@example.com']);
        $admin = User::factory()->create([
            'email' => 'platform@example.com',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('platform.campuses.index'))
            ->assertOk()
            ->assertSee('Kelola Kampus')
            ->assertSee('Kampus Utama')
            ->assertSee(route('platform.campuses.index', ['campus' => null]));

        $this->post(route('platform.campuses.store'), [
            'name' => 'Kampus Barat',
            'slug' => 'kampus-barat',
        ])->assertRedirect(route('platform.campuses.index'))
            ->assertSessionHas('success');

        $campus = Campus::query()->where('slug', 'kampus-barat')->firstOrFail();
        $this->assertTrue($campus->is_active);

        $this->patch(route('platform.campuses.status', ['managedCampus' => $campus]), [
            'is_active' => 0,
        ])->assertSessionHas('success');
        $this->assertFalse($campus->fresh()->is_active);
        $this->get(route('home', ['campus' => $campus->slug]))->assertNotFound();

        $this->patch(route('platform.campuses.status', ['managedCampus' => $campus]), [
            'is_active' => 1,
        ])->assertSessionHas('success');
        $this->assertTrue($campus->fresh()->is_active);

        $mainCampus = Campus::query()->where('slug', config('campus.default_slug'))->firstOrFail();
        $this->patch(route('platform.campuses.status', ['managedCampus' => $mainCampus]), [
            'is_active' => 0,
        ])->assertSessionHas('error');
        $this->assertTrue($mainCampus->fresh()->is_active);

        $this->from(route('platform.campuses.index', ['campus' => null]))
            ->post(route('platform.campuses.store', ['campus' => null]), [
                'name' => 'Duplikat',
                'slug' => 'kampus-barat',
            ])->assertSessionHasErrors('slug');
    }

    public function test_platform_admin_can_approve_campus_application_and_invite_requester_as_admin(): void
    {
        Mail::fake();
        $this->post(route('campus-applications.store', ['campus' => null]), [
            'name' => 'universitas nusantara',
            'email' => 'admin@nusantara.ac.id',
        ])->assertRedirect(route('campuses.index'))
            ->assertSessionHas('application_submitted');

        $application = CampusApplication::query()->firstOrFail();
        $this->assertSame('pending', $application->status);
        $this->assertSame('Universitas Nusantara', $application->name);
        $this->assertDatabaseMissing('campuses', ['slug' => 'universitas-nusantara']);
        $this->post(route('register', ['campus' => config('campus.default_slug')]), [
            'name' => 'Admin Nusantara',
            'email' => 'admin@nusantara.ac.id',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertSessionHasErrors('email');
        $this->post(route('campus-applications.store', ['campus' => null]), [
            'name' => 'Universitas Nusantara',
            'email' => 'ADMIN@NUSANTARA.AC.ID',
        ])->assertSessionHasErrors('email');

        config(['platform.admin_email' => 'platform@example.com']);
        $platformAdmin = User::factory()->create([
            'email' => 'platform@example.com',
            'role' => 'admin',
        ]);

        $this->actingAs($platformAdmin)
            ->get(route('platform.campuses.index', ['campus' => null]))
            ->assertOk()
            ->assertSee('Universitas Nusantara')
            ->assertSee('Setujui');

        $this->post(route('platform.campuses.applications.approve', [
            'application' => $application,
            'campus' => null,
        ]))->assertRedirect(route('platform.campuses.index', ['campus' => null]))
            ->assertSessionHas('success');

        $application->refresh();
        $this->assertSame('approved', $application->status);
        $this->assertDatabaseHas('campuses', [
            'id' => $application->campus_id,
            'name' => 'Universitas Nusantara',
            'slug' => 'universitas-nusantara',
            'is_active' => 1,
        ]);

        $invitation = null;
        Mail::assertSent(CampusAdminInvitation::class, function (CampusAdminInvitation $mail) use (&$invitation, $application): bool {
            $invitation = $mail;

            return $mail->hasTo($application->email);
        });

        $this->assertStringContainsString(
            route('campus-applications.setup', [
                'application' => $application,
                'token' => $invitation->token,
                'campus' => null,
            ]),
            $invitation->render(),
        );

        $firstToken = $invitation->token;
        $this->post(route('platform.campuses.applications.resend', [
            'application' => $application,
            'campus' => null,
        ]))->assertRedirect(route('platform.campuses.index', ['campus' => null]))
            ->assertSessionHas('success');

        $this->get(route('campus-applications.setup', [
            'application' => $application,
            'token' => $firstToken,
            'campus' => null,
        ]))->assertRedirect(route('campuses.index'));

        $resentInvitation = null;
        Mail::assertSent(CampusAdminInvitation::class, function (CampusAdminInvitation $mail) use (&$resentInvitation, $application): bool {
            if ($mail->application->is($application)) {
                $resentInvitation = $mail;
            }

            return $mail->hasTo($application->email);
        });
        $this->assertNotSame($firstToken, $resentInvitation->token);

        $setupRoute = [
            'application' => $application,
            'token' => $resentInvitation->token,
            'campus' => null,
        ];
        $this->get(route('campus-applications.setup', $setupRoute))
            ->assertOk()
            ->assertSee('Buat Akun Admin');

        $this->post(route('campus-applications.setup.complete', $setupRoute), [
            'name' => 'Admin Nusantara',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('home', ['campus' => 'universitas-nusantara']))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'admin@nusantara.ac.id',
            'role' => 'admin',
            'campus_id' => $application->campus_id,
        ]);
        $this->assertNotNull($application->fresh()->admin_created_at);
        $this->get(route('campus-applications.setup', $setupRoute))
            ->assertRedirect(route('campuses.index'));
    }

    public function test_campus_creation_web_access_is_restricted_to_the_configured_platform_admin(): void
    {
        config(['platform.admin_email' => 'platform@example.com']);
        $campusAdmin = User::factory()->create(['role' => 'admin']);
        $application = CampusApplication::create([
            'name' => 'Portal Tertutup',
            'email' => 'requester@example.com',
            'slug' => 'portal-tertutup',
        ]);

        $this->actingAs($campusAdmin)
            ->get(route('platform.campuses.index'))
            ->assertForbidden();
        $this->post(route('platform.campuses.applications.approve', [
            'application' => $application,
            'campus' => null,
        ]))->assertForbidden();

        config(['platform.admin_email' => null]);
        $this->get(route('platform.campuses.index'))->assertForbidden();
    }

    public function test_resending_campus_invitation_handles_mail_transport_failure(): void
    {
        config(['platform.admin_email' => 'platform@example.com']);
        $admin = User::factory()->create([
            'email' => 'platform@example.com',
            'role' => 'admin',
        ]);
        $campus = Campus::create([
            'name' => 'Kampus Undangan',
            'slug' => 'kampus-undangan',
            'is_active' => true,
        ]);
        $application = CampusApplication::create([
            'name' => 'Kampus Undangan',
            'email' => 'requester@example.com',
            'slug' => 'kampus-undangan',
            'status' => 'approved',
            'campus_id' => $campus->id,
            'invitation_token_hash' => Hash::make('old-token'),
            'invitation_expires_at' => now()->addDays(7),
        ]);
        $pendingMail = \Mockery::mock(PendingMail::class);
        $pendingMail->shouldReceive('send')
            ->once()
            ->with(\Mockery::type(CampusAdminInvitation::class))
            ->andThrow(new TransportException('SMTP unavailable'));
        Mail::shouldReceive('to')
            ->once()
            ->with($application->email)
            ->andReturn($pendingMail);

        $this->actingAs($admin)
            ->post(route('platform.campuses.applications.resend', [
                'application' => $application,
                'campus' => null,
            ]))
            ->assertRedirect(route('platform.campuses.index', ['campus' => null]))
            ->assertSessionHas('error', 'Email undangan gagal dikirim. Tautan baru tersimpan; coba kirim ulang setelah layanan email tersedia.');

        $this->assertNotSame(
            $application->invitation_token_hash,
            $application->fresh()->invitation_token_hash,
        );
        $this->assertNull($application->fresh()->admin_created_at);
    }

    public function test_platform_admin_can_reject_a_campus_application_without_creating_a_portal(): void
    {
        config(['platform.admin_email' => 'platform@example.com']);
        $admin = User::factory()->create([
            'email' => 'platform@example.com',
            'role' => 'admin',
        ]);
        $application = CampusApplication::create([
            'name' => 'Kampus Ditolak',
            'email' => 'requester@example.com',
            'slug' => 'kampus-ditolak',
        ]);

        $this->actingAs($admin)
            ->post(route('platform.campuses.applications.reject', [
                'application' => $application,
                'campus' => null,
            ]))->assertRedirect(route('platform.campuses.index', ['campus' => null]))
            ->assertSessionHas('success');

        $this->assertSame('rejected', $application->fresh()->status);
        $this->assertDatabaseMissing('campuses', ['slug' => 'kampus-ditolak']);
    }

    public function test_lowercase_campus_names_are_title_cased_for_new_and_existing_portals(): void
    {
        $campus = Campus::create([
            'name' => 'universitas malikussaleh',
            'slug' => 'universitas-malikussaleh',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->id,
            'name' => 'Universitas Malikussaleh',
        ]);
        $this->assertSame('Universitas Malikussaleh', $campus->name);

        DB::table('campuses')
            ->where('id', $campus->id)
            ->update(['name' => 'politeknik negeri lhokseumawe']);

        $this->assertSame('Politeknik Negeri Lhokseumawe', $campus->fresh()->name);
    }

    public function test_a_claim_requires_a_matching_security_answer_and_a_valid_handover_code(): void
    {
        $item = $this->createFoundItem('Blue Wallet', 'a campus card');
        $claimant = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($claimant)->post(route('claims.store', ['item' => $item]), [
            'security_answer' => 'wrong answer',
        ])->assertRedirect(route('my.history'));

        $claim = Claim::firstOrFail();
        $this->assertSame('claim_in_progress', $item->fresh()->status);

        $this->actingAs($admin)->post(route('admin.claims.verify', ['claim' => $claim]), [
            'action' => 'approve',
        ])->assertSessionHas('error');

        $this->assertSame('pending', $claim->fresh()->status);

        $this->post(route('admin.claims.verify', ['claim' => $claim]), [
            'action' => 'reject',
            'admin_note' => 'Jawaban belum cocok.',
        ])->assertSessionHasNoErrors();

        $this->assertSame('approved', $item->fresh()->status);

        $this->actingAs($claimant)->post(route('claims.store', ['item' => $item]), [
            'security_answer' => ' A CAMPUS CARD ',
        ])->assertRedirect(route('my.history'));

        $claim = Claim::where('status', 'pending')->firstOrFail();
        $this->actingAs($admin)->post(route('admin.claims.verify', ['claim' => $claim]), [
            'action' => 'approve',
        ])->assertSessionHasNoErrors();

        $claim->refresh();
        $this->assertSame('approved', $claim->status);
        $this->assertNotEmpty($claim->handover_code);

        $this->post(route('admin.handovers.process', ['claim' => $claim]), [
            'code' => 'LFP-WRNG-CODE',
        ])->assertSessionHas('error');

        $this->assertSame('approved', $claim->fresh()->status);

        $this->post(route('admin.handovers.process', ['claim' => $claim]), [
            'code' => $claim->handover_code,
        ])->assertRedirect(route('admin.handovers'));

        $this->assertSame('completed', $claim->fresh()->status);
        $this->assertSame('returned', $item->fresh()->status);
        $this->assertDatabaseHas('handovers', ['claim_id' => $claim->id]);
    }

    public function test_rejected_reports_require_a_reason_and_are_not_public(): void
    {
        $item = $this->createFoundItem();
        $item->update(['status' => 'pending']);
        $admin = User::factory()->create(['role' => 'satpam']);

        $this->actingAs($admin)->post(route('admin.reports.status', ['item' => $item]), [
            'status' => 'rejected',
        ])->assertSessionHasErrors('rejection_reason');

        $this->post(route('admin.reports.status', ['item' => $item]), [
            'status' => 'rejected',
            'rejection_reason' => 'Insufficient report details.',
        ])->assertSessionHasNoErrors();

        $this->assertSame('rejected', $item->fresh()->status);
        $this->assertSame('Insufficient report details.', $item->fresh()->rejection_reason);
        $this->actingAsGuest();
        $this->get(route('items.show', ['item' => $item]))->assertNotFound();
    }

    public function test_database_seeder_uses_the_current_schema(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@campus.ac.id',
            'role' => 'admin',
        ]);
        $this->assertDatabaseHas('items', [
            'title' => 'Flashdisk Sandisk 32GB Merah',
            'type' => 'lost',
            'status' => 'approved',
        ]);
        $foundItem = Item::where('type', 'found')->firstOrFail();
        $this->assertTrue($foundItem->matchesSecurityAnswer('Anime'));
    }

    public function test_login_is_rate_limited_after_repeated_failures(): void
    {
        $user = User::factory()->create();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertStringContainsString(
            'Terlalu banyak percobaan masuk',
            (string) session('errors')->first('email')
        );
    }

    public function test_rejecting_a_claim_requires_an_admin_note(): void
    {
        $item = $this->createFoundItem();
        $claimant = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($claimant)->post(route('claims.store', ['item' => $item]), [
            'security_answer' => 'blue sticker',
        ])->assertRedirect(route('my.history'));

        $claim = Claim::firstOrFail();

        $this->actingAs($admin)->post(route('admin.claims.verify', ['claim' => $claim]), [
            'action' => 'reject',
        ])->assertSessionHasErrors('admin_note');

        $this->assertSame('pending', $claim->fresh()->status);
    }

    public function test_claim_proof_is_only_accessible_to_the_owner_and_admins(): void
    {
        Storage::fake('local');

        $item = $this->createFoundItem();
        $claimant = User::factory()->create();
        $otherUser = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($claimant)->post(route('claims.store', ['item' => $item]), [
            'security_answer' => 'blue sticker',
            'proof_image' => UploadedFile::fake()->createWithContent(
                'bukti.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2n2kAAAAASUVORK5CYII=')
            ),
        ])->assertRedirect(route('my.history'));

        $claim = Claim::firstOrFail();

        $this->assertNotNull($claim->proof_image);
        Storage::disk('local')->assertExists($claim->proof_image);

        $this->actingAs($claimant)
            ->get(route('claims.proof', ['claim' => $claim]))
            ->assertOk();

        $this->actingAs($otherUser)
            ->get(route('claims.proof', ['claim' => $claim]))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('claims.proof', ['claim' => $claim]))
            ->assertOk();

        $this->actingAsGuest();
        $this->get(route('claims.proof', ['claim' => $claim]))
            ->assertRedirect(route('login'));
    }

    public function test_claim_serialization_hides_security_answer_and_handover_code(): void
    {
        $item = $this->createFoundItem();

        $claim = Claim::create([
            'item_id' => $item->id,
            'user_id' => User::factory()->create()->id,
            'security_answer' => 'jawaban rahasia',
            'handover_code' => 'LFP-AAAA-BBBB',
            'status' => 'approved',
        ]);

        $serialized = $claim->toArray();

        $this->assertArrayNotHasKey('security_answer', $serialized);
        $this->assertArrayNotHasKey('handover_code', $serialized);
        $this->assertSame('jawaban rahasia', $claim->security_answer);
        $this->assertSame('LFP-AAAA-BBBB', $claim->handover_code);
    }

    private function createCategory(): Category
    {
        return Category::firstOrCreate([
            'name' => 'Lainnya',
        ], [
            'slug' => 'lainnya',
            'icon' => 'tag',
        ]);
    }

    private function createFoundItem(string $title = 'Found Item', string $answer = 'blue sticker'): Item
    {
        $user = User::factory()->create();

        return Item::create([
            'user_id' => $user->id,
            'category_id' => $this->createCategory()->id,
            'type' => 'found',
            'title' => $title,
            'description' => 'Item description.',
            'location' => 'Library',
            'date_event' => now()->toDateString(),
            'security_question' => 'What is the hidden detail?',
            'security_answer' => $answer,
            'status' => 'approved',
        ]);
    }
}
