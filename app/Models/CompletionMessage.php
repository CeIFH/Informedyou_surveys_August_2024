<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletionMessage extends Model
{
    use HasFactory;

    protected $fillable = ['survey_id', 'title', 'content', 'condition', 'is_default'];

    /**
     * Get the survey that owns the completion message.
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}