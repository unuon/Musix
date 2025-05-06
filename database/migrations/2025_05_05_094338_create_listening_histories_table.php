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
        Schema::create('listening_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('track_id');
            $table->string('title');
            $table->string('artist')->nullable();
            $table->string('album')->nullable();
            $table->string('duration')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamp('played_at');
            $table->timestamps();
            
            // Index for faster querying history by user
            $table->index('user_id');
            $table->index('played_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listening_history');
    }
};