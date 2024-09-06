<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyViewsAnalytics extends Model
{
    use HasFactory;



protected $fillable = [
    'survey_id',
    'folder_id',
    'user_id',
    'session_id',
    'ip_address',
    'geolocation',
    'device_type',
    'browser_type',
    'device_os',
    'referral_source',
    'viewed_at',
    'response_started_at',
    'response_completed_at',
    'response_duration',
    'is_completed',
    'is_returning_user',
    'survey_version',
    'survey_score',
];

protected $casts = [
    'geolocation' => 'array',
    'viewed_at' => 'datetime',
    'response_started_at' => 'datetime',
    'response_completed_at' => 'datetime',
];

// Relationships
public function survey()
{
    return $this->belongsTo(Survey::class);
}

public function folder()
{
    return $this->belongsTo(Folder::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}
}