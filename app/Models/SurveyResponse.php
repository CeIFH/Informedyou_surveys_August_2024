<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SurveyResponse extends Model
{
    // Define the fillable fields for mass assignment
    protected $fillable = [
        'survey_id',
        'folder_id',
        'user_id',
        'responses',
        'signature',
        'completion_uuid',
        'session_id',
        'ip_address',
        'geolocation',
        'device_type',
        'browser_type',
        'device_os',
        'referral_source',
        'view_count',
        'viewed_at',
        'response_started_at',
        'response_completed_at',
        'response_duration',
        'is_completed',
        'is_returning_user',
        'survey_version',
        'survey_score',
        'is_abandoned',
        'question_timings',
        'completion_path',
        'device_orientation',
        'interaction_metadata',
        'completion_feedback',
        'platform_type',
        'timezone',
        'pre_survey_duration',
        'response_change_count',
        'partial_responses',
        'device_orientation_changed',
        'network_connection',
        'referrer_url',
        'completion_behavior',
        'custom_metadata',
        'time_to_first_interaction',
        'display_mode',
        'browser_plugins',
        'completion_count',
        'abandoned_count',
        'is_active',
        'edit_count',
        'last_modified_at',
        'company_id',
    ];

    // Automatically generate UUID on model creation
    public static function boot()
    {
        parent::boot();

        // Create UUID for each survey response
        static::creating(function ($model) {
            if (empty($model->completion_uuid)) {
                $model->completion_uuid = Str::uuid()->toString();
            }
        });
    }

    protected $casts = [
        'response_started_at' => 'datetime',
        'response_completed_at' => 'datetime',
        'question_timings' => 'array',
        'responses' => 'array', // This ensures the responses are stored and retrieved as an array
    ];
}
