<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'folder_id',
        'company_id',
        'redirect_url',
        'redirect_type',
        'redirect_delay',
        'is_active',
        'inactive_message',
        'completion_message', // Make sure this is included
    ];

    protected static function booted()
    {
        static::saving(function ($survey) {
            \Log::info('Saving survey', [
                'survey_id' => $survey->id,
                'folder_id' => $survey->folder_id,
                'company_id' => $survey->company_id,
            ]);
        });
    }

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Define the relationship: A Survey belongs to a Folder.
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Define the relationship: A Survey has many CompletionMessages.
     */
    public function completionMessages()
    {
        return $this->hasMany(CompletionMessage::class);
    }

    /**
     * Get the default completion message.
     *
     * @return CompletionMessage|null
     */
    public function getDefaultCompletionMessage()
    {
        return $this->completionMessages()->where('is_default', true)->first();
    }

    /**
     * Get the appropriate completion message based on conditions.
     *
     * @param array $responses The survey responses
     * @return CompletionMessage
     */
    public function getCompletionMessage($responses)
    {
        foreach ($this->completionMessages as $message) {
            if (empty($message->condition) || $this->evaluateCondition($message->condition, $responses)) {
                return $message;
            }
        }

        return $this->getDefaultCompletionMessage() ?? new CompletionMessage([
            'content' => 'Thank you for completing the survey!'
        ]);
    }

    /**
     * Evaluate a condition against the survey responses.
     *
     * @param string $condition
     * @param array $responses
     * @return bool
     */
    private function evaluateCondition($condition, $responses)
    {
        // This is a simple example. You might want to implement a more sophisticated
        // condition evaluation system based on your specific needs.
        $condition = str_replace(['{{', '}}'], ['$responses["', '"]'], $condition);
        return eval("return $condition;");
    }

    /**
     * Define the relationship: A Survey has many Views.
     */
    public function views()
    {
        return $this->hasMany(View::class);
    }

    // New method to increment visit count
    public function incrementVisitCount()
    {
        $this->views_count = $this->views_count + 1;
        $this->save();
    }

    // New method to save survey response
    public function saveResponse($response)
    {
        $this->responses()->create(['response' => $response]);
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}