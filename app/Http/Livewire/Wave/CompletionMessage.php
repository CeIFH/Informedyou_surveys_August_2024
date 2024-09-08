<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;
use Illuminate\Support\Facades\Log;

class CompletionMessage extends Component
{
    public $survey;
    public $messageTitle;
    public $messageContent;
    public $redirectUrl;
    public $redirectType;
    public $redirectDelay;
    public $surveyTitle;

    public function mount(Survey $survey)
    {
        $this->survey = $survey;
        $this->getCompletionMessage();
        $this->redirectUrl = $survey->redirect_url ?: route('survey.show', $survey->id);
        $this->redirectType = $survey->redirect_type;
        $this->redirectDelay = $survey->redirect_delay;
        $this->surveyTitle = $survey->title;

        Log::info('Completion message loaded with redirect settings', [
            'survey_id' => $survey->id,
            'survey_title' => $this->surveyTitle,
            'message_title' => $this->messageTitle,
            'redirect_url' => $this->redirectUrl,
            'redirect_type' => $this->redirectType,
            'redirect_delay' => $this->redirectDelay,
        ]);
    }

    private function getCompletionMessage()
    {
        $defaultMessage = $this->survey->completionMessages->where('is_default', true)->first();
        if ($defaultMessage) {
            $this->messageTitle = $defaultMessage->title;
            $this->messageContent = $defaultMessage->content;
        } else {
            $this->messageTitle = 'Thank You';
            $this->messageContent = 'Thank you for completing the survey!';
        }
    }

    public function render()
    {
        return view('livewire.wave.completion-message');
    }
}
