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
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('full_name');
      $table->string('user_name')->unique();
      $table->string('email')->unique();
      $table->string('password');
      $table->string('profile_picture')->nullable();
      $table->enum(
        'role',
        ['admin', 'bisnis_owner', 'tourist']
      );
      $table->enum(
        'status',
        ['active', 'suspended']
      );
      $table->timestamp('suspended_at')->nullable();
      $table->text('suspended_reason')->nullable();
      $table->timestamp('email_verified_at')->nullable();
      $table->rememberToken();
      $table->timestamps();
    });

    Schema::create('password_reset_tokens', function (Blueprint $table) {
      $table->string('email')->primary();
      $table->string('token');
      $table->timestamp('created_at')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('users');
    Schema::dropIfExists('password_reset_tokens');
  }
};
