<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Item;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_decision_notifies_the_reporter(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $item = $this->createItem($owner, status: 'pending');

        $this->actingAs($admin)->post(route('admin.reports.status', ['item' => $item]), [
            'status' => 'approved',
        ])->assertSessionHasNoErrors();

        $notification = Notification::query()->where('user_id', $owner->id)->firstOrFail();

        $this->assertSame('report_approved', $notification->type);
        $this->assertNull($notification->read_at);
        $this->assertStringContainsString($item->title, $notification->message);

        $this->actingAs($owner)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Laporan Disetujui')
            ->assertSee($item->title);

        $this->post(route('notifications.read', ['notification' => $notification]))
            ->assertRedirect(route('items.show', [
                'campus' => config('campus.default_slug'),
                'item' => $item,
            ]));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_rejected_report_notification_contains_the_rejection_reason(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => 'satpam']);
        $item = $this->createItem($owner, status: 'pending');

        $this->actingAs($admin)->post(route('admin.reports.status', ['item' => $item]), [
            'status' => 'rejected',
            'rejection_reason' => 'Foto barang tidak jelas.',
        ])->assertSessionHasNoErrors();

        $notification = Notification::query()->where('user_id', $owner->id)->firstOrFail();

        $this->assertSame('report_rejected', $notification->type);
        $this->assertStringContainsString('Foto barang tidak jelas.', $notification->message);
    }

    public function test_claim_and_handover_decisions_notify_the_claimant(): void
    {
        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $item = $this->createItem($owner);

        $this->actingAs($claimant)->post(route('claims.store', ['item' => $item]), [
            'security_answer' => 'blue sticker',
        ])->assertRedirect(route('my.history'));

        $claim = Claim::query()->firstOrFail();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'type' => 'claim_submitted',
        ]);

        $this->actingAs($admin)->post(route('admin.claims.verify', ['claim' => $claim]), [
            'action' => 'approve',
        ])->assertSessionHasNoErrors();

        $claim->refresh();

        $approvedNotification = Notification::query()
            ->where('user_id', $claimant->id)
            ->where('type', 'claim_approved')
            ->firstOrFail();

        $this->assertStringContainsString($claim->handover_code, $approvedNotification->message);

        $this->actingAs($admin)->post(route('admin.handovers.process', ['claim' => $claim]), [
            'code' => $claim->handover_code,
        ])->assertRedirect(route('admin.handovers'));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $claimant->id,
            'type' => 'handover_completed',
        ]);
    }

    public function test_users_cannot_read_other_users_notifications(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $notification = Notification::create([
            'user_id' => $owner->id,
            'type' => 'info',
            'title' => 'Pesan Uji',
            'message' => 'Hanya untuk pemilik akun.',
        ]);

        $this->actingAs($otherUser)
            ->post(route('notifications.read', ['notification' => $notification]))
            ->assertForbidden();

        $this->assertNull($notification->fresh()->read_at);

        $this->actingAs($owner)
            ->post(route('notifications.read-all'))
            ->assertSessionHas('success');

        $this->assertNotNull($notification->fresh()->read_at);
    }

    private function createItem(User $owner, string $status = 'approved'): Item
    {
        $category = Category::firstOrCreate([
            'name' => 'Lainnya',
        ], [
            'slug' => 'lainnya',
            'icon' => 'tag',
        ]);

        return Item::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'type' => 'found',
            'title' => 'Dompet Biru',
            'description' => 'Ditemukan di perpustakaan.',
            'location' => 'Perpustakaan',
            'date_event' => now()->toDateString(),
            'security_question' => 'Apa isi dompet?',
            'security_answer' => 'blue sticker',
            'status' => $status,
        ]);
    }
}
