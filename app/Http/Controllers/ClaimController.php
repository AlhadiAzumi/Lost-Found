<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClaimController extends Controller
{
    public function create(Item $item)
    {
        if (!in_array($item->type, ['found', 'ditemukan']) || !in_array($item->status, ['approved', 'disetujui'])) {
            return redirect()->route('items.show', $item)->with('error', 'Barang ini tidak dapat diklaim.');
        }

        if (auth()->id() === $item->user_id) {
            return redirect()->route('items.show', $item)->with('error', 'Kamu tidak dapat mengklaim barang yang kamu laporkan sendiri.');
        }

        // Cek apakah user sudah punya klaim pending pada item ini
        $existingClaim = Claim::where('item_id', $item->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'menunggu', 'approved', 'disetujui'])
            ->first();

        if ($existingClaim) {
            return redirect()->route('my.history')->with('error', 'Kamu sudah memiliki klaim aktif untuk barang ini.');
        }

        return view('claims.create', compact('item'));
    }

    public function store(Request $request, Item $item)
    {
        abort_unless(
            in_array($item->type, ['found', 'ditemukan'], true)
            && in_array($item->status, ['approved', 'disetujui'], true)
            && auth()->id() !== $item->user_id,
            404
        );

        $validated = $request->validate([
            'security_answer' => ['required', 'string', 'max:500'],
            'proof_image'     => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:2048'],
        ]);

        DB::transaction(function () use ($request, $item, $validated): void {
            $lockedItem = Item::query()->lockForUpdate()->findOrFail($item->id);

            if (!in_array($lockedItem->type, ['found', 'ditemukan'], true)
                || !in_array($lockedItem->status, ['approved', 'disetujui'], true)
                || $lockedItem->user_id === auth()->id()
                || Claim::where('item_id', $lockedItem->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->exists()) {
                throw ValidationException::withMessages([
                    'claim' => 'Barang ini tidak lagi tersedia untuk diklaim.',
                ]);
            }

            $proofPath = $request->hasFile('proof_image')
                ? $request->file('proof_image')->store('claims', 'public')
                : null;

            Claim::create([
                'item_id'         => $lockedItem->id,
                'user_id'         => auth()->id(),
                'security_answer' => $validated['security_answer'],
                'proof_image'     => $proofPath,
                'status'          => 'pending',
            ]);

            $lockedItem->update(['status' => 'claim_in_progress']);
        });

        return redirect()->route('my.history')
            ->with('success', 'Pengajuan klaim berhasil dikirim! Petugas admin/satpam akan segera memverifikasi bukti yang kamu berikan.');
    }
}
