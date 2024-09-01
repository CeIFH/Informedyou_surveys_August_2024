<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SurveyDuplicate extends Component
{
    public $surveyId;

    public function mount($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    public function duplicateSurvey()
    {
        $survey = Survey::findOrFail($this->surveyId);

        DB::transaction(function () use ($survey) {
            // Duplicate the survey
            $newSurvey = $survey->replicate();
            $newSurvey->title .= ' - Copy';
            $newSurvey->save();

            // Duplicate the completion messages
            foreach ($survey->completionMessages as $completionMessage) {
                $newSurvey->completionMessages()->create([
                    'title' => $completionMessage->title,
                    'content' => $completionMessage->content,
                    'condition' => $completionMessage->condition,
                    'is_default' => $completionMessage->is_default,
                ]);
            }

            Log::info('Survey duplicated successfully', ['newSurveyId' => $newSurvey->id]);

            // Redirect to the edit page of the new survey
            return redirect()->route('survey.edit', $newSurvey->id);
        });
    }

    public function render()
    {
        return view('livewire.survey-duplicate');
    }
}
