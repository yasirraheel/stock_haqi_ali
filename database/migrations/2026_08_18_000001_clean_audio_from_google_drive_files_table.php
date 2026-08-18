<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CleanAudioFromGoogleDriveFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Delete audio records from google_drive_files (Effects table) so Effects list contains ONLY video effects
        DB::table('google_drive_files')
            ->where('mime_type', 'LIKE', 'audio/%')
            ->orWhere('name', 'LIKE', '%.mp3')
            ->orWhere('name', 'LIKE', '%.wav')
            ->orWhere('name', 'LIKE', '%.flac')
            ->orWhere('name', 'LIKE', '%.aac')
            ->orWhere('name', 'LIKE', '%.ogg')
            ->orWhere('name', 'LIKE', '%.m4a')
            ->orWhere('name', 'LIKE', '%.wma')
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
