<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveysTable extends Migration
{
    public function up()
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->json('content');
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('set null');

            // Adding the new columns
            $table->string('logo', 191)->nullable();
            $table->string('bccemail', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('ccemail', 191)->nullable();
            $table->text('success_msg')->nullable();
            $table->text('thanks_msg')->nullable();
            $table->integer('is_active')->default(1);
            $table->unsignedBigInteger('allow_share_section')->nullable();
            $table->unsignedBigInteger('allow_comments')->nullable();
            $table->string('theme', 191)->default('theme1');
            $table->string('theme_color', 191)->default('theme-2');
            $table->string('theme_background_image', 191)->nullable();
            $table->string('set_end_date', 191)->default('0');
            $table->dateTime('set_end_date_time')->nullable();
            $table->tinyInteger('limit_status')->default(0)->comment('1-On 0-off');
            $table->string('limit', 255)->nullable();
            $table->tinyInteger('form_fill_edit_lock')->default(1)->comment('1-On ,0-off');
            $table->tinyInteger('conditional_rule')->default(1)->comment('1 - Enable / 0 - Disable');
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surveys');
    }
}

