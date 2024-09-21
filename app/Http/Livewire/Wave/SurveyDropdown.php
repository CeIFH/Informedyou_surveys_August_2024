<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;

class SurveyDropdown extends Component
{
    public Survey $survey;
    public $showDropdown = false;
    public $showDeleteConfirmation = false;  // Add this line
    public $deleteConfirmationText = '';

    protected $rules = [
        'deleteConfirmationText' => 'required|in:DELETE'
    ];

    public function mount(Survey $survey)
    {
        $this->survey = $survey;
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function duplicateSurvey()
    {
        $newSurvey = $this->survey->replicate();
        $newSurvey->title = 'Copy of ' . $newSurvey->title;
        $newSurvey->save();

        $this->showDropdown = false;
        $this->dispatch('surveyDuplicated');
        session()->flash('message', 'Survey duplicated successfully.');
    }

    public function confirmDelete()
    {
        $this->showDropdown = false;
        $this->showDeleteConfirmation = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteConfirmation = false;
        $this->deleteConfirmationText = '';
        $this->resetValidation();
    }

    public function deleteSurvey()
    {
        $this->validate();

        $this->survey->delete();
        $this->showDeleteConfirmation = false;
        $this->dispatch('surveyDeleted');
        session()->flash('message', 'Survey deleted successfully.');
    }

    public function render()
    {
        return view('livewire.wave.survey-dropdown');
    }
}