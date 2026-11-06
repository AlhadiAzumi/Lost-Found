<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemReportController extends Controller
{
    public function createLost()
    {
        $categories = Category::orderBy('name')->get();
        return view('reports.create-lost', compact('categories'));
    }

    public function createFound()
    {
        $categories = Category::orderBy('name')->get();
        return view('reports.create-found', compact('categories'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type');
        
        $rules = [
            'type'              => ['required', 'in:lost,found'],
            'title'             => ['required', 'string', 'max:255'],
            'category_id'       => ['required', 'exists:categories,id'],
            'location'          => ['required', 'string', 'max:255'],
            'date_event'        => ['required', 'date'],
            'description'       => ['required', 'string', 'max:2000'],
            'image'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];

        if ($type === 'found') {
            $rules['security_question'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules, [
            'title.required'             => 'Nama barang wajib diisi.',
            'category_id.required'       => 'Kategori barang wajib dipilih.',
            'location.required'          => 'Lokasi kejadian wajib diisi.',
            'date_event.required'        => 'Tanggal kejadian wajib diisi.',
            'description.required'       => 'Deskripsi barang wajib diisi.',
            'security_question.required' => 'Pertanyaan keamanan wajib diisi untuk barang temuan.',
            'image.max'                  => 'Ukuran foto maksimal 2 MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        $item = Item::create([
            'user_id'           => auth()->id(),
            'category_id'       => $validated['category_id'],
            'type'              => $type,
            'title'             => $validated['title'],
            'description'       => !empty(trim($validated['description'] ?? '')) ? $validated['description'] : '-',
            'location'          => $validated['location'],
            'date_event'        => $validated['date_event'],
            'image'             => $imagePath,
            'security_question' => $validated['security_question'] ?? null,
            'status'            => 'approved', // Langsung approved untuk kenyamanan testing v1
        ]);

        return redirect()->route('items.show', $item)
            ->with('success', 'Laporan berhasil dibuat dan ditambahkan ke katalog!');
    }
}

