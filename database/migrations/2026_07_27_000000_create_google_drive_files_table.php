<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleDriveFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_drive_files', function (Blueprint $table) {
            $table->id();
            $table->string('folder_id')->index();
            $table->string('file_id')->unique();
            $table->string('name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->text('url')->nullable();
            $table->string('status')->default('scanned');
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
        Schema::dropIfExists('google_drive_files');
    }
}
