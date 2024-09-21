<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            // Primary key
            $table->bigIncrements('id')->comment('Primary key');

            // Foreign keys for survey and folder
            $table->unsignedBigInteger('survey_id')->comment('Foreign key referencing the survey');
            $table->unsignedBigInteger('folder_id')->nullable()->comment('Optional foreign key referencing the folder');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Optional foreign key referencing the user');

            // Company information (new fields)
            $table->unsignedBigInteger('company_id')->comment('Foreign key referencing the company');
            // Remove the company_name column
            // $table->string('company_name')->comment('Name of the company');

            // Additional requested fields
            $table->longText('responses')->comment('Stores user responses in JSON format');
            $table->string('signature', 255)->nullable()->comment('Stores digital signature for the survey (optional)');

            // UUID for each completion
            $table->uuid('completion_uuid')->unique()->comment('Unique identifier for each survey completion');

            // User session and device information
            $table->string('session_id')->nullable()->comment('Session ID to track user session');
            $table->string('ip_address', 45)->nullable()->comment('IP address of the user');
            $table->json('geolocation')->nullable()->comment('Stores geolocation data (optional)');
            $table->string('device_type', 50)->nullable()->comment('Type of device used by the respondent');
            $table->string('browser_type', 50)->nullable()->comment('Browser type used by the respondent');
            $table->string('device_os', 50)->nullable()->comment('Operating system of the device');
            $table->string('referral_source', 255)->nullable()->comment('Tracks the source of referral to the survey');

            // View count
            $table->unsignedInteger('view_count')->default(0)->comment('Number of times the survey was viewed');

            // Survey timing and completion data
            $table->timestamp('viewed_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Timestamp when the survey was first viewed');
            $table->timestamp('response_started_at')->nullable()->comment('Timestamp when the user started the survey');
            $table->timestamp('response_completed_at')->nullable()->comment('Timestamp when the user completed the survey');
            $table->unsignedInteger('response_duration')->nullable()->comment('Time taken to complete the survey in seconds');
            $table->boolean('is_completed')->default(false)->comment('Indicates if the survey was fully completed');
            $table->boolean('is_returning_user')->default(false)->comment('Indicates if the user has taken the survey before');

            // Survey versioning and scoring
            $table->unsignedInteger('survey_version')->nullable()->comment('Version number of the survey');
            $table->decimal('survey_score', 5, 2)->nullable()->comment('Optional score of the survey if applicable');

            // Additional analytic fields
            $table->boolean('is_abandoned')->default(false)->comment('Indicates if the survey was abandoned');
            $table->json('question_timings')->nullable()->comment('Stores the time spent on each question');
            $table->string('completion_path')->nullable()->comment('Tracks how the user completed the survey');
            $table->string('device_orientation', 10)->nullable()->comment('Device orientation (portrait or landscape)');
            $table->json('interaction_metadata')->nullable()->comment('Additional metadata like scroll depth, clicks, etc.');
            $table->text('completion_feedback')->nullable()->comment('Feedback from the user after survey completion');
            $table->string('platform_type', 50)->nullable()->comment('Platform used (e.g., mobile app, web browser)');
            $table->string('timezone', 50)->nullable()->comment('Timezone of the user');
            $table->unsignedInteger('pre_survey_duration')->nullable()->comment('Time spent on the page before starting the survey');
            $table->unsignedInteger('response_change_count')->default(0)->comment('Number of times the user changed their responses');
            $table->json('partial_responses')->nullable()->comment('Stores partial responses if survey allows saving');
            $table->boolean('device_orientation_changed')->default(false)->comment('Indicates if the device orientation was changed during the survey');
            $table->string('network_connection', 50)->nullable()->comment('Type of network connection (e.g., WiFi, cellular)');
            $table->string('referrer_url', 255)->nullable()->comment('URL referring the user to the survey');
            $table->json('completion_behavior')->nullable()->comment('Tracks behavior like skipped questions or returning to previous questions');
            $table->json('custom_metadata')->nullable()->comment('Custom metadata for flexible tracking');
            $table->unsignedInteger('time_to_first_interaction')->nullable()->comment('Time to first interaction with the survey');
            $table->string('display_mode', 50)->nullable()->comment('Display mode (fullscreen, embedded, popup, etc.)');
            $table->json('browser_plugins')->nullable()->comment('Stores information on any browser plugins/extensions used');

            // Admin-focused fields
            $table->unsignedInteger('completion_count')->default(0)->comment('Count of completed surveys');
            $table->unsignedInteger('abandoned_count')->default(0)->comment('Count of abandoned surveys');
            $table->boolean('is_active')->default(true)->comment('Indicates if the survey is active or inactive');
            $table->unsignedInteger('edit_count')->default(0)->comment('Tracks how many times the survey was edited');
            $table->timestamp('last_modified_at')->nullable()->comment('Timestamp of the last modification to the survey');

            // Manually add created_at and updated_at columns with comments
            $table->timestamp('created_at')->nullable()->comment('Timestamp when the survey response was created');
            $table->timestamp('updated_at')->nullable()->comment('Timestamp when the survey response was last updated');

            // Foreign key constraints
            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade')->comment('Foreign key constraint linking to surveys table');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('set null')->comment('Foreign key constraint linking to folders table');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->comment('Foreign key constraint linking to users table');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->comment('Foreign key constraint linking to companies table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_responses');
    }
}
