<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('claimed_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('handed_over_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('handover_date');
            $table->string('proof_photo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { 
        Schema::dropIfExists('handovers'); 
    }
};