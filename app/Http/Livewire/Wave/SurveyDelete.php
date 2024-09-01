<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;

class SurveyDelete extends Component
{
    public $surveyId;

    public function mount($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    public function deleteSurvey()
    {
        $survey = Survey::findOrFail($this->surveyId);
        $survey->delete();

        return redirect()->route('home')->with('message', 'Survey deleted successfully.');
    }

    public function render()
    {
        return view('livewire.survey-delete');
    }
}
