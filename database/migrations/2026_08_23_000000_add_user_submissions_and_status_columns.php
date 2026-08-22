<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserSubmissionsAndStatusColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Audio table: add added_by and status
        if (Schema::hasTable('audio')) {
            Schema::table('audio', function (Blueprint $table) {
                if (!Schema::hasColumn('audio', 'added_by')) {
                    $table->unsignedBigInteger('added_by')->nullable()->after('views_count')->index();
                }
                if (!Schema::hasColumn('audio', 'status')) {
                    $table->string('status', 20)->default('active')->after('is_active')->index();
                }
            });
        }

        // 2. Effects table: add added_by and status
        if (Schema::hasTable('effects')) {
            Schema::table('effects', function (Blueprint $table) {
                if (!Schema::hasColumn('effects', 'added_by')) {
                    $table->unsignedBigInteger('added_by')->nullable()->after('views_count')->index();
                }
                if (!Schema::hasColumn('effects', 'status')) {
                    $table->string('status', 20)->default('active')->after('is_active')->index();
                }
            });
        }

        // 3. Film Stock drive files table: add added_by
        if (Schema::hasTable('film_stock_drive_files')) {
            Schema::table('film_stock_drive_files', function (Blueprint $table) {
                if (!Schema::hasColumn('film_stock_drive_files', 'added_by')) {
                    $table->unsignedBigInteger('added_by')->nullable()->after('film_stock_id')->index();
                }
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
        if (Schema::hasTable('audio')) {
            Schema::table('audio', function (Blueprint $table) {
                if (Schema::hasColumn('audio', 'added_by')) {
                    $table->dropColumn('added_by');
                }
                if (Schema::hasColumn('audio', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }

        if (Schema::hasTable('effects')) {
            Schema::table('effects', function (Blueprint $table) {
                if (Schema::hasColumn('effects', 'added_by')) {
                    $table->dropColumn('added_by');
                }
                if (Schema::hasColumn('effects', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }

        if (Schema::hasTable('film_stock_drive_files')) {
            Schema::table('film_stock_drive_files', function (Blueprint $table) {
                if (Schema::hasColumn('film_stock_drive_files', 'added_by')) {
                    $table->dropColumn('added_by');
                }
            });
        }
    }
}
