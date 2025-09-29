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
        Schema::create('audio', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('audio_path');
            $table->string('audio_url')->nullable();
            $table->decimal('license_price', 8, 2)->nullable();
            $table->string('duration')->nullable();
            $table->string('file_size')->nullable();
            $table->string('format')->nullable();
            $table->integer('bitrate')->nullable();
            $table->integer('sample_rate')->nullable();
            $table->string('genre')->nullable();
            $table->string('mood')->nullable();
            $table->string('tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('downloads_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio');
    }
};
