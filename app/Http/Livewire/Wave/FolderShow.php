<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Folder;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class FolderShow extends Component
{
    use WithPagination;

    public $folder;
    public $folders = [];
    public $showFolderModal = false;
    public $newFolderName = '';

    protected $rules = [
        'newFolderName' => 'required|string|max:255',
    ];

    public function mount($id)
    {
        $this->folder = Folder::findOrFail($id);
        $this->loadFolders();
    }

    public function loadFolders()
    {
        $this->folders = Folder::all();
    }

    public function toggleModal()
    {
        $this->showFolderModal = !$this->showFolderModal;
    }

    public function createFolder()
    {
        $this->validate();

        Folder::create(['name' => $this->newFolderName]);

        $this->newFolderName = '';
        $this->showFolderModal = false;
        $this->loadFolders();
        $this->dispatch('folderListUpdated');
    }

    #[On('folderListUpdated')]
    public function handleFolderListUpdated()
    {
        $this->loadFolders();
    }

    public function render()
    {
        return view('livewire.folder-show', [
            'surveys' => $this->folder->surveys()->paginate(12),
        ]);
    }
}