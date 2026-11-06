@extends('layouts.app')

@section('title', 'Ajukan Klaim | Lost & Found')

@section('content')
<div class="page-wrap page-wrap--narrow">
    <a href="{{ route('items.show', $item) }}" class="btn btn--ghost" style="margin-bottom:20px; display:inline-flex; align-items:center; gap:8px; padding-left:0;">
        <x-icon name="arrow-left" :size="18" /> Batal &amp; Kembali
    </a>

    <div class="card" style="padding:28px;">
        {{-- Item preview --}}
        <div style="display:flex; gap:16px; align-items:center; padding-bottom:20px; border-bottom:1.5px solid #f1f5f9; margin-bottom:24px; flex-wrap:wrap;">
            <div class="history-thumb" style="flex-shrink:0;">
                <img src="{{ $item->photo_url }}" alt="{{ $item->title }}">
            </div>
            <div>
                <x-badge icon="search-check" color="#a3e636">Barang Temuan</x-badge>
                <h2 style="font-size:18px; font-weight:800; margin:6px 0 4px; color:#111;">{{ $item->title }}</h2>
                <span style="font-size:13px; color:var(--muted);">Ditemukan di: {{ $item->location }}</span>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert--error">
                <x-icon name="circle-x" :size="18" />
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('claims.store', $item) }}" enctype="multipart/form-data"
              style="display:flex; flex-direction:column; gap:20px;">
            @csrf

            {{-- Security Question --}}
            <div class="card panel panel--mint">
                <span style="display:block; font-size:11px; font-weight:800; color:#166534; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:8px;">
                    Pertanyaan Keamanan dari Penemu
                </span>
                <p style="margin:0 0 14px; font-size:16px; font-weight:700; color:#111; line-height:1.4;">
                    "{{ $item->security_question ?: 'Sebutkan ciri khusus atau isi dari barang ini yang hanya diketahui pemilik.' }}"
                </p>
                <div class="field">
                    <label class="label label--required">Jawaban Kamu</label>
                    <textarea name="security_answer" rows="3" required class="input" style="background:var(--white);"
                              placeholder="Jawab dengan jujur dan sedetail mungkin...">{{ old('security_answer') }}</textarea>
                </div>
            </div>

            {{-- Bukti --}}
            <div class="field">
                <label class="label">Bukti Pendukung Kepemilikan <span style="color:var(--muted); font-weight:500;">(Opsional tapi Dianjurkan)</span></label>
                <input type="file" name="proof_image" class="input">
                <span class="hint">Bisa berupa foto lama bersama barang, nota pembelian, nomor serial, atau kartu identitas pendukung.</span>
            </div>

            {{-- Warning --}}
            <div class="alert alert--warning" style="margin-bottom:0;">
                <x-icon name="triangle-alert" :size="18" />
                <span><strong>Peringatan:</strong> Percobaan klaim palsu dapat dikenakan sanksi tata tertib kampus. Data klaim dan akunmu akan diverifikasi oleh petugas satpam.</span>
            </div>

            <button type="submit" class="btn btn--primary btn--block" style="padding:14px; font-size:15px;">
                <x-icon name="handshake" :size="18" /> Kirim Pengajuan Klaim
            </button>
        </form>
    </div>
</div>
@endsection
