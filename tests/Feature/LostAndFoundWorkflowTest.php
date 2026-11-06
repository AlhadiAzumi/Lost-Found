<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LostAndFoundWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_assigns_the_database_supported_user_role(): void
    {
        $this->post('/register', [
            'name' => 'New Student',
            'email' => 'student@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('catalog.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'student@example.com',
            'role' => 'mahasiswa',
        ]);
    }

    public function test_found_reports_are_pending_and_store_the_security_answer_as_a_hash(): void
    {
        $user = User::factory()->create();
        $category = $this->createCategory();

        $this->actingAs($user)->post('/reports', [
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

        $this->get(route('items.show', $item))->assertNotFound();

        $this->actingAs($item->user)
            ->get(route('items.show', $item))
            ->assertOk();
    }

    public function test_a_claim_requires_a_matching_security_answer_and_a_valid_handover_code(): void
    {
        $item = $this->createFoundItem('Blue Wallet', 'a campus card');
        $claimant = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($claimant)->post(route('claims.store', $item), [
            'security_answer' => 'wrong answer',
        ])->assertRedirect(route('my.history'));

        $claim = Claim::firstOrFail();
        $this->assertSame('claim_in_progress', $item->fresh()->status);

        $this->actingAs($admin)->post(route('admin.claims.verify', $claim), [
            'action' => 'approve',
        ])->assertSessionHas('error');

        $this->assertSame('pending', $claim->fresh()->status);

        $this->post(route('admin.claims.verify', $claim), [
            'action' => 'reject',
            'admin_note' => 'Jawaban belum cocok.',
        ])->assertSessionHasNoErrors();

        $this->assertSame('approved', $item->fresh()->status);

        $this->actingAs($claimant)->post(route('claims.store', $item), [
            'security_answer' => ' A CAMPUS CARD ',
        ])->assertRedirect(route('my.history'));

        $claim = Claim::where('status', 'pending')->firstOrFail();
        $this->actingAs($admin)->post(route('admin.claims.verify', $claim), [
            'action' => 'approve',
        ])->assertSessionHasNoErrors();

        $claim->refresh();
        $this->assertSame('approved', $claim->status);
        $this->assertNotEmpty($claim->handover_code);

        $this->post(route('admin.handovers.process', $claim), [
            'code' => 'LFP-WRNG-CODE',
        ])->assertSessionHas('error');

        $this->assertSame('approved', $claim->fresh()->status);

        $this->post(route('admin.handovers.process', $claim), [
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

        $this->actingAs($admin)->post(route('admin.reports.status', $item), [
            'status' => 'rejected',
        ])->assertSessionHasErrors('rejection_reason');

        $this->post(route('admin.reports.status', $item), [
            'status' => 'rejected',
            'rejection_reason' => 'Insufficient report details.',
        ])->assertSessionHasNoErrors();

        $this->assertSame('rejected', $item->fresh()->status);
        $this->assertSame('Insufficient report details.', $item->fresh()->rejection_reason);
        $this->actingAsGuest();
        $this->get(route('items.show', $item))->assertNotFound();
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
