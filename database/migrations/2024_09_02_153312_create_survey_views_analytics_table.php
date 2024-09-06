<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyViewsAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_views_analytics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('survey_id');
            $table->unsignedBigInteger('folder_id')->nullable(); // optional
            $table->unsignedBigInteger('user_id')->nullable(); // optional
            $table->string('session_id')->nullable(); // optional, tracks user session
            $table->string('ip_address', 45)->nullable(); // optional
            $table->json('geolocation')->nullable(); // optional, stores geolocation data
            $table->string('device_type', 50)->nullable(); // optional
            $table->string('browser_type', 50)->nullable(); // optional
            $table->string('device_os', 50)->nullable(); // optional, stores the device operating system
            $table->string('referral_source', 255)->nullable(); // optional, tracks referral source
            $table->timestamp('viewed_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('response_started_at')->nullable(); // new column to track when the user starts the survey
            $table->timestamp('response_completed_at')->nullable(); // new column to track when the user completes the survey
            $table->unsignedInteger('response_duration')->nullable(); // stores the time taken to complete the survey
            $table->boolean('is_completed')->default(false); // new column to indicate if the survey was completed
            $table->boolean('is_returning_user')->default(false); // indicates if the user is returning
            $table->unsignedInteger('survey_version')->nullable(); // optional, tracks the version of the survey
            $table->decimal('survey_score', 5, 2)->nullable(); // optional, stores survey score if applicable
            $table->timestamps();

            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('set null'); // if folders table exists
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null'); // if users table exists
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_views_analytics');
    }
}
