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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_name');
            $table->string('image_path');
            $table->string('image_url')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('license_price', 8, 2);
            $table->string('tags')->nullable();
            $table->string('category')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Image metadata fields
            $table->string('file_name')->nullable();
            $table->string('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('resolution')->nullable();
            $table->string('color_space')->nullable();
            $table->integer('bit_depth')->nullable();
            $table->boolean('has_transparency')->default(false);

            // EXIF data fields
            $table->string('camera_make')->nullable();
            $table->string('camera_model')->nullable();
            $table->string('lens_model')->nullable();
            $table->string('focal_length')->nullable();
            $table->string('aperture')->nullable();
            $table->string('shutter_speed')->nullable();
            $table->integer('iso')->nullable();
            $table->string('flash')->nullable();
            $table->string('white_balance')->nullable();
            $table->timestamp('date_taken')->nullable();
            $table->string('gps_latitude')->nullable();
            $table->string('gps_longitude')->nullable();
            $table->string('gps_location')->nullable();

            // Additional fields
            $table->string('orientation')->nullable();
            $table->string('copyright')->nullable();
            $table->string('artist')->nullable();
            $table->text('keywords')->nullable();
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->unsignedBigInteger('added_by')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['status', 'created_at']);
            $table->index('category');
            $table->index('added_by');
            $table->fullText(['title', 'description', 'tags', 'keywords']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
