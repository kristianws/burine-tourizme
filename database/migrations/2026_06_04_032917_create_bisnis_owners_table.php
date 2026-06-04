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
    Schema::create('bisnis_owners', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

      $table->string('nik')->unique();
      $table->string('ktp_image');
      $table->boolean('is_verified')
        ->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('bisnis_owners');
  }
};
