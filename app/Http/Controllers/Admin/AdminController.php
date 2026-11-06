<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Handover;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_items'     => Item::count(),
            'lost_items'      => Item::whereIn('type', ['lost', 'hilang'])->count(),
            'found_items'     => Item::whereIn('type', ['found', 'ditemukan'])->count(),
            'pending_claims'  => Claim::whereIn('status', ['pending', 'menunggu'])->count(),
            'returned_items'  => Item::whereIn('status', ['returned', 'selesai'])->count(),
        ];

        $recentReports = Item::with('category', 'user')->orderByDesc('id')->take(5)->get();
        $recentClaims  = Claim::with('item', 'user')->whereIn('status', ['pending', 'menunggu'])->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentReports', 'recentClaims'));
    }

    public function reports()
    {
        $items = Item::with('category', 'user')->orderByDesc('id')->paginate(10);
        return view('admin.reports', compact('items'));
    }

    public function updateReportStatus(Request $request, Item $item)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'rejection_reason' => ['required_if:status,rejected', 'nullable', 'string', 'max:2000'],
        ]);

        if ($item->status !== 'pending') {
            return back()->with('error', 'Hanya laporan yang menunggu moderasi yang dapat diproses.');
        }

        $item->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['status'] === 'rejected'
                ? $validated['rejection_reason']
                : null,
            'approved_by' => $validated['status'] === 'approved' ? auth()->id() : null,
        ]);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function claims()
    {
        $claims = Claim::with('item', 'user', 'verifier')->orderByDesc('id')->paginate(10);
        return view('admin.claims', compact('claims'));
    }

    public function verifyClaim(Request $request, Claim $claim)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($claim->status !== 'pending') {
            return back()->with('error', 'Klaim ini sudah diproses.');
        }

        if ($validated['action'] === 'approve'
            && $claim->item->matchesSecurityAnswer($claim->security_answer) === false) {
            return back()->with('error', 'Jawaban keamanan tidak cocok. Klaim tidak dapat disetujui.');
        }

        if ($validated['action'] === 'approve') {
            $code = DB::transaction(function () use ($claim, $validated): string {
                $lockedClaim = Claim::query()->lockForUpdate()->findOrFail($claim->id);
                abort_if($lockedClaim->status !== 'pending', 409, 'Klaim ini sudah diproses.');

                do {
                    $code = 'LFP-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
                } while (Claim::where('handover_code', $code)->exists());

                $lockedClaim->update([
                    'status'        => 'approved',
                    'handover_code' => $code,
                    'verified_by'   => auth()->id(),
                    'admin_note'    => $validated['admin_note'] ?? null,
                ]);

                return $code;
            });

            return back()->with('success', "Klaim berhasil disetujui! Kode Serah Terima diterbitkan: $code");
        }

        DB::transaction(function () use ($claim, $validated): void {
            $lockedClaim = Claim::query()->lockForUpdate()->findOrFail($claim->id);
            abort_if($lockedClaim->status !== 'pending', 409, 'Klaim ini sudah diproses.');

            $lockedClaim->update([
                'status'      => 'rejected',
                'verified_by' => auth()->id(),
                'admin_note'  => $validated['admin_note'] ?? 'Bukti atau jawaban tidak sesuai.',
            ]);

            if (!$lockedClaim->item->claims()->whereIn('status', ['pending', 'approved'])->exists()) {
                $lockedClaim->item->update(['status' => 'approved']);
            }
        });

        return back()->with('success', 'Klaim telah ditolak.');
    }

    public function handovers(Request $request)
    {
        $claimToHandover = null;
        $searchCode = $request->query('code');

        if ($searchCode) {
            $claimToHandover = Claim::with('item', 'user')
                ->where('handover_code', trim($searchCode))
                ->first();
        }

        $recentHandovers = Handover::with(['item', 'recipient', 'officer'])->orderByDesc('id')->paginate(10);

        return view('admin.handovers', compact('claimToHandover', 'searchCode', 'recentHandovers'));
    }

    public function processHandover(Request $request, Claim $claim)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $processed = DB::transaction(function () use ($claim, $validated): bool {
            $lockedClaim = Claim::query()->lockForUpdate()->findOrFail($claim->id);

            if ($lockedClaim->status !== 'approved'
                || $lockedClaim->handover()->exists()
                || !hash_equals((string) $lockedClaim->handover_code, trim($validated['code']))) {
                return false;
            }

            Handover::create([
                'claim_id'       => $lockedClaim->id,
                'item_id'        => $lockedClaim->item_id,
                'claimed_by'     => $lockedClaim->user_id,
                'handed_over_by' => auth()->id(),
                'handover_date'  => now(),
                'notes'          => $validated['notes'] ?? null,
            ]);

            $lockedClaim->update(['status' => 'completed']);
            $lockedClaim->item->update(['status' => 'returned']);

            return true;
        });

        if (!$processed) {
            return back()->with('error', 'Kode serah terima tidak valid atau sudah pernah diproses.');
        }

        return redirect()->route('admin.handovers')
            ->with('success', 'Serah terima berhasil dicatat! Status barang kini resmi: Dikembalikan.');
    }
}
