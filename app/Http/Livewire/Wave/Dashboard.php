<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use Illuminate\Support\Facades\Redirect;

class Dashboard extends Component
{
    public $tools = [
        ['name' => 'Surveys', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'color' => '#60A5FA'],
        ['name' => 'Polls', 'icon' => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z', 'color' => '#A78BFA'],
        ['name' => 'Presentations', 'icon' => 'M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z', 'color' => '#F472B6'],
        ['name' => 'Feeback', 'icon' => 'M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122', 'color' => '#34D399']
    ];

    public $showComingSoonModal = false;
    public $comingSoonTool = '';

    public function render()
    {
        return view('livewire.wave.dashboard');
    }

    public function enterTool($toolName)
    {
        if ($toolName === 'Surveys') {
            return redirect()->to('/survey/dashboard');
        } else {
            $this->showComingSoonModal = true;
            $this->comingSoonTool = $toolName;
        }
    }
}