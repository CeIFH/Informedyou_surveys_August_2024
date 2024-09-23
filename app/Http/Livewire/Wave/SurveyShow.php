<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;
use Illuminate\Support\Facades\Log;

class SurveyShow extends Component
{
    public $survey;
    public $title;
    public $questions;
    public $responses = [];
    public $signatures = [];

    
    
    //this is not in use the survey response is used instead
    
    
    
    
    public function mount($surveyId)
    {
        Log::info("SurveyShow mount method called for survey ID: {$surveyId}");
        
        $this->survey = Survey::findOrFail($surveyId);
        
        Log::info("Survey found", ['survey_id' => $this->survey->id, 'current_view_count' => $this->survey->view_count]);
        
        $this->survey->incrementViewCount();
        
        Log::info("incrementViewCount called", ['new_view_count' => $this->survey->view_count]);

        $this->title = $this->survey->title;
        $this->questions = json_decode($this->survey->content, true);

        // Additional logging for more context
        Log::info("SurveyShow component mounted for survey: {$this->survey->id}", [
            'user_id' => auth()->id() ?? 'guest',
            'ip_address' => request()->ip(),
        ]);

        // Initialize responses array
        foreach ($this->questions as $index => $question) {
            if ($question['type'] === 'checkbox') {
                $this->responses[$index] = [];
            } else {
                $this->responses[$index] = '';
            }
        }
    }

    public function submit()
    {
        // Handle form submission
        // You can add validation and saving logic here
    }

    public function generateAndDownloadPdf()
    {
        // Handle PDF generation and download
        // You can implement this functionality here
    }

    public function render()
    {
        return view('livewire.survey-show');
    }
}