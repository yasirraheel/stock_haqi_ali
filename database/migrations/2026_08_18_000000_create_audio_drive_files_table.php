<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudioDriveFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio_drive_files', function (Blueprint $table) {
            $table->id();
            $table->string('folder_id')->index();
            $table->string('file_id')->unique();
            $table->string('name');
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->default(0);
            $table->text('url')->nullable();
            $table->unsignedBigInteger('audio_id')->nullable()->index();
            $table->string('status', 20)->default('scanned')->index(); // scanned, imported, blocked
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audio_drive_files');
    }
}
