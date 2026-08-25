<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScannedFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('scanned_folders')) {
            Schema::create('scanned_folders', function (Blueprint $table) {
                $table->id();
                $table->string('type')->default('effect')->index();
                $table->string('folder_id')->index();
                $table->string('folder_name')->nullable();
                $table->text('folder_url')->nullable();
                $table->unsignedInteger('total_files')->default(0);
                $table->unsignedInteger('imported_files')->default(0);
                $table->unsignedInteger('pending_files')->default(0);
                $table->unsignedInteger('blocked_files')->default(0);
                $table->timestamp('last_scanned_at')->nullable();
                $table->timestamps();

                $table->unique(['type', 'folder_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scanned_folders');
    }
}