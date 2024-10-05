<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Folder;
use App\Models\Survey;
use App\Models\Company;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Question;
use App\Models\Option;
use Livewire\Attributes\On;
use App\Models\CompletionMessage;

class Home extends Component
{
    use WithPagination;

    public $id;
    
    public $showFolderModal = false;
    public $newFolderName = '';
    public $currentFolder = null;
    public $editingFolder = null;
    public $editingFolderId = null;
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
    public $editingFolderName = '';


    // Sorting variables for folders
    public $folderSortField = 'created_at';
    public $folderSortAsc = false;

    // Sorting variables for surveys
    public $surveySortField = 'created_at';
    public $surveySortAsc = false;

    // Survey Dropdown Properties
    public $showDropdown = false;
    public $showDeleteConfirmation = false;

    // Folder deletion properties
    public $folderToDelete;
    public $showDeleteFolderModal = false;
    public $folderDeleteConfirmationText = '';

    protected $queryString = ['folderSearch', 'surveySearch', 'selectedFolderId', 'selectedCompanyId'];

    public $showDeleteEmptyFolderModal = false;

    public function mount()
    {
        $this->id = uniqid();
        Log::info('Home component mounted', ['componentId' => $this->id]);
        
        // Get the companies the user is assigned to
        $userCompanies = Auth::user()->companies;
        
        if ($userCompanies->isNotEmpty()) {
            // Set the selected company to the first company the user is assigned to
            $this->selectedCompanyId = $userCompanies->first()->id;
            session(['selected_company_id' => $this->selectedCompanyId]);
        } else {
            // Handle the case where the user has no assigned companies
            Log::warning('User has no assigned companies', ['userId' => Auth::id()]);
            // You might want to redirect to an error page or show a message
            $this->selectedCompanyId = null;
        }
        
        // Always set the default folder to "All Surveys"
        $this->selectedFolderId = null;
        $this->selectedFolderName = 'All Surveys';
        
        // Remove the session check for selected_folder_id
        // session('selected_folder_id', null);
        
        $this->loadCompanyData();
    }

    private function loadCompanyData()
    {
        if ($this->selectedCompanyId && Auth::user()->companies->contains('id', $this->selectedCompanyId)) {
            $this->totalCompanySurveys = Survey::where('company_id', $this->selectedCompanyId)->count();
        } else {
            $this->totalCompanySurveys = 0;
        }
    }

    public function selectFolder($folderId = null, $folderName = 'All Surveys')
    {
        $this->selectedFolderId = $folderId;
        $this->selectedFolderName = $folderName;
        $this->resetPage('surveys');
        $this->showFolderList = false;
        // Reset selected survey when changing folders
        $this->selectedSurveyId = null;
        
        // Update the session
        session(['selected_folder_id' => $this->selectedFolderId]);
        
        // Dispatch an event to reset survey analytics
    $this->dispatch('resetSurveyAnalytics');
        // Dispatch an event if needed
        $this->dispatch('folderSelected', [
            'folderId' => $this->selectedFolderId,
            'folderName' => $this->selectedFolderName
        ]);

        // Reset survey analytics
        $this->dispatch('resetSurveyAnalytics');
    }

    public function selectSurvey($surveyId)
    {
        if (Survey::where('company_id', $this->selectedCompanyId)->where('id', $surveyId)->exists()) {
            $this->selectedSurveyId = $surveyId;
            $this->dispatch('surveySelected', surveyId: $surveyId);
            
            // Force a full page reload
            $this->dispatch('reloadPage');
        } else {
            // Handle invalid survey selection
            Log::warning('Invalid survey selection attempt', ['userId' => Auth::id(), 'surveyId' => $surveyId]);
            session()->flash('error', 'Invalid survey selection.');
        }
    }

    // ... (keep other existing methods)

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

    // ... (keep other existing methods)

    public function toggleFolderView()
    {
        $this->folderGridView = !$this->folderGridView;
    }

    public function toggleSurveyView()
    {
        $this->surveyGridView = !$this->surveyGridView;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $userCompanies = Auth::user()->companies;
        
        if ($this->selectedCompanyId && $userCompanies->contains('id', $this->selectedCompanyId)) {
            $this->totalCompanySurveys = Survey::where('company_id', $this->selectedCompanyId)
                ->where('title', 'like', '%' . $this->surveySearch . '%')
                ->count();
            
            $foldersQuery = Folder::where('company_id', $this->selectedCompanyId)
                ->where('name', 'like', '%' . $this->folderSearch . '%')
                ->orderBy($this->folderSortField, $this->folderSortAsc ? 'asc' : 'desc');

            $totalFolders = $foldersQuery->count();
            $folders = $foldersQuery->paginate(11, ['*'], 'folders');

            $surveysQuery = Survey::where('company_id', $this->selectedCompanyId);

            // Only filter by folder if a specific folder is selected
            if ($this->selectedFolderId) {
                $surveysQuery->where('folder_id', $this->selectedFolderId);
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

            $viewData = [
                'folders' => $folders,
                'totalFolders' => $totalFolders,
                'folderSurveys' => $folderSurveys,
                'totalSurveys' => $this->selectedFolderId ? $totalSurveys : $this->totalCompanySurveys,
                'selectedFolder' => $this->selectedFolderId ? Folder::where('company_id', $this->selectedCompanyId)->find($this->selectedFolderId) : null,
                'userCompanies' => $userCompanies,
                'selectedFolderName' => $this->selectedFolderName,
                'selectedSurveyId' => $this->selectedSurveyId,
                'hasAccess' => true,
            ];
        } else {
            $viewData = [
                'folders' => collect(),
                'totalFolders' => 0,
                'folderSurveys' => collect(),
                'totalSurveys' => 0,
                'selectedFolder' => null,
                'userCompanies' => $userCompanies,
                'selectedFolderName' => 'All Surveys',
                'selectedSurveyId' => null,
                'hasAccess' => false,
            ];
        }

        return view('livewire.wave.home', $viewData);
    }

    // ... (keep other existing methods)

    public function duplicateSurvey($surveyId)
    {
        DB::beginTransaction();

        try {
            $originalSurvey = Survey::findOrFail($surveyId);

            // Duplicate the survey
            $newSurvey = $originalSurvey->replicate();
            $newSurvey->title = "Copy of " . $newSurvey->title;
            $newSurvey->view_count = 0; // Reset view count for the new survey
            $newSurvey->save();

            // The content column already contains the questions and options in JSON format
            // No need to duplicate questions and options separately

            // Duplicate other attributes if needed
            // For example, if you need to create new records in other tables that reference this survey,
            // you would do that here.

            DB::commit();

            // Log success
            Log::info('Survey duplicated successfully', ['original_id' => $surveyId, 'new_id' => $newSurvey->id]);

            // Flash success message
            session()->flash('message', 'Survey duplicated successfully.');

            // Optionally redirect to the new survey's edit page
            return redirect()->route('survey.edit', ['surveyId' => $newSurvey->id]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Survey duplication failed', ['survey_id' => $surveyId, 'error' => $e->getMessage()]);

            // Flash error message
            session()->flash('error', 'Failed to duplicate survey. Please try again.');

            // Optionally, you can rethrow the exception if you want it to be handled by Laravel's exception handler
            // throw $e;
        }
    }

    public function switchCompany($companyId)
    {
        if (Auth::user()->companies->contains('id', $companyId)) {
            $this->selectedCompanyId = $companyId;
            session(['selected_company_id' => $companyId]);
            
            // Reset folder selection
            $this->selectedFolderId = null;
            $this->selectedFolderName = 'All Surveys';
            session()->forget('selected_folder_id');
            
   // Reset selected survey
   $this->selectedSurveyId = null;

            // Reset search and pagination
            $this->folderSearch = '';
            $this->surveySearch = '';
            $this->resetPage('folders');
            $this->resetPage('surveys');

            $this->loadCompanyData();
            
            // Dispatch an event to reset survey analytics
            $this->dispatch('resetSurveyAnalytics');

            // Force a complete re-render of the component
            $this->dispatch('companyChanged');

            // Refresh the entire page
            $this->dispatch('refreshPage');
        } else {
            session()->flash('error', 'You do not have permission to access this company.');
        }
    }

    public function updatedSelectedCompanyId()
    {
        $this->switchCompany($this->selectedCompanyId);
    }

    public function updatedSelectedFolderId()
    {
        if ($this->selectedFolderId) {
            $folder = Folder::find($this->selectedFolderId);
            if ($folder) {
                $this->selectedFolderName = $folder->name;
            }
        } else {
            $this->selectedFolderName = 'All Surveys';
        }
        $this->resetPage('surveys');
    }

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

    public function sortFolders($field)
    {
        if ($this->folderSortField === $field) {
            $this->folderSortAsc = !$this->folderSortAsc;
        } else {
            $this->folderSortField = $field;
            $this->folderSortAsc = true;
        }
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

    #[On('folderListUpdated')]
    public function refreshFolderList()
    {
        // This method will be called when the folderListUpdated event is emitted
        // The component will automatically re-render, which will fetch the updated folder list
        $this->resetPage('folders');
    }

    public function sortSurveys($field)
    {
        if ($this->surveySortField === $field) {
            $this->surveySortAsc = !$this->surveySortAsc;
        } else {
            $this->surveySortField = $field;
            $this->surveySortAsc = false; // Default to descending order when changing fields
        }
    }

    public function refreshPage()
    {
        // This method will be called by the 'refreshPage' event
        // It doesn't need to do anything as the component will re-render automatically
    }

    public function reloadPage()
    {
        // This method will be called by the 'reloadPage' event
        // It doesn't need to do anything as we'll handle the reload in JavaScript
    }

}