<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('thumbnails', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('file_id'); // Column to store file name
            $table->string('video_image_thumb'); // Column to store the file path
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thumbnails');
    }
};
