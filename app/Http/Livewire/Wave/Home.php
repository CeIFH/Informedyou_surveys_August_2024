<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Folder;
use App\Models\Survey;
use App\Models\Company;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Home extends Component
{
    use WithPagination;

    public $id;
    
    public $showFolderModal = false;
    public $newFolderName = '';
    public $currentFolder = null;
    public $editingFolder = null;
    public $folderGridView = true;
    public $surveyGridView = true;
    public $folderSearch = '';
    public $surveySearch = '';
    public $selectedFolderId = null;
    public $selectedSurveyId;
    public $selectedCompanyId;
    public $totalCompanySurveys = 0;
    public $selectedFolderName = 'All Surveys';
    public $showFolderList = false;
    public $surveyToDelete;
    public $showDeleteSurveyModal = false;
    public $surveyDeleteConfirmationText = '';
    public $surveys;

    // Sorting variables for folders
    public $folderSortField = 'name';
    public $folderSortAsc = true;

    // Sorting variables for surveys
    public $surveySortField = 'title';
    public $surveySortAsc = true;

    // Survey Dropdown Properties
    public $showDropdown = false;
    public $showDeleteConfirmation = false;

    // Folder deletion properties
    public $folderToDelete;
    public $showDeleteFolderModal = false;
    public $folderDeleteConfirmationText = '';

    protected $queryString = ['folderSearch', 'surveySearch', 'selectedFolderId', 'selectedCompanyId'];

    protected $listeners = [
        'folderCreated' => '$refresh',
        'survey-dropdown' => '$refresh',
        'folderDeleted' => '$refresh'
    ];

    protected $rules = [
        'newFolderName' => 'required|string|max:255',
        'folderDeleteConfirmationText' => 'required|in:delete surveys and folder',
        'surveyDeleteConfirmationText' => 'required|in:delete',
    ];

    public function toggleFolderList()
    {
        $this->showFolderList = !$this->showFolderList;
    }

    public function selectFolder($folderId = null, $folderName = 'All Surveys')
    {
        Log::info('selectFolder method called', [
            'folderId' => $folderId,
            'folderName' => $folderName,
        ]);
        
        try {
            $this->selectedFolderId = ($folderId !== 'null' && $folderId !== null) ? $folderId : null;
            $this->selectedFolderName = $folderName;
            
            $this->resetPage('surveys');
            $this->showFolderList = false;
            
            session(['selected_folder_id' => $this->selectedFolderId]);
            
            $this->dispatch('folderSelected', [
                'folderId' => $this->selectedFolderId,
                'folderName' => $this->selectedFolderName
            ]);
            
            Log::info('selectFolder method completed', [
                'newSelectedFolderId' => $this->selectedFolderId,
                'newSelectedFolderName' => $this->selectedFolderName,
                'sessionFolderId' => session('selected_folder_id'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in selectFolder method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    public function updatedSelectedFolderId()
    {
        $this->resetPage('surveys');
    }

    public function refreshFolders()
    {
        Log::info('Refreshing folders');
        $this->render();
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

    public function switchCompany($companyId)
    {
        if (Auth::user()->companies->contains('id', $companyId)) {
            $this->selectedCompanyId = $companyId;
            session(['selected_company_id' => $companyId]);
            $this->resetPage('folders');
            $this->resetPage('surveys');
            $this->selectedFolderId = null;
            $this->selectedFolderName = 'All Surveys';
            $this->loadCompanyData();
            $this->dispatch('companyChanged');
        } else {
            session()->flash('error', 'You do not have permission to access this company.');
        }
    }

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
        $this->render();
    }

    public function clearSurveySearch()
    {
        $this->surveySearch = '';
        $this->resetPage('surveys');
    }

    public function updatedFolderSearch()
    {
        $this->resetPage('folders');
        $this->render(); 

    }

    public function updatedSurveySearch()
    {
        $this->resetPage('surveys');
    }

    public $editingFolderId = null;
    public $editingFolderName = '';

    public function toggleFolderModal($folderId = null)
{
    $this->showFolderModal = !$this->showFolderModal;
    if ($folderId) {
        $this->editingFolderId = $folderId;
        $folder = Folder::find($folderId);
        $this->editingFolderName = $folder ? $folder->name : '';
    } else {
        $this->editingFolderId = null;
        $this->editingFolderName = '';
    }
    $this->dispatch('closeDropdown');
}

    public function saveFolder()
    {
        $this->validate([
            'editingFolderName' => 'required|string|max:255',
        ]);

        if (!$this->selectedCompanyId || !Auth::user()->companies->contains('id', $this->selectedCompanyId)) {
            session()->flash('error', 'You do not have permission to modify folders for this company.');
            return;
        }

        try {
            if ($this->editingFolderId) {
                $folder = Folder::findOrFail($this->editingFolderId);
                $folder->update(['name' => $this->editingFolderName]);
                $message = 'Folder updated successfully.';
            } else {
                Folder::create([
                    'name' => $this->editingFolderName,
                    'company_id' => $this->selectedCompanyId,
                ]);
                $message = 'Folder created successfully.';
            }

            $this->editingFolderName = '';
            $this->editingFolderId = null;
            $this->showFolderModal = false;
            $this->dispatch('folderSaved');
            $this->refreshFolderList();
            session()->flash('message', $message);
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving folder: ' . $e->getMessage());
            Log::error('Error saving folder', [
                'error' => $e->getMessage(),
                'selectedCompanyId' => $this->selectedCompanyId,
                'editingFolderName' => $this->editingFolderName,
                'editingFolderId' => $this->editingFolderId,
            ]);
        }
    }

    public function refreshFolderList()
    {
        $this->selectedFolderId = null;
        $this->selectedFolderName = 'All Surveys';
        $this->resetPage('folders');
        $this->resetPage('surveys');
    }

    #[Layout('layouts.app')]
    public function render()
{
    Log::info('Render method called', [
        'componentId' => $this->id ?? 'Not set',
        'class' => get_class($this),
        'traits' => class_uses($this),
    ]);

    $userCompanies = Auth::user()->companies;
    Log::info('User Companies:', $userCompanies->toArray());
    Log::info('Selected Company ID: ' . $this->selectedCompanyId);

    if ($this->selectedCompanyId && $userCompanies->contains('id', $this->selectedCompanyId)) {
        $this->totalCompanySurveys = Survey::where('company_id', $this->selectedCompanyId)
            ->where('title', 'like', '%' . $this->surveySearch . '%')
            ->count();
        
        Log::info('Total company surveys', ['count' => $this->totalCompanySurveys]);

        $foldersQuery = Folder::where('company_id', $this->selectedCompanyId)
            ->where('name', 'like', '%' . $this->folderSearch . '%')
            ->orderBy($this->folderSortField, $this->folderSortAsc ? 'asc' : 'desc');

        $totalFolders = $foldersQuery->count();
        $folders = $foldersQuery->paginate(11, ['*'], 'folders');

        Log::info('Folders query', [
            'totalFolders' => $totalFolders,
            'foldersCount' => $folders->count()
        ]);

        $surveysQuery = Survey::where('company_id', $this->selectedCompanyId);

        if ($this->selectedFolderId) {
            $surveysQuery->where('folder_id', $this->selectedFolderId);
            Log::info('Filtering surveys by folder', ['folderId' => $this->selectedFolderId]);
        }

        $surveysQuery = $surveysQuery->where('title', 'like', '%' . $this->surveySearch . '%')
            ->orderBy($this->surveySortField, $this->surveySortAsc ? 'asc' : 'desc');

        $totalSurveys = $surveysQuery->count();
        $folderSurveys = $surveysQuery->paginate(11, ['*'], 'surveys');

        // Calculate completion count for each survey
        $folderSurveys->getCollection()->transform(function ($survey) {
            $survey->completionCount = $survey->getCompletionCount();
            return $survey;
        });

        Log::info('Surveys query', [
            'totalSurveys' => $totalSurveys,
            'folderSurveysCount' => $folderSurveys->count()
        ]);
    } else {
        Log::info('No company selected or user does not have access');
        $folders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 11);
        $totalFolders = 0;
        $folderSurveys = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 11);
        $totalSurveys = 0;
        $this->totalCompanySurveys = 0;
    }

    $viewData = [
        'folders' => $folders,
        'totalFolders' => $folders->total(),
        'folderSurveys' => $folderSurveys,
        'totalSurveys' => $this->selectedFolderId ? $totalSurveys : $this->totalCompanySurveys,
        'selectedFolder' => $this->selectedFolderId ? Folder::find($this->selectedFolderId) : null,
        'userCompanies' => $userCompanies,
        'selectedFolderName' => $this->selectedFolderName,
    ];

    Log::info('render method completed', [
        'viewDataKeys' => array_keys($viewData),
        'totalSurveys' => $viewData['totalSurveys'],
        'selectedFolderName' => $viewData['selectedFolderName'],
        'componentId' => $this->id
    ]);

    return view('livewire.wave.home', $viewData);
}

    public function mount()
    {
        $this->id = uniqid();
        Log::info('Home component mounted', ['componentId' => $this->id]);
        
        $this->selectedCompanyId = $this->selectedCompanyId ?? auth()->user()->companies->first()->id;
        
        $this->selectedFolderId = session('selected_folder_id', null);
        
        if ($this->selectedFolderId) {
            $folder = Folder::find($this->selectedFolderId);
            if ($folder) {
                $this->selectedFolderName = $folder->name;
            } else {
                $this->selectedFolderId = null;
                $this->selectedFolderName = 'All Surveys';
            }
        } else {
            $this->selectedFolderName = 'All Surveys';
        }
        
        $this->loadCompanyData();

    }
    
    private function loadCompanyData()
    {
        $this->render();
    }

    private function getFirstAccessibleSurvey()
    {
        return Survey::where('company_id', $this->selectedCompanyId)->first()->id ?? null;
    }

    public function createSurvey()
    {
        if (!$this->selectedCompanyId || !Auth::user()->companies->contains('id', $this->selectedCompanyId)) {
            session()->flash('error', 'You do not have permission to create a survey for this company.');
            return;
        }

        $company = Company::findOrFail($this->selectedCompanyId);
        
        Survey::create([
            'title' => $this->surveyTitle,
            'content' => $this->surveyContent,
            'company_id' => $company->id,
            // ... other survey attributes ...
        ]);
    }

    // Survey Dropdown Methods
    
    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function duplicateSurvey($surveyId)
    {
        $survey = Survey::findOrFail($surveyId);
        $newSurvey = $survey->replicate();
        $newSurvey->title = 'Copy of ' . $newSurvey->title;
        $newSurvey->save();
        $this->showDropdown = false;
        $this->dispatch('surveyDuplicated');
        session()->flash('message', 'Survey duplicated successfully.');
    }

 

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->surveyToDelete = null;
        $this->deleteConfirmationText = '';
    }

    public function confirmDeleteSurvey($surveyId)
    {
        $this->surveyToDelete = Survey::findOrFail($surveyId);
        $this->showDeleteSurveyModal = true;
        $this->surveyDeleteConfirmationText = '';
        $this->dispatch('closeDropdown');
    }

    public function cancelDeleteSurvey()
    {
        $this->showDeleteSurveyModal = false;
        $this->surveyToDelete = null;
        $this->surveyDeleteConfirmationText = '';
        $this->resetValidation('surveyDeleteConfirmationText');
    }

    public function deleteSurvey()
    {
        $this->validate([
            'surveyDeleteConfirmationText' => 'required|in:delete',
        ]);

        if ($this->surveyToDelete) {
            $this->surveyToDelete->delete();
            $this->showDeleteSurveyModal = false;
            $this->surveyToDelete = null;
            $this->surveyDeleteConfirmationText = '';
            session()->flash('message', 'Survey deleted successfully.');
            $this->dispatch('surveyDeleted');
        }
    }

    public function closeDropdown()
    {
        $this->dispatch('closeDropdown');
    }

    
    // Folder Deletion Methods

    public function confirmDeleteFolder($folderId)
{
    $this->folderToDelete = Folder::findOrFail($folderId);
    $this->showDeleteFolderModal = true;
    $this->dispatch('closeDropdown');
}

public function cancelDeleteFolder()
{
    $this->showDeleteFolderModal = false;
    $this->folderDeleteConfirmationText = '';
    $this->folderToDelete = null;
    $this->resetValidation();
    $this->dispatch('closeDropdown');
}

    public function deleteFolderWithSurveys()
    {
        $this->validate([
            'folderDeleteConfirmationText' => 'required|in:delete surveys and folder',
        ]);

        if ($this->folderToDelete) {
            DB::transaction(function () {
                // Set folder_id to null for all surveys in this folder
                $this->folderToDelete->surveys()->update(['folder_id' => null]);
                
                // Delete the folder
                $this->folderToDelete->delete();
            });

            $this->dispatch('folderDeleted');
            session()->flash('message', 'Folder deleted and surveys unassigned successfully.');
        }

        $this->showDeleteFolderModal = false;
        $this->folderDeleteConfirmationText = '';
        $this->folderToDelete = null;
        $this->selectedFolderId = null;
        $this->selectedFolderName = 'All Surveys';
        $this->refreshFolderList();
    }

    public function deleteEmptyFolder()
    {
        if ($this->folderToDelete && $this->folderToDelete->surveys->isEmpty()) {
            $this->folderToDelete->delete();
            $this->dispatch('folderDeleted');
            session()->flash('message', 'Empty folder deleted successfully.');
        }

        $this->showDeleteFolderModal = false;
        $this->folderToDelete = null;
        $this->selectedFolderId = null;
        $this->selectedFolderName = 'All Surveys';
        $this->refreshFolderList();
    }

    public function confirmDelete($surveyId)
    {
        $this->surveyToDelete = Survey::findOrFail($surveyId);
        $this->showDeleteModal = true;
        $this->deleteConfirmationText = '';
    }



}

