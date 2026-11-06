<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CampusManagementController extends Controller
{
    public function index(): View
    {
        return view('platform.campuses.index', [
            'campuses' => Campus::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('campuses', 'slug'),
            ],
        ], [
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
            'slug.unique' => 'Alamat portal ini sudah digunakan.',
        ]);

        Campus::create([
            'name' => $validated['name'],
            'slug' => Str::lower($validated['slug']),
            'is_active' => true,
        ]);

        return redirect()->route('platform.campuses.index', ['campus' => null])
            ->with('success', 'Portal kampus berhasil dibuat.');
    }

    public function updateStatus(Request $request, Campus $managedCampus): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $isActive = $request->boolean('is_active');

        if ($managedCampus->slug === config('campus.default_slug') && ! $isActive) {
            return back()->with('error', 'Portal kampus utama tidak dapat dinonaktifkan.');
        }

        $managedCampus->update(['is_active' => $isActive]);

        return redirect()->route('platform.campuses.index', ['campus' => null])
            ->with('success', $isActive ? 'Portal kampus diaktifkan.' : 'Portal kampus dinonaktifkan.');
    }
}
