<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Folder;
use App\Models\Survey;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class Home extends Component
{
    use WithPagination;

    public $showFolderModal = false;
    public $newFolderName = '';
    public $currentFolder = null;
    public $editingFolder = null;
    public $folderGridView = true;
    public $surveyGridView = true;
    public $folderSearch = '';
    public $surveySearch = '';
    public $selectedFolderId = null;

    // Sorting variables for folders
    public $folderSortField = 'name';
    public $folderSortAsc = true;

    // Sorting variables for surveys
    public $surveySortField = 'title';
    public $surveySortAsc = true;

    protected $queryString = ['folderSearch', 'surveySearch', 'selectedFolderId'];
    protected $listeners = ['folderCreated' => '$refresh'];

    protected $rules = [
        'newFolderName' => 'required|string|max:255',
    ];

    public function updatingFolderSearch()
    {
        $this->resetPage('folders');
    }

    public function updatingSurveySearch()
    {
        $this->resetPage('surveys');
    }

    public function clearFolderSearch()
    {
        $this->folderSearch = '';
        $this->resetPage('folders');
    }

    public function clearSurveySearch()
    {
        $this->surveySearch = '';
        $this->resetPage('surveys');
    }

    public function updatedFolderSearch()
    {
        $this->resetPage('folders');
    }

    public function updatedSurveySearch()
    {
        $this->resetPage('surveys');
    }

    public function toggleFolderModal()
    {
        $this->showFolderModal = !$this->showFolderModal;
    }

    public function createFolder()
    {
        $this->validate([
            'newFolderName' => 'required|string|max:255',
        ]);
        
        Folder::create([
            'name' => $this->newFolderName,
            // Add any other necessary fields
        ]);

        $this->newFolderName = '';
        $this->showFolderModal = false;
        $this->emit('folderCreated');
    }

    public function editFolder(Folder $folder)
    {
        $this->editingFolder = $folder;
        $this->newFolderName = $folder->name;
        $this->showFolderModal = true;
    }

    public function resetFolderForm()
    {
        $this->newFolderName = '';
        $this->editingFolder = null;
        $this->showFolderModal = false;
    }

    public function toggleFolderView()
    {
        $this->folderGridView = !$this->folderGridView;
    }

    public function toggleSurveyView()
    {
        $this->surveyGridView = !$this->surveyGridView;
    }

    public function sortFolders($field)
    {
        if ($this->folderSortField === $field) {
            $this->folderSortAsc = !$this->folderSortAsc;
        } else {
            $this->folderSortAsc = true;
        }
        
        $this->folderSortField = $field;
    }

    public function sortSurveys($field)
    {
        if ($this->surveySortField === $field) {
            $this->surveySortAsc = !$this->surveySortAsc;
        } else {
            $this->surveySortAsc = true;
        }
        
        $this->surveySortField = $field;
    }

    public function selectFolder($folderId = null)
    {
        $this->selectedFolderId = $folderId;
        $this->resetPage('surveys');
    }

    #[Layout('layouts.app')]  // This line specifies the layout to use
    public function render()
    {
        $foldersQuery = Folder::where('name', 'like', '%' . $this->folderSearch . '%')
            ->orderBy('created_at', 'desc')  // Order by creation date, newest first
            ->orderBy($this->folderSortField, $this->folderSortAsc ? 'asc' : 'desc');

        $totalFolders = $foldersQuery->count();
        $folders = $foldersQuery->paginate(11, ['*'], 'folders');

        $totalSurveys = Survey::count(); // Total count of all surveys

        $surveysQuery = $this->selectedFolderId
            ? Survey::where('folder_id', $this->selectedFolderId)
            : Survey::query();

        $surveysQuery = $surveysQuery->where('title', 'like', '%' . $this->surveySearch . '%')
            ->orderBy('created_at', 'desc')  // Order by creation date, newest first
            ->orderBy($this->surveySortField, $this->surveySortAsc ? 'asc' : 'desc');

        $folderSurveys = $surveysQuery->paginate(11, ['*'], 'surveys');

        return view('livewire.wave.home', [
            'folders' => $folders,
            'totalFolders' => $totalFolders,
            'folderSurveys' => $folderSurveys,
            'totalSurveys' => $totalSurveys,
            'selectedFolder' => $this->selectedFolderId ? Folder::find($this->selectedFolderId) : null,
        ]);
    }
}