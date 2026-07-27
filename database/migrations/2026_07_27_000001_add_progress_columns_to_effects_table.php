<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgressColumnsToEffectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('effects', function (Blueprint $table) {
            if (!Schema::hasColumn('effects', 'process_percent')) {
                $table->integer('process_percent')->default(0)->after('status');
            }
            if (!Schema::hasColumn('effects', 'process_step')) {
                $table->string('process_step')->nullable()->after('process_percent');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('effects', function (Blueprint $table) {
            $table->dropColumn(['process_percent', 'process_step']);
        });
    }
}
