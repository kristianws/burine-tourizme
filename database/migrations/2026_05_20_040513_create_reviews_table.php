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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Turis reviewer
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating'); // Range 1-5, validasi dilakukan di Controller
            $table->text('comment');
            $table->text('reply')->nullable(); // Balasan dari Mitra
            $table->boolean('is_hidden')->default(false); // Moderasi Admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
