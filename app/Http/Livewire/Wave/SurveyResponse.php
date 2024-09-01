<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Survey;
use App\Models\SurveyResponse as SurveyResponseModel;
use Spatie\Browsershot\Browsershot;

class SurveyResponse extends Component
{
    use WithFileUploads;

    public $survey;
    public $responses = [];
    public $signatures = [];
    public $surveyResponse;
    public $selectedCompletionMessageId;
    public $newCompletionMessage = '';
    public $isCompleted = false;

    public function mount($id)
    {
        $this->survey = Survey::findOrFail($id);
        $questions = json_decode($this->survey->content, true);

        foreach ($questions as $index => $question) {
            if ($question['type'] == 'checkbox') {
                $this->responses[$index] = [];
            } elseif ($question['type'] == 'signature') {
                $this->signatures[$index] = null;
            } else {
                $this->responses[$index] = '';
            }
        }
    }

    public function updatedResponses($value, $key)
    {
        Log::info("Response updated: Key: $key, Value: " . json_encode($value));
    }

    public function updatedSignatures($value, $key)
    {
        Log::info("Signature updated: Key: $key, Value: " . json_encode($value));
    }

    public function submit()
    {
        Log::debug('Responses before validation', $this->responses);
        $this->submitted = true;
        Log::info('Form submitted', ['submitted' => $this->submitted]);
        $rules = [
            'responses' => 'required|array|min:1',
        ];

        $questions = json_decode($this->survey->content, true);

        foreach ($questions as $index => $question) {
            if (isset($question['required']) && $question['required']) {
                if ($question['type'] == 'checkbox') {
                    $rules["responses.$index"] = 'array|min:1';
                } elseif ($question['type'] == 'signature') {
                    $rules["signatures.$index"] = 'required|string';
                } else {
                    $rules["responses.$index"] = 'required';
                }
            } else {
                $rules["responses.$index"] = 'nullable';
            }
        }

        $this->validate($rules);

        Log::debug('Responses after validation', $this->responses);

        $formattedResponses = [];

        foreach ($this->responses as $index => $response) {
            if (is_array($response)) {
                $formattedResponses[$index] = array_keys(array_filter($response));
            } else {
                $formattedResponses[$index] = $response;
            }
        }

        foreach ($this->signatures as $index => $signature) {
            if ($signature) {
                $signatureData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signature));
                $signaturePath = 'signatures/' . uniqid() . '.jpg';
                Storage::disk('public')->put($signaturePath, $signatureData);
                $encryptedSignaturePath = Crypt::encrypt($signaturePath);
                $formattedResponses["signature_$index"] = $encryptedSignaturePath;
            }
        }

        try {
            DB::beginTransaction();

            $data = [
                'survey_id' => $this->survey->id,
                'responses' => json_encode($formattedResponses),
            ];

            Log::debug('Data before database insertion', $data);

            $this->surveyResponse = SurveyResponseModel::create($data);

            if (!$this->surveyResponse) {
                throw new \Exception('Survey response could not be created.');
            }

            $this->saveSurveyPdf();

            DB::commit();

            session()->flash('message', 'Survey submitted successfully.');
            $this->isCompleted = true;

            Log::info('Survey response saved successfully', ['id' => $this->surveyResponse->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving survey response', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            session()->flash('error', 'There was an error submitting your survey. Please try again.');
        }
    }

    public function saveSurveyPdf()
    {
        $directory = storage_path('app/private/survey_responses');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    
        $pdfPath = $directory . '/' . $this->surveyResponse->id . '.pdf';
    
        Browsershot::html($this->generateHtmlForPdf($this->surveyResponse))
            ->setOption('landscape', true)
            ->save($pdfPath);
    
        Log::info('Survey PDF saved', ['path' => $pdfPath]);
    }

    public function generateHtmlForPdf($surveyResponse)
    {
        $html = '<h1>' . $this->survey->title . '</h1>';

        $responses = json_decode($surveyResponse->responses, true);
        $questions = json_decode($this->survey->content, true);

        foreach ($questions as $index => $question) {
            $html .= '<h3>' . $question['question'] . '</h3>';

            if (isset($responses[$index])) {
                if (is_array($responses[$index])) {
                    $html .= '<ul>';
                    foreach ($responses[$index] as $response) {
                        $html .= '<li>' . htmlspecialchars($response) . '</li>';
                    }
                    $html .= '</ul>';
                } else {
                    $html .= '<p>' . htmlspecialchars($responses[$index]) . '</p>';
                }
            }
        }

        return $html;
    }

    public function debugSurveyResponseData()
    {
        Log::info('Current survey response data', [
            'survey_id' => $this->survey->id,
            'responses' => $this->responses,
            'signatures' => $this->signatures,
        ]);
    }

    public function render()
    {
        $this->debugSurveyResponseData();
        return view('livewire.wave.survey-response', [
            'survey' => $this->survey,
            'questions' => json_decode($this->survey->content, true),
            'completionMessages' => $this->survey->completionMessages,
            'isCompleted' => $this->isCompleted,
        ]);
    }
}
