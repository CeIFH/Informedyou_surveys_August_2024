<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompletionMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('completion_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->nullable()->constrained()->onDelete('set null');  // Allow survey_id to be set to NULL
            $table->string('title');
            $table->text('content');
            $table->text('condition')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('completion_messages');
    }
}