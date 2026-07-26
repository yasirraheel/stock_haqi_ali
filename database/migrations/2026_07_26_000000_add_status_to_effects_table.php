<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('effects', function (Blueprint $table) {
            $table->string('status')->default('ready')->after('is_active');
            $table->string('processed_url')->nullable()->after('status');
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
            $table->dropColumn(['status', 'processed_url']);
        });
    }
};
