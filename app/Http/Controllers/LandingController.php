<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'totalItems' => Item::publiclyVisible()->count(),
            'returnedItems' => Item::where('status', 'returned')->count(),
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
