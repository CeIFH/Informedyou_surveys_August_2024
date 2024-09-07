<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\CompletionMessage as CompletionMessageModel;

class CompletionMessage extends Component
{
    public $survey;
    public $response;
    public $message;

    public function mount(Survey $survey, SurveyResponse $response)
    {
        $this->survey = $survey;
        $this->response = $response;
        $this->setCompletionMessage();
    }

    private function setCompletionMessage()
    {
        $completionMessage = $this->survey->completionMessages()
            ->where(function ($query) {
                $query->where('is_default', true)
                    ->orWhereRaw('? REGEXP `condition`', [$this->getResponseString()]);
            })
            ->first();

        if ($completionMessage) {
            $this->message = $this->parseMessage($completionMessage->content);
        } else {
            $this->message = "Thank you for completing the survey!";
        }
    }

    private function getResponseString()
    {
        // Convert the response data to a string for regex matching
        return json_encode($this->response->data);
    }

    private function parseMessage($message)
    {
        // Replace variables
        $message = preg_replace_callback('/\{(\w+)\}/', function($matches) {
            return $this->response->data[$matches[1]] ?? $matches[0];
        }, $message);

        // Evaluate calculations
        $message = preg_replace_callback('/\{\{(.+?)\}\}/', function($matches) {
            return $this->evaluateExpression($matches[1]);
        }, $message);

        return $message;
    }

    private function evaluateExpression($expression)
    {
        $expression = preg_replace_callback('/\{(\w+)\}/', function($matches) {
            return $this->response->data[$matches[1]] ?? 0;
        }, $expression);

        try {
            return eval("return $expression;");
        } catch (\Throwable $e) {
            return "Error in expression";
        }
    }

    public function render()
    {
        return view('livewire.wave.completion-message');
    }
}
