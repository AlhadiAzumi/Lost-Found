<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Item;
use Illuminate\Http\Request;

class UserHistoryController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'reports');

        $reports = Item::where('user_id', auth()->id())
            ->with('category')
            ->orderByDesc('id')
            ->get();

        $claims = Claim::where('user_id', auth()->id())
            ->with(['item.category'])
            ->orderByDesc('id')
            ->get();

        return view('my.history', compact('reports', 'claims', 'tab'));
    }
}

