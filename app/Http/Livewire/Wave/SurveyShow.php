<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;

class SurveyShow extends Component
{
    public $survey;
    public $title;
    public $questions;
    public $responses = [];
    public $signatures = [];

    public function mount($surveyId)
    {
        $survey = Survey::findOrFail($surveyId);
        $this->title = $survey->title;
        $this->questions = json_decode($survey->content, true);

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