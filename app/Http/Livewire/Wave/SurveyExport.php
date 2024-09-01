<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;
use Maatwebsite\Excel\Facades\Excel; // Assuming you are using Laravel Excel

class SurveyExport extends Component
{
    public $survey;

    public function mount($id)
    {
        $this->survey = Survey::findOrFail($id);
    }

    public function export()
    {
        // Logic to export the survey data, for example as Excel file
        return Excel::download(new SurveyExport($this->survey), $this->survey->title . '.xlsx');
    }

    public function render()
    {
        return view('livewire.survey-export', ['survey' => $this->survey]);
    }
}
