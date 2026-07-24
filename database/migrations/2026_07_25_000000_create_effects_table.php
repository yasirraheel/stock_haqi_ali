<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEffectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('effects')) {
            Schema::create('effects', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('effect_url');
                $table->string('category')->default('General')->nullable();
                $table->decimal('license_price', 8, 2)->default(0.00)->nullable();
                $table->boolean('is_premium')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('downloads_count')->default(0);
                $table->integer('views_count')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('effects');
    }
}
