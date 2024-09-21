<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;

class SurveyEdit extends Component
{
    public Survey $survey;

    public function mount(Survey $survey)
    {
        $this->survey = $survey;
    }

    public function render()
    {
        return view('livewire.wave.survey-edit');
    }
}
