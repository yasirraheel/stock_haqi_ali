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
        Schema::table('photos', function (Blueprint $table) {
            // GPS altitude
            $table->string('gps_altitude')->nullable();

            // Additional EXIF data
            $table->string('subject')->nullable();
            $table->string('software')->nullable();
            $table->string('exposure_mode')->nullable();
            $table->string('metering_mode')->nullable();
            $table->string('scene_capture_type')->nullable();
            $table->string('contrast')->nullable();
            $table->string('saturation')->nullable();
            $table->string('sharpness')->nullable();

            // Lens information
            $table->string('lens_specification')->nullable();
            $table->string('lens_serial_number')->nullable();

            // Focus information
            $table->string('focus_distance')->nullable();
            $table->string('focus_mode')->nullable();

            // Image quality settings
            $table->string('image_quality')->nullable();
            $table->string('white_balance_mode')->nullable();
            $table->string('subject_distance_range')->nullable();

            // Digital zoom and focal length
            $table->string('digital_zoom_ratio')->nullable();
            $table->string('focal_length_35mm')->nullable();

            // Scene and rendering
            $table->string('scene_type')->nullable();
            $table->string('custom_rendered')->nullable();

            // Exposure and light
            $table->string('exposure_program')->nullable();
            $table->string('light_source')->nullable();
            $table->string('gain_control')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn([
                'gps_altitude', 'subject', 'software', 'exposure_mode', 'metering_mode',
                'scene_capture_type', 'contrast', 'saturation', 'sharpness',
                'lens_specification', 'lens_serial_number', 'focus_distance', 'focus_mode',
                'image_quality', 'white_balance_mode', 'subject_distance_range',
                'digital_zoom_ratio', 'focal_length_35mm', 'scene_type', 'custom_rendered',
                'exposure_program', 'light_source', 'gain_control'
            ]);
        });
    }
};
