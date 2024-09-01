<?php

namespace App\Http\Livewire\Wave;

use Closure;
use Illuminate\Contracts\View\View;
use Livewire\Component;


class SurveyDropdown extends Component
{
    public $icon;

    /**
     * Create a new component instance.
     *
     * @param string $icon
     * @return void
     */
    public function __construct($icon = null)
    {
        $this->icon = $icon;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('livewire.wave.survey-dropdown');
    }
}