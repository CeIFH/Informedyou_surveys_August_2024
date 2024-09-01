<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\Log;

class BuildWithAi extends Component
{
    public $showModal = false;
    public $title = '';
    public $description = '';
    public $numberOfQuestions = 5;
    public $tone = 'neutral';
    public $generatedQuestions = [];
    public $message = '';
    public $error = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'numberOfQuestions' => 'required|integer|min:1|max:20',
        'tone' => 'required|string|in:neutral,friendly,formal',
    ];

    public function toggleModal()
    {
        $this->showModal = !$this->showModal;
    }

    public function generateSurvey()
    {
        $this->validate();

        try {
            $endpoint = config('services.azure_openai.endpoint');
            $apiKey = config('services.azure_openai.api_key');

            $jsonBody = json_encode([
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "You are a survey generation assistant. Create a survey based on the input provided."
                    ],
                    [
                        "role" => "user",
                        "content" => "Create a survey titled '{$this->title}' with {$this->numberOfQuestions} questions. The tone should be {$this->tone}. Description: {$this->description}. 
                        Provide the output as a JSON array of questions, where each question is an object with the following properties:
                        - 'question': The text of the question
                        - 'type': One of ['text', 'multiple_choice', 'checkbox', 'dropdown', 'textarea', 'email', 'phone', 'number', 'date', 'website', 'time', 'city', 'file', 'signature']
                        - 'options': An array of specific option texts for multiple_choice, checkbox, or dropdown types
                        - 'subheading': An optional subheading for the question
                        Ensure that the types are appropriate for the questions and that multi-choice questions have relevant options."
                    ]
                ],
                "temperature" => 0.7,
                "top_p" => 0.95,
                "max_tokens" => 1000
            ]);

            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'api-key: ' . $apiKey
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode !== 200) {
                throw new \Exception('API request failed with status code: ' . $httpCode . '. Response: ' . $response);
            }

            if (curl_errno($ch)) {
                throw new \Exception('cURL error: ' . curl_error($ch));
            }

            curl_close($ch);

            $data = json_decode($response, true);
            
            Log::info('API Response', ['response' => $data]);

            $content = $data['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                throw new \Exception('No content generated from AI. Full response: ' . json_encode($data));
            }

            $this->generatedQuestions = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from AI. Content: ' . $content);
            }

            $this->showModal = false;

            $this->dispatch('aiSurveyGenerated', [
                'title' => $this->title,
                'description' => $this->description,
                'questions' => $this->generatedQuestions,
            ]);

            $this->message = 'Survey generated successfully. Please review and save.';
        } catch (\Exception $e) {
            Log::error('Error generating survey with AI', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error = 'Error generating survey with AI: ' . $e->getMessage();
        }
    }

    public function clearMessages()
    {
        $this->message = '';
        $this->error = '';
    }

    public function render()
    {
        return view('livewire.wave.build-with-ai');
    }
}