<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\Item;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function notify(User $user, string $type, string $title, string $message, ?string $url = null): Notification
    {
        return $user->notifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
        ]);
    }

    public function reportStatusChanged(Item $item): void
    {
        $item->loadMissing('user', 'campus');

        $owner = $item->user;

        if ($owner === null || $owner->is(auth()->user())) {
            return;
        }

        $url = route('items.show', [
            'campus' => $item->campus?->slug ?? config('campus.default_slug'),
            'item' => $item,
        ]);

        if ($item->status === 'approved') {
            $this->notify(
                $owner,
                'report_approved',
                'Laporan Disetujui',
                "Laporan \"{$item->title}\" telah disetujui dan sekarang tampil di katalog barang.",
                $url
            );

            return;
        }

        $message = "Laporan \"{$item->title}\" ditolak oleh petugas.";

        if (filled($item->rejection_reason)) {
            $message .= " Alasan: {$item->rejection_reason}";
        }

        $this->notify($owner, 'report_rejected', 'Laporan Ditolak', $message, $url);
    }

    public function claimDecided(Claim $claim): void
    {
        $claim->loadMissing('user', 'item.campus');

        $claimant = $claim->user;
        $item = $claim->item;

        if ($claimant === null || $item === null || $claimant->is(auth()->user())) {
            return;
        }

        $url = route('my.history', [
            'campus' => $item->campus?->slug ?? config('campus.default_slug'),
            'tab' => 'claims',
        ]);

        if ($claim->status === 'approved') {
            $this->notify(
                $claimant,
                'claim_approved',
                'Klaim Disetujui',
                "Klaim kamu untuk \"{$item->title}\" telah disetujui. Kode Serah Terima: {$claim->handover_code}. Tunjukkan kode ini ke petugas Pos Satpam.",
                $url
            );

            return;
        }

        $message = "Klaim kamu untuk \"{$item->title}\" ditolak.";

        if (filled($claim->admin_note)) {
            $message .= " Alasan: {$claim->admin_note}";
        }

        $this->notify($claimant, 'claim_rejected', 'Klaim Ditolak', $message, $url);
    }

    public function claimSubmitted(Claim $claim): void
    {
        $claim->loadMissing('user', 'item.campus');

        $item = $claim->item;

        if ($item === null) {
            return;
        }

        $admins = User::query()
            ->whereIn('role', ['admin', 'satpam'])
            ->where('campus_id', $item->campus_id)
            ->get();

        $url = route('admin.claims', [
            'campus' => $item->campus?->slug ?? config('campus.default_slug'),
        ]);

        foreach ($admins as $admin) {
            $this->notify(
                $admin,
                'claim_submitted',
                'Klaim Baru Masuk',
                "{$claim->user?->name} mengajukan klaim untuk \"{$item->title}\". Silakan verifikasi.",
                $url
            );
        }
    }

    public function handoverCompleted(Claim $claim): void
    {
        $claim->loadMissing('user', 'item.campus');

        $claimant = $claim->user;
        $item = $claim->item;

        if ($claimant === null || $item === null) {
            return;
        }

        $this->notify(
            $claimant,
            'handover_completed',
            'Barang Telah Diserahkan',
            "Barang \"{$item->title}\" telah diserahkan setelah Kode Serah Terima diverifikasi.",
            route('my.history', [
                'campus' => $item->campus?->slug ?? config('campus.default_slug'),
                'tab' => 'claims',
            ])
        );
    }
}
