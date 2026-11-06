<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $validated = $request->validate([
            'security_answer' => ['required', 'string', 'max:500'],
            'proof_image'     => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:2048'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('claims', 'public');
        }

        $claim = Claim::create([
            'item_id'         => $item->id,
            'user_id'         => auth()->id(),
            'security_answer' => $validated['security_answer'],
            'proof_image'     => $proofPath,
            'status'          => 'pending',
        ]);

        // Tandai item sedang diproses klaim
        $item->update(['status' => 'claim_in_progress']);

        return redirect()->route('my.history')
            ->with('success', 'Pengajuan klaim berhasil dikirim! Petugas admin/satpam akan segera memverifikasi bukti yang kamu berikan.');
    }
}

