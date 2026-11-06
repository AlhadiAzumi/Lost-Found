<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q'        => trim((string) $request->query('q', '')),
            'type'     => in_array($request->query('type'), ['lost', 'found', 'hilang', 'ditemukan'], true) ? $request->query('type') : null,
            'category' => $request->query('category'),
            'location' => $request->query('location'),
            'from'     => $request->query('from'),
            'to'       => $request->query('to'),
            'sort'     => $request->query('sort') === 'terlama' ? 'terlama' : 'terbaru',
        ];

        // Normalisasi filter type ke format DB jika perlu
        $dbType = null;
        if ($filters['type'] === 'hilang' || $filters['type'] === 'lost') {
            $dbType = ['lost', 'hilang'];
        } elseif ($filters['type'] === 'ditemukan' || $filters['type'] === 'found') {
            $dbType = ['found', 'ditemukan'];
        }

        $items = Item::query()
            ->with('category')
            ->publiclyVisible()
            ->when($filters['q'], function ($q, $v) {
                $q->where(function ($w) use ($v) {
                    $search = '%' . mb_strtolower($v) . '%';
                    $w->whereRaw('LOWER(title) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(description) LIKE ?', [$search]);
                });
            })
            ->when($dbType, fn ($q, $v) => $q->whereIn('type', $v))
            ->when($filters['category'], fn ($q, $v) => $q->where('category_id', $v))
            ->when($filters['location'], fn ($q, $v) => $q->where('location', $v))
            ->when($filters['from'], fn ($q, $v) => $q->whereDate('date_event', '>=', $v))
            ->when($filters['to'], fn ($q, $v) => $q->whereDate('date_event', '<=', $v))
            ->orderBy('date_event', $filters['sort'] === 'terlama' ? 'asc' : 'desc')
            ->orderByDesc('id')
            ->paginate(6)
            ->withQueryString();

        return view('catalog.index', [
            'items'      => $items,
            'filters'    => $filters,
            'categories' => Category::orderBy('name')->get(),
            'locations'  => Item::publiclyVisible()->distinct()->orderBy('location')->pluck('location'),
        ]);
    }

    public function show(Item $item)
    {
        abort_unless(
            in_array($item->status, ['approved', 'claim_in_progress', 'returned'], true)
            || auth()->id() === $item->user_id
            || auth()->user()?->role === 'admin'
            || auth()->user()?->role === 'satpam',
            404
        );

        $item->load(['category', 'user']);
        return view('catalog.show', compact('item'));
    }
}
