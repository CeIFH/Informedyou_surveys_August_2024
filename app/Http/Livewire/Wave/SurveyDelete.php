<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;

class SurveyDelete extends Component
{
    public Survey $survey;

    public function mount(Survey $survey)
    {
        $this->survey = $survey;
    }

    public function deleteSurvey()
    {
        $this->survey->delete();
        $this->dispatch('surveyDeleted');
        session()->flash('message', 'Survey deleted successfully.');
    }

    public function render()
    {
        return view('livewire.wave.survey-delete');
    }
}
