<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Folder;
use App\Models\Company;
use Illuminate\Support\Collection;

class FolderAnalytics extends Component
{
    public $folders;
    public $selectedFolderId;
    public $folderSurveys;
    public $selectedFolder;

    public function mount()
    {
        $user = auth()->user();
        $company = $user ? $user->company : null;
        
        if ($company) {
            $this->folders = $company->folders;
            $this->selectedFolderId = $this->folders->first()->id ?? null;
            $this->loadFolderSurveys();
        } else {
            $this->folders = new Collection();
            $this->folderSurveys = new Collection();
        }
    }

    public function loadFolderSurveys()
    {
        if ($this->selectedFolderId) {
            $this->selectedFolder = Folder::find($this->selectedFolderId);
            if ($this->selectedFolder) {
                $this->folderSurveys = $this->selectedFolder->surveys;
            } else {
                $this->folderSurveys = new Collection();
            }
        } else {
            $this->folderSurveys = new Collection();
        }
    }

    public function updatedSelectedFolderId()
    {
        $this->loadFolderSurveys();
    }

    public function render()
    {
        return view('livewire.wave.folder-analytics');
    }
}
