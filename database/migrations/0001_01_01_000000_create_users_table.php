<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
       Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('nim_nip')->unique()->nullable(); // NIM untuk Mahasiswa, NIP untuk Satpam/Admin
    $table->string('phone_number')->nullable();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->enum('role', ['mahasiswa', 'satpam', 'admin'])->default('mahasiswa'); // Peran pengguna
    $table->string('avatar')->nullable();
    $table->rememberToken();
    $table->timestamps();
    });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};