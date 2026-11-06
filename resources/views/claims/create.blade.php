@extends('layouts.app')

@section('title', 'Ajukan Klaim: ' . $item->title)

@section('content')
<div class="container" style="max-width: 640px; margin: 36px auto; padding: 0 16px;">
    <a href="{{ route('items.show', $item) }}" class="btn btn--ghost" style="margin-bottom: 20px; display: inline-flex; align-items: center; gap: 8px;">
        <x-icon name="arrow-left" :size="20" /> Batal & Kembali
    </a>

    <div class="card" style="padding: 32px; border: 2px solid #111; border-radius: 20px; box-shadow: 6px 6px 0 #111; background: #fff;">
        <div style="display: flex; gap: 16px; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; margin-bottom: 24px;">
            <div style="width: 70px; height: 70px; border-radius: 10px; overflow: hidden; border: 1.5px solid #111; flex-shrink: 0;">
                <img src="{{ $item->photo_url }}" alt="{{ $item->title }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div>
                <x-badge icon="search-check" color="#a3e636">Barang Temuan</x-badge>
                <h2 style="font-size: 18px; font-weight: 800; margin: 4px 0; color: #111;">{{ $item->title }}</h2>
                <span style="font-size: 13px; color: #6b7280;">Ditemukan di: {{ $item->location }}</span>
            </div>
        </div>

        @if ($errors->any())
            <div style="margin-bottom: 20px; padding: 12px; background: #fee2e2; border: 1.5px solid #dc2626; border-radius: 10px; font-size: 14px; color: #991b1b;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('claims.store', $item) }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf

            {{-- Kotak Pertanyaan Keamanan --}}
            <div style="padding: 18px; background: #f0fdf4; border: 1.5px solid #16a34a; border-radius: 14px;">
                <span style="display: block; font-size: 12px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 0.5px;">Pertanyaan Keamanan dari Penemu:</span>
                <p style="margin: 8px 0 14px; font-size: 16px; font-weight: 700; color: #111;">
                    "{{ $item->security_question ?: 'Sebutkan ciri khusus atau isi dari barang ini yang hanya diketahui pemilik.' }}"
                </p>

                <label for="security_answer" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px; color: #111;">Jawaban Kamu *</label>
                <textarea id="security_answer" name="security_answer" rows="3" required
                          class="input" style="width: 100%; box-sizing: border-box; background: #fff;"
                          placeholder="Jawab dengan jujur dan sedetail mungkin...">{{ old('security_answer') }}</textarea>
            </div>

            <div>
                <label for="proof_image" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Bukti Pendukung Kepemilikan (Opsional tapi Dianjurkan)</label>
                <input type="file" id="proof_image" name="proof_image" class="input" style="width: 100%; box-sizing: border-box;">
                <small style="color: #6b7280; font-size: 12px; display: block; margin-top: 4px;">
                    Dapat berupa foto lama bersama barang, nota pembelian, nomor serial, dus/box, atau kartu identitas pendukung.
                </small>
            </div>

            <div style="padding: 12px; background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; font-size: 13px; color: #92400e;">
                ⚠️ <strong>Penting:</strong> Percobaan klaim palsu dapat dikenakan sanksi tata tertib kampus. Data klaim dan akunmu akan diverifikasi oleh petugas satpam.
            </div>

            <button type="submit" class="btn btn--primary" style="padding: 14px; font-size: 16px; margin-top: 8px;">
                Kirim Pengajuan Klaim
            </button>
        </form>
    </div>
</div>
@endsection

