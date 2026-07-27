<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEffectIdToGoogleDriveFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_drive_files', function (Blueprint $table) {
            $table->unsignedBigInteger('effect_id')->nullable()->after('url')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_drive_files', function (Blueprint $table) {
            $table->dropColumn('effect_id');
        });
    }
}
