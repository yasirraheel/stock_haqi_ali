<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_tv_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('live_tv_id')->nullable()->index();
            $table->string('channel_name');
            $table->text('channel_description')->nullable();
            $table->string('channel_access', 20)->default('Free');
            $table->unsignedBigInteger('channel_cat_id')->nullable()->index();
            $table->string('channel_url_type', 30)->default('HLS');
            $table->string('channel_url', 500);
            $table->string('channel_url2', 500)->nullable();
            $table->string('channel_url3', 500)->nullable();
            $table->string('channel_thumb', 255);
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->string('seo_keyword', 500)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable()->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_tv_submissions');
    }
};
