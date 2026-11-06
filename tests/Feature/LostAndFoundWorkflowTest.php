<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Campus;
use App\Models\Claim;
use App\Models\Handover;
use App\Models\Item;
use App\Models\User;
use App\Support\CampusContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
            ->assertDontSee('Kampus Kedua Item');
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
            ->assertSee('Politeknik Contoh');
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

    public function test_campus_creation_web_access_is_restricted_to_the_configured_platform_admin(): void
    {
        config(['platform.admin_email' => 'platform@example.com']);
        $campusAdmin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($campusAdmin)
            ->get(route('platform.campuses.index'))
            ->assertForbidden();

        config(['platform.admin_email' => null]);
        $this->get(route('platform.campuses.index'))->assertForbidden();
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

    private function createCategory(): Category
    {
        return Category::create([
            'name' => 'Lainnya',
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
