<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bisnis_owner_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('gmaps');
            $table->string('location');
            $table->decimal('price', 10, 2);
            $table->text('description');
            $table->time('open_time');
            $table->time('close_time');
            $table->string('thumbnail')->nullable();
            $table->enum(
                'status',
                [
                    'pending',
                    'approved',
                    'rejected',
                    'deleted'
                ]
            )->default('pending');
            $table->text('notes')->nullable();
            $table->dateTime('deleted_at', precision: 0)->nullable();
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
