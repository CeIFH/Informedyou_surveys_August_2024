<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;

class SurveyDropdown extends Component
{
    public $survey;

    public function mount($survey)
    {
        $this->survey = $survey;
    }

    public function render()
    {
        return view('livewire.wave.survey-dropdown');
    }

    public function deleteSurvey()
    {
        // Add logic to delete the survey
        $this->survey->delete();
        $this->dispatch('surveyDeleted');
    }

    public function duplicateSurvey()
    {
        // Add logic to duplicate the survey
        $newSurvey = $this->survey->replicate();
        $newSurvey->title = 'Copy of ' . $newSurvey->title;
        $newSurvey->save();
        $this->dispatch('surveyDuplicated');
    }
}