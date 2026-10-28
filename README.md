# 🔍 Campus Lost & Found Portal

Sistem Informasi Pusat Kehilangan dan Penemuan Barang (Lost & Found) berbasis web untuk lingkungan kampus. Aplikasi ini memfasilitasi pelaporan barang hilang, pendataan barang temuan, dan proses verifikasi klaim kepemilikan secara aman dan terstruktur.

Proyek ini dikembangkan untuk memenuhi tugas mata kuliah **Rekayasa Perangkat Lunak**.

## ✨ Fitur Utama

### 👥 Modul Pengguna (Mahasiswa/Umum)
- **Katalog Interaktif:** Pencarian barang hilang dan ditemukan dengan filter kategori, lokasi, dan tanggal.
- **Lapor Barang Hilang (Lost):** Formulir pelaporan detail barang yang hilang beserta unggahan foto.
- **Lapor Barang Temuan (Found):** Formulir pendataan barang temuan yang dilengkapi dengan *Pertanyaan Keamanan (Security Question)* untuk mencegah klaim palsu.
- **Pengajuan Klaim:** Sistem pengajuan hak milik dengan menjawab pertanyaan verifikasi dan mengunggah bukti kepemilikan.

### 🛡️ Modul Admin (Satpam/Petugas Pos)
- **Dashboard Analitik:** Ringkasan statistik persentase barang kembali, total laporan, dan klaim tertunda.
- **Moderasi Laporan:** Persetujuan atau penolakan laporan baru untuk mencegah spam.
- **Verifikasi Klaim:** Validasi jawaban keamanan dari pengaju klaim.
- **Manajemen Serah Terima:** Sistem verifikasi kode unik saat serah terima barang di pos satpam.

## 🛠️ Tech Stack
- **Framework:** Laravel
- **Database:** Supabase (PostgreSQL)
- **Architecture:** MVC (Model-View-Controller)
- **Access Control:** Laravel Middleware

## 👨‍💻 Susunan Tim (Kelompok 04 - TI 2B)
- **Project Manager:** Alhadi Azumi
- **UI/UX Designer:** Rahmat Naufal, Fatia Azahara
- **Frontend Developer:** Ayla Azzura, Fadhlul Hadi
- **Backend & Database Developer:** Abdul Karim, Riska Ayu
- **Quality Assurance (QA):** All Members

## 🚀 Cara Instalasi (Pengembangan Lokal)

1. Clone repositori ini:
   ```bash
   git clone https://github.com/AlhadiAzumi/Lost-Found.git
   ```
2. Masuk ke direktori proyek:
   ```bash
   cd Lost-Found
   ```
3. Instal dependensi Composer:
   ```bash
   composer install
   ```
4. Salin berkas `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
5. Sesuaikan konfigurasi database di berkas `.env` dengan kredensial Supabase kamu:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com # Sesuaikan dengan host Supabase
   DB_PORT=6543
   DB_DATABASE=postgres
   DB_USERNAME=postgres.xxxxxxx
   DB_PASSWORD=password-supabase-kamu
   ```
6. Generate *application key*:
   ```bash
   php artisan key:generate
   ```
7. Jalankan migrasi database ke Supabase:
   ```bash
   php artisan migrate
   ```
8. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
Aplikasi dapat diakses melalui `http://localhost:8000`.
