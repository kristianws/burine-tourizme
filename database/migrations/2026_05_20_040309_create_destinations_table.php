<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Mitra Pemilik
            $table->string('name')->index(); // Index agar pencarian nama wisata lebih cepat
            $table->text('description');
            $table->string('location'); 
            $table->string('google_maps_url')->nullable(); 
            $table->string('image_url')->nullable(); 
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
