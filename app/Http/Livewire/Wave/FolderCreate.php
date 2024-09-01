<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Folder;
use Illuminate\Support\Facades\Log;

class FolderCreate extends Component
{
    public $newFolderName = '';
    public $showFolderModal = false;

    protected $rules = [
        'newFolderName' => 'required|string|max:255',
    ];

    public function toggleFolderModal()
    {
        $this->showFolderModal = !$this->showFolderModal;
    }

    public function createFolder()
    {
        $this->validate();

        try {
            $folder = Folder::create(['name' => $this->newFolderName]);
            Log::info('Folder created successfully', ['folder_id' => $folder->id]);

            // Reset the form
            $this->newFolderName = '';
            $this->showFolderModal = false;

            // Dispatch an event to notify parent components
            $this->dispatch('folderCreated', folderId: $folder->id);

            $this->dispatch('notify', message: 'Folder created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating folder', ['error' => $e->getMessage()]);
            $this->dispatch('notify', message: 'Error creating folder: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.wave.folder-create');
    }
}
