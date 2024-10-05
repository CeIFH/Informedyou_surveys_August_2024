<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Illuminate\Support\Facades\Log;

class SurveyAnalytics extends Component
{
    public $surveys;
    public $selectedSurveyId;
    public $selectedSurvey;
    public $chartType = 'bar';
    public $chartData = [];
    public $chartTypes = [];
    public $chartId;

    public function mount()
    {
        $this->surveys = Survey::all(); // Or however you're fetching surveys
        if ($this->surveys->isNotEmpty()) {
            $this->selectSurvey($this->surveys->first()->id);
        }
        $this->chartId = uniqid(); // Generate a unique ID for charts
    }

    public function selectSurvey($surveyId)
    {
        $this->selectedSurveyId = $surveyId;
        $this->loadSurvey();
        $this->chartData = $this->getChartData();
        $this->chartTypes = array_fill(0, count($this->chartData), 'pie'); // Default to pie charts
    }

    public function loadSurvey()
    {
        if ($this->selectedSurveyId) {
            $this->selectedSurvey = Survey::with('responses')->find($this->selectedSurveyId);
            if ($this->selectedSurvey) {
                $this->selectedSurvey->incrementViewCount();
            }
        }
    }

    public function toggleChartType($index)
    {
        $this->chartTypes[$index] = $this->chartTypes[$index] === 'pie' ? 'bar' : 'pie';
        $this->chartId = uniqid(); // Regenerate chart ID to force re-render
    }

    private function getChartData()
    {
        if (!$this->selectedSurvey || !$this->selectedSurvey->responses) {
            return [];
        }

        $chartData = [];
        $surveyContent = $this->getSurveyContent();

        if (!is_array($surveyContent)) {
            return [];
        }

        foreach ($surveyContent as $questionIndex => $question) {
            if (isset($question['type']) && in_array($question['type'], ['multiple_choice', 'checkbox', 'dropdown'])) {
                $responses = $this->selectedSurvey->responses->pluck('responses')->filter()->values();
                $questionResponses = $responses->map(function ($response) use ($questionIndex) {
                    return $response[$questionIndex] ?? null;
                })->filter();
                
                $data = $questionResponses->groupBy(function ($item) {
                    return is_array($item) ? implode(', ', $item) : $item;
                })->map->count();

                $total = $data->sum();
                $pieChartModel = (new PieChartModel())
                    ->setTitle($question['text'] ?? "Question {$questionIndex}")
                    ->setAnimated(true)
                    ->withOnSliceClickEvent('onSliceClick')
                    ->setDataLabelsEnabled(true)
                    ->setDataLabelsFormat('{percentage:.0f}%');

                $slices = [];
                foreach ($data as $option => $count) {
                    $color = $this->getRandomColor();
                    $pieChartModel->addSlice($option, $count, $color);
                    $slices[] = [
                        'title' => $option,
                        'value' => $count,
                        'color' => $color,
                        'percentage' => round(($count / $total) * 100, 1)
                    ];
                }

                $chartData[$questionIndex] = [
                    'question' => $question['text'] ?? "Question {$questionIndex}",
                    'type' => $question['type'],
                    'pieChartModel' => $pieChartModel,
                    'slices' => $slices
                ];
            }
        }

        return $chartData;
    }

    private function getSurveyContent()
    {
        $content = $this->selectedSurvey->content;

        if (is_string($content)) {
            $decodedContent = json_decode($content, true);
            return $decodedContent ?: [];
        }

        return is_array($content) ? $content : [];
    }

    public function render()
    {
        return view('livewire.wave.survey-analytics', [
            'surveys' => $this->surveys,
            'selectedSurvey' => $this->selectedSurvey,
            'chartData' => $this->chartData,
        ]);
    }

    private function getRandomColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
