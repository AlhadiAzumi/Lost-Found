<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $stats = Item::publiclyVisible()
            ->selectRaw('count(*) as total_items')
            ->selectRaw("sum(case when status = 'returned' then 1 else 0 end) as returned_items")
            ->first();

        return view('home', [
            'totalItems' => (int) ($stats->total_items ?? 0),
            'returnedItems' => (int) ($stats->returned_items ?? 0),
            'recentItems' => Item::publiclyVisible()
                ->with('category')
                ->orderByDesc('date_event')
                ->orderByDesc('id')
                ->limit(3)
                ->get(),
            'categories' => Category::withCount([
                'items' => fn ($query) => $query->publiclyVisible(),
            ])->orderBy('name')->limit(6)->get(),
        ]);
    }
}
