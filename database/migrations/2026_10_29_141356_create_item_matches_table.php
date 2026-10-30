<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('item_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('found_item_id')->constrained('items')->onDelete('cascade');
            $table->float('match_score');
            $table->enum('status', ['suggested', 'confirmed', 'dismissed'])->default('suggested');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('item_matches'); }
};