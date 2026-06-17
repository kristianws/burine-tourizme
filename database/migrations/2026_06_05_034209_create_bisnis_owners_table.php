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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nik')->unique();
            $table->string('ktp_photo');
            $table->enum(
                'status',
                [
                    'pending',
                    'approved',
                    'rejected'
                ]
            )->default('pending');
            $table->boolean('verification_status')->default(false);
            $table->dateTime('verification_at', precision: 0)->nullable();
            $table->string('verification_notes')->nullable();
            $table->string('nib')->unique()->nullable();
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
