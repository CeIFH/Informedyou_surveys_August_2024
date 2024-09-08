<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Survey;
use App\Models\Folder;
use App\Models\CompletionMessage;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Facades\Excel;

class SurveyBuilder extends Component
{
    use WithFileUploads;

    public $surveyId;
    public $title;
    public $description;
    public $questions = [];
    public $draggingQuestion = null;
    public $file;
    public $selectedFolder = null;
    public $newFolderName = '';
    public $folders = [];
    public $showFolderModal = false;
    public $completionMessages = [];
    public $defaultMessageIndex = 0;
    public $isEditMode = false;
    public $showSuccessModal = false;
    public $surveyUrl = '';
    public $responses = [];
    public $redirectUrl = '';
    public $redirectType = 'button';
    public $redirectDelay = 5;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'questions' => 'required|array|min:1',
        'questions.*.question' => 'required|string|max:255',
        'questions.*.type' => 'required|string|in:text,multiple_choice,checkbox,dropdown,textarea,email,phone,number,date,website,time,city,file,signature',
        'questions.*.options' => 'nullable|array',
        'questions.*.options.*' => 'nullable|string|max:255',
        'questions.*.subheading' => 'nullable|string|max:255',
        'questions.*.points' => 'nullable|integer',
        'questions.*.required' => 'boolean',
        'questions.*.show_subheading' => 'boolean',
        'questions.*.show_points' => 'boolean',
        'selectedFolder' => 'nullable|exists:folders,id',
        'completionMessages' => 'required|array|min:1',
        'completionMessages.*.title' => 'required|string|max:255',
        'completionMessages.*.content' => 'required|string',
        'completionMessages.*.condition' => 'nullable|string',
        'defaultMessageIndex' => 'required|integer|min:0',
        'redirectUrl' => 'nullable|string',
        'redirectType' => 'required|in:button,automatic',
        'redirectDelay' => 'required_if:redirectType,automatic|integer|min:1',
    ];


    public function mount($surveyId = null)
    {
        $this->loadFolders();

        if ($surveyId) {
            $this->surveyId = $surveyId;
            $this->isEditMode = true;
            $this->loadSurvey();
        } else {
            $this->initializeCompletionMessages();
        }
    }

    #[On('aiSurveyGenerated')] 
    public function handleAiGeneratedSurvey($data)
    {
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->questions = collect($data['questions'])->map(function ($question) {
            return [
                'question' => $question['question'],
                'type' => $question['type'],
                'options' => $question['options'] ?? [],
                'subheading' => $question['subheading'] ?? '',
                'show_subheading' => !empty($question['subheading']),
                'required' => false,
                'points' => 0,
                'show_points' => false,
            ];
        })->toArray();
        $this->isEditMode = false;
    }

    public function loadFolders()
    {
        $this->folders = Folder::all();
    }

    public function initializeCompletionMessages()
    {
        $this->completionMessages = [
            ['title' => 'Default Completion Message', 'content' => 'Thank you for completing the survey!', 'condition' => '']
        ];
    }

    public function loadSurvey()
    {
        $survey = Survey::findOrFail($this->surveyId);
        $this->title = $survey->title;
        $this->questions = json_decode($survey->content, true);
        $this->selectedFolder = $survey->folder_id;
        $this->completionMessages = $survey->completionMessages->map(function ($message) {
            return [
                'title' => $message->title,
                'content' => $message->content,
                'condition' => $message->condition,
            ];
        })->toArray();
        $this->defaultMessageIndex = $survey->completionMessages->search(function ($message) {
            return $message->is_default;
        });
        $this->redirectUrl = $survey->redirect_url ?? '';
        $this->redirectType = $survey->redirect_type ?? 'button';
        $this->redirectDelay = $survey->redirect_delay ?? 5;
    }

    public function saveSurvey()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $surveyData = [
                    'title' => $this->title,
                    'description' => $this->description,
                    'content' => json_encode($this->questions),
                    'folder_id' => $this->selectedFolder,
                    'redirect_url' => $this->formatUrl($this->redirectUrl),
                    'redirect_type' => $this->redirectType,
                    'redirect_delay' => $this->redirectDelay,
                ];

                if ($this->isEditMode) {
                    Log::info('Edit Mode: Updating survey', ['surveyId' => $this->surveyId]);
                    $survey = Survey::findOrFail($this->surveyId);
                    $survey->update($surveyData);
                    Log::info('Survey updated', ['survey' => $survey]);
                    $survey->completionMessages()->delete();
                } else {
                    Log::info('Create Mode: Creating new survey');
                    $survey = Survey::create($surveyData);
                    $this->surveyId = $survey->id;
                    $this->isEditMode = true;
                }

                foreach ($this->completionMessages as $index => $message) {
                    $survey->completionMessages()->create([
                        'title' => $message['title'],
                        'content' => $message['content'],
                        'condition' => $message['condition'],
                        'is_default' => ($index === $this->defaultMessageIndex)
                    ]);
                }

                $this->surveyUrl = route('survey.show', $this->surveyId);
                $this->showSuccessModal = true;
                $message = $this->isEditMode ? 'Survey updated successfully.' : 'Survey created successfully.';
                Log::info($message, ['survey_id' => $this->surveyId, 'folder_id' => $this->selectedFolder]);
                session()->flash('message', $message);
            });

            // Log the saved data for debugging
            Log::info('Survey saved with redirect settings', [
                'survey_id' => $this->surveyId,
                'redirect_url' => $this->redirectUrl,
                'redirect_type' => $this->redirectType,
                'redirect_delay' => $this->redirectDelay,
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving survey', ['error' => $e->getMessage()]);
            session()->flash('error', 'Error saving survey: ' . $e->getMessage());
        }
    }

    private function formatUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        // Remove existing protocol if present
        $url = preg_replace('#^https?://#', '', $url);

        // Add https:// prefix
        return 'https://' . $url;
    }

    public function addQuestion()
    {
        $this->questions[] = [
            'question' => '',
            'type' => 'text',
            'options' => [],
            'subheading' => '',
            'show_subheading' => false,
            'required' => false,
            'points' => 0,
            'show_points' => false,
        ];
    }

    public function removeQuestion($index)
    {
        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    public function addOption($questionIndex)
    {
        if (!isset($this->questions[$questionIndex]['options'])) {
            $this->questions[$questionIndex]['options'] = [];
        }
        $this->questions[$questionIndex]['options'][] = '';
    }

    public function removeOption($questionIndex, $optionIndex)
    {
        unset($this->questions[$questionIndex]['options'][$optionIndex]);
        $this->questions[$questionIndex]['options'] = array_values($this->questions[$questionIndex]['options']);
    }

    public function toggleSubheading($index)
    {
        $this->questions[$index]['show_subheading'] = !$this->questions[$index]['show_subheading'];
    }

    public function toggleRequired($index)
    {
        $this->questions[$index]['required'] = !$this->questions[$index]['required'];
    }

    public function togglePoints($index)
    {
        $this->questions[$index]['show_points'] = !$this->questions[$index]['show_points'];
    }

    public function addCompletionMessage()
    {
        $this->completionMessages[] = [
            'title' => '',
            'content' => '',
            'condition' => ''
        ];
    }

    public function removeCompletionMessage($index)
    {
        if ($index > 0) {
            unset($this->completionMessages[$index]);
            $this->completionMessages = array_values($this->completionMessages);
            if ($this->defaultMessageIndex >= count($this->completionMessages)) {
                $this->defaultMessageIndex = 0;
            }
        }
    }

    public function startDragging($index)
    {
        $this->draggingQuestion = $index;
    }

    public function endDragging()
    {
        $this->draggingQuestion = null;
    }

    public function drop($index, $position = 'after')
    {
        if ($this->draggingQuestion !== null) {
            $draggedQuestion = $this->questions[$this->draggingQuestion];
            array_splice($this->questions, $this->draggingQuestion, 1);
            if ($position === 'before') {
                array_splice($this->questions, $index, 0, [$draggedQuestion]);
            } else {
                array_splice($this->questions, $index + 1, 0, [$draggedQuestion]);
            }
            $this->draggingQuestion = null;
        }
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

        $folder = Folder::create(['name' => $this->newFolderName]);
        $this->loadFolders();
        $this->selectedFolder = $folder->id;
        $this->newFolderName = '';
        $this->showFolderModal = false;
    }

    public function importSurvey()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $path = $this->file->store('uploads');
        $data = Excel::toArray([], storage_path('app/' . $path));

        if (isset($data[0][0][1])) {
            $this->title = $data[0][0][1];
        }

        $this->questions = [];
        $allowedTypes = [
            'text', 'multiple_choice', 'checkbox', 'dropdown', 'textarea', 'email', 
            'phone', 'number', 'date', 'website', 'time', 'city', 'file', 'signature'
        ];

        foreach ($data[0][2] as $index => $type) {
            if ($index > 0 && !empty($type)) {
                $lowerType = strtolower($type);
                if (in_array($lowerType, $allowedTypes)) {
                    $question = [
                        'question' => $data[0][1][$index] ?? '',
                        'type' => $lowerType,
                        'options' => [],
                        'subheading' => $data[0][0][$index] ?? '',
                        'show_subheading' => !empty($data[0][0][$index]),
                        'required' => false,
                        'points' => $data[0][4][$index] ?? 0,
                        'show_points' => false,
                    ];
                    if (in_array($lowerType, ['multiple_choice', 'checkbox', 'dropdown'])) {
                        $question['options'] = array_map('trim', explode(',', $data[0][3][$index] ?? ''));
                    }
                    $this->questions[] = $question;
                }
            }
        }
    }

    public function duplicateSurvey($surveyId)
    {
        $survey = Survey::findOrFail($surveyId);
        DB::transaction(function () use ($survey) {
            $newSurvey = $survey->replicate();
            $newSurvey->title .= ' - Copy';
            $newSurvey->save();

            foreach ($survey->completionMessages as $completionMessage) {
                $newSurvey->completionMessages()->create([
                    'title' => $completionMessage->title,
                    'content' => $completionMessage->content,
                    'condition' => $completionMessage->condition,
                    'is_default' => $completionMessage->is_default,
                ]);
            }

            Log::info('Survey duplicated successfully', ['newSurveyId' => $newSurvey->id]);
            return redirect()->route('survey.edit', $newSurvey->id);
        });
    }

    public function previewSurvey()
    {
        if ($this->isEditMode && $this->surveyId) {
            return redirect()->route('survey.show', $this->surveyId);
        }
    }

    public function goHome()
    {
        if ($this->selectedFolder) {
            return redirect()->route('folder.show', $this->selectedFolder);
        } else {
            return redirect()->route('home');
        }
    }

    public function render()
    {
        return view('livewire.wave.survey-builder', [
            'surveyId' => $this->surveyId,
        ])->layout('layouts.app');
    }

    public function saveResponse()
    {
        $this->validate([
            'responses.*' => 'required',
        ]);

        try {
            DB::transaction(function () {
                $formattedResponses = [];
                foreach ($this->questions as $index => $question) {
                    $formattedResponses[] = [
                        'question' => $question['question'],
                        'type' => $question['type'],
                        'response' => $this->responses[$index] ?? null,
                    ];
                }

                $surveyResponse = SurveyResponse::create([
                    'survey_id' => $this->surveyId,
                    'responses' => $formattedResponses,
                ]);

                Log::info('Survey response saved', ['response_id' => $surveyResponse->id]);
                session()->flash('message', 'Survey response submitted successfully.');
                $this->responses = []; // Clear responses after successful submission
            });
        } catch (\Exception $e) {
            Log::error('Error saving survey response', ['error' => $e->getMessage()]);
            session()->flash('error', 'Error submitting survey response: ' . $e->getMessage());
        }
    }

    #[On('clearSurveyData')]
    public function clearSurveyData()
    {
        // Clear the survey data in the component
        $this->responses = [];
        // Add any other data that needs to be cleared

        // Dispatch an event to notify that the data has been cleared
        $this->dispatch('surveyDataCleared');
    }
}