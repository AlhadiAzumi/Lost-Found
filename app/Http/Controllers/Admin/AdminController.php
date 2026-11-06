<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Handover;
use App\Models\Item;
use Illuminate\Http\Request;
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
            'status' => ['required', 'in:approved,rejected,pending'],
        ]);

        $item->update(['status' => $validated['status']]);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function claims()
    {
        $claims = Claim::with('item', 'user', 'verifier')->orderByDesc('id')->paginate(10);
        return view('admin.claims', compact('claims'));
    }

    public function verifyClaim(Request $request, Claim $claim)
    {
        $action = $request->input('action'); // approve | reject

        if ($action === 'approve') {
            // Buat handover code unik (Format: LFP-XXXX-XXXX)
            $code = 'LFP-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));

            $claim->update([
                'status'        => 'approved',
                'handover_code' => $code,
                'verified_by'   => auth()->id(),
                'admin_note'    => $request->input('admin_note'),
            ]);

            return back()->with('success', "Klaim berhasil disetujui! Kode Serah Terima diterbitkan: $code");
        } else {
            $claim->update([
                'status'      => 'rejected',
                'verified_by' => auth()->id(),
                'admin_note'  => $request->input('admin_note', 'Bukti atau jawaban tidak sesuai.'),
            ]);

            // Kembalikan status item ke approved jika tidak ada klaim pending lain
            $claim->item->update(['status' => 'approved']);

            return back()->with('success', 'Klaim telah ditolak.');
        }
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
        if ($claim->status !== 'approved' || $claim->handover()->exists()) {
            return back()->with('error', 'Kode serah terima tidak valid atau sudah pernah diproses.');
        }

        Handover::create([
            'claim_id'       => $claim->id,
            'item_id'        => $claim->item_id,
            'claimed_by'     => $claim->user_id,
            'handed_over_by' => auth()->id(),
            'handover_date'  => now(),
            'notes'          => $request->input('notes'),
        ]);

        $claim->update(['status' => 'completed']);
        $claim->item->update(['status' => 'returned']);

        return redirect()->route('admin.handovers')
            ->with('success', 'Serah terima berhasil dicatat! Status barang kini resmi: Dikembalikan.');
    }
}

