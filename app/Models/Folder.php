<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id',
        // other fillable attributes...
    ];

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    // New method to save folder response
    public function saveResponse($response)
    {
        $this->responses()->create(['response' => $response]);
    }

    public function responses()
    {
        return $this->hasMany(FolderResponse::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function incrementSurveysViewCount()
    {
        $this->surveys()->increment('view_count');
    }
}