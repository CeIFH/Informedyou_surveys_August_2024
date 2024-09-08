<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Survey;
use App\Models\SurveyResponse as SurveyResponseModel;
use Spatie\Browsershot\Browsershot;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use Livewire\Attributes\On;

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
    public $responseStartedAt;
    public $surveyToken;
    public $isActive;
    public $inactiveMessage;

    public function mount($id)
    {
        $this->survey = Survey::findOrFail($id);
        $this->isActive = $this->survey->is_active;
        $this->inactiveMessage = $this->survey->inactive_message;

        if ($this->isActive) {
            $questions = json_decode($this->survey->content, true);

            // Generate a unique token for this survey session
            $this->surveyToken = md5(uniqid(rand(), true));
            session(['survey_token' => $this->surveyToken]);

            $this->initializeResponses($questions);
        }
    }

    private function initializeResponses($questions)
    {
        $this->responses = [];
        $this->signatures = [];
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

    public function render()
    {
        if (!$this->isActive) {
            return view('livewire.wave.survey-inactive', [
                'survey' => $this->survey,
                'inactiveMessage' => $this->inactiveMessage,
            ]);
        }

        // Check if the survey token matches
        if (session('survey_token') !== $this->surveyToken) {
            $this->initializeResponses(json_decode($this->survey->content, true));
        }

        $this->debugSurveyResponseData();
        return view('livewire.wave.survey-response', [
            'survey' => $this->survey,
            'questions' => json_decode($this->survey->content, true),
            'completionMessages' => $this->survey->completionMessages,
            'isCompleted' => $this->isCompleted,
        ]);
    }

    public function updatedResponses($value, $key)
    {
        $this->recordResponseStartTime();
        $questions = json_decode($this->survey->content, true);

        if (!isset($questions[$key])) {
            Log::error("Invalid key: $key");
            return;
        }

        if ($questions[$key]['type'] == 'multiple_choice') {
            $this->responses[$key] = $value;
        } elseif ($questions[$key]['type'] == 'checkbox') {
            $options = $questions[$key]['options'];
            $this->responses[$key] = [];
            foreach ($value as $index => $selected) {
                if ($selected) {
                    $this->responses[$key][$options[$index]] = true;
                }
            }
        } else {
            $this->responses[$key] = $value;
        }

        Log::info("Response updated: Key: $key, Value: " . json_encode($this->responses[$key]));
    }

    private function recordResponseStartTime()
    {
        if (!$this->responseStartedAt) {
            $this->responseStartedAt = now();
            Log::info('Response started at: ' . $this->responseStartedAt);
        }
    }

    private function calculateResponseDuration()
    {
        if ($this->responseStartedAt) {
            $responseCompletedAt = now();
            $duration = $responseCompletedAt->diffInSeconds($this->responseStartedAt);
            Log::info('Response duration: ' . $duration . ' seconds');
            return $duration;
        }
        return null;
    }

    private function isSurveyFullyCompleted()
    {
        $questions = json_decode($this->survey->content, true);
        $isFullyCompleted = true;

        foreach ($questions as $index => $question) {
            $hasResponse = false;

            if ($question['type'] === 'signature') {
                $hasResponse = isset($this->signatures[$index]) && !empty($this->signatures[$index]);
            } elseif ($question['type'] === 'checkbox') {
                $hasResponse = isset($this->responses[$index]) && !empty(array_filter($this->responses[$index]));
            } else {
                $hasResponse = isset($this->responses[$index]) && $this->responses[$index] !== '';
            }

            if (!$hasResponse) {
                $isFullyCompleted = false;
                break;
            }
        }

        Log::info('Survey completion status', ['isFullyCompleted' => $isFullyCompleted]);
        return $isFullyCompleted;
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

        foreach ($questions as $index => $question) {
            $questionText = $question['question'];
            if (isset($this->responses[$index])) {
                if (is_array($this->responses[$index])) {
                    // For checkbox questions, only include selected options
                    $formattedResponses[$questionText] = array_filter($this->responses[$index], function($value) {
                        return $value === true;
                    });
                } else {
                    $formattedResponses[$questionText] = $this->responses[$index];
                }
            }
        }

        foreach ($this->signatures as $index => $signature) {
            if ($signature) {
                $signatureData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signature));
                $signaturePath = 'signatures/' . uniqid() . '.jpg';
                Storage::disk('public')->put($signaturePath, $signatureData);
                $encryptedSignaturePath = Crypt::encrypt($signaturePath);
                $formattedResponses[$questions[$index]['question']] = $encryptedSignaturePath;
            }
        }

        try {
            DB::beginTransaction();

            $ipAddress = $this->getIpAddress();
            $geolocation = $this->getGeolocation($ipAddress);
            $deviceInfo = $this->getDeviceInfo();
            $responseCompletedAt = now();
            $responseDuration = $this->calculateResponseDuration();
            $isFullyCompleted = $this->isSurveyFullyCompleted();

            $data = [
                'survey_id' => $this->survey->id,
                'responses' => json_encode($formattedResponses),
                'ip_address' => $ipAddress,
                'geolocation' => json_encode($geolocation),
                'device_type' => $deviceInfo['device_type'],
                'browser_type' => $deviceInfo['browser_type'],
                'device_os' => $deviceInfo['device_os'],
                'response_started_at' => $this->responseStartedAt,
                'response_completed_at' => $responseCompletedAt,
                'response_duration' => $responseDuration,
                'is_completed' => $isFullyCompleted,
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

            // Clear the survey data
            $this->clearSurveyData();

            // Clear the survey in progress flag
            session()->forget('survey_token');

            // Redirect to the completion page
            return redirect()->route('survey.completion', ['survey' => $this->survey, 'response' => $this->surveyResponse]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving survey response', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            session()->flash('error', 'There was an error submitting your survey. Please try again.');
        }
        Log::info('Submit method called');
    }

    private function getIpAddress()
    {
        $ipAddress = request()->ip();

        // If you're behind a proxy or load balancer, you might need to use:
        // $ipAddress = request()->header('X-Forwarded-For') ?? request()->ip();

        Log::info('IP Address captured', ['ip' => $ipAddress]);

        return $ipAddress;
    }

    private function getGeolocation($ipAddress)
    {
        try {
            $response = Http::get("https://ipapi.co/{$ipAddress}/json/");

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Geolocation data retrieved', ['data' => $data]);
                return [
                    'country' => $data['country_name'] ?? null,
                    'region' => $data['region'] ?? null,
                    'city' => $data['city'] ?? null,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                ];
            } else {
                Log::warning('Failed to retrieve geolocation data', ['status' => $response->status()]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving geolocation data', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function getDeviceInfo()
    {
        $agent = new Agent();

        $deviceType = $agent->isDesktop() ? 'desktop' : ($agent->isTablet() ? 'tablet' : ($agent->isMobile() ? 'mobile' : 'other'));
        $browserType = $agent->browser();
        $deviceOs = $agent->platform();

        Log::info('Device info captured', [
            'device_type' => $deviceType,
            'browser_type' => $browserType,
            'device_os' => $deviceOs,
        ]);

        return [
            'device_type' => $deviceType,
            'browser_type' => $browserType,
            'device_os' => $deviceOs,
        ];
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

        foreach ($questions as $question) {
            $questionText = $question['question'];
            $html .= '<h3>' . $questionText . '</h3>';

            if (isset($responses[$questionText])) {
                $response = $responses[$questionText];
                if (is_array($response)) {
                    $html .= '<ul>';
                    foreach ($response as $item) {
                        $html .= '<li>' . htmlspecialchars($item) . '</li>';
                    }
                    $html .= '</ul>';
                } else {
                    if ($question['type'] === 'signature') {
                        try {
                            $decryptedPath = Crypt::decrypt($response);
                            $fullPath = storage_path('app/public/' . $decryptedPath);
                            if (file_exists($fullPath)) {
                                $base64Image = base64_encode(file_get_contents($fullPath));
                                $html .= '<img src="data:image/jpeg;base64,' . $base64Image . '" alt="Signature" style="max-width: 300px; max-height: 100px;">';
                            } else {
                                $html .= '<p>[Signature file not found]</p>';
                            }
                        } catch (\Exception $e) {
                            $html .= '<p>[Error displaying signature]</p>';
                            Log::error('Error displaying signature', ['error' => $e->getMessage()]);
                        }
                    } else {
                        $html .= '<p>' . htmlspecialchars($response) . '</p>';
                    }
                }
            } else {
                $html .= '<p>No response provided</p>';
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

    #[On('clearSurveyData')]
    public function clearSurveyData()
    {
        Log::info('Clearing survey data');
        $questions = json_decode($this->survey->content, true);
        $this->responses = [];
        $this->signatures = [];
        foreach ($questions as $index => $question) {
            if ($question['type'] == 'checkbox') {
                $this->responses[$index] = [];
            } elseif ($question['type'] == 'signature') {
                $this->signatures[$index] = null;
            } else {
                $this->responses[$index] = '';
            }
        }
        $this->responseStartedAt = null;
        $this->isCompleted = false;
        $this->surveyToken = md5(uniqid(rand(), true));
        session(['survey_token' => $this->surveyToken]);
        Log::info('Survey data cleared', ['responses' => $this->responses, 'signatures' => $this->signatures]);

        // Dispatch event to clear client-side data
        $this->dispatch('surveyDataCleared');
    }
}
