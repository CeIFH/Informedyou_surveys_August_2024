<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;

class DropdownMenu extends Component
{
    public $survey;
    public $isOpen = false;

    public function mount(Survey $survey)
    {
        $this->survey = $survey;
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        return view('livewire.dropdown-menu');
    }
}
