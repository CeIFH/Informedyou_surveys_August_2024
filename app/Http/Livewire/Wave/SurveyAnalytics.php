<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;
use App\Models\Company;

class SurveyAnalytics extends Component
{
    public $surveys;
    public $selectedSurveyId;
    public $selectedSurvey;

    public function mount()
    {
        $user = auth()->user();
        $company = $user ? $user->company : null;
        
        if ($company) {
            $this->surveys = $company->surveys;
            $this->selectedSurveyId = $this->surveys->first()->id ?? null;
            $this->loadSurvey();
        } else {
            $this->surveys = collect();
        }
    }

    public function loadSurvey()
    {
        if ($this->selectedSurveyId) {
            $this->selectedSurvey = Survey::find($this->selectedSurveyId);
            $this->incrementViewCount();
        }
    }

    public function updatedSelectedSurveyId()
    {
        $this->loadSurvey();
    }

    private function incrementViewCount()
    {
        if ($this->selectedSurvey) {
            $this->selectedSurvey->incrementViewCount();
            if ($this->selectedSurvey->folder) {
                $this->selectedSurvey->folder->incrementSurveysViewCount();
            }
        }
    }

    public function render()
    {
        return view('livewire.wave.survey-analytics');
    }
}
