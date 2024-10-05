<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SurveyViewsAnalytics as SurveyViewsAnalyticsModel;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Illuminate\Support\Collection;

class SurveyViewsAnalytics extends Component
{
    use WithPagination;

    public $surveyId;
    public $viewsData;
    public $startDate;
    public $endDate;
    public $perPage = 20;
    public $dateRanges = [
        '7' => 'Last 7 days',
        '30' => 'Last 30 days',
        '90' => 'Last 90 days',
        'custom' => 'Custom range',
    ];
    public $selectedRange = '30';
    public $chartId;
    public $surveyQuestions = [];
    public $error = null;
    public $chartTypes = [];
    public $surveyTitle = '';
    public $isDataTableCollapsed = false;

    protected $queryString = ['startDate', 'endDate', 'perPage', 'selectedRange'];

    public function mount($surveyId = null)
    {
        $this->surveyId = $surveyId;
        $this->updateDateRange();
        $this->chartId = uniqid();
        if ($this->surveyId) {
            $this->loadSurveyData();
        }
    }

    #[On('surveySelected')]
    public function updateSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
        $this->loadSurveyData();
        $this->resetPage();
    }

    private function loadSurveyData()
    {
        $this->loadSurveyQuestions();
        $this->error = null;
        if ($this->surveyId) {
            $survey = Survey::find($this->surveyId);
            $this->surveyTitle = $survey ? $survey->title : '';
            $this->updateDateRange(); // Ensure date range is set
            $this->chartId = uniqid(); // Generate new chart ID to force refresh
        }
    }

    private function loadSurveyQuestions()
    {
        try {
            $survey = Survey::findOrFail($this->surveyId);
            $this->surveyQuestions = new Collection(json_decode($survey->content, true) ?? []);
        } catch (\Exception $e) {
            Log::error('Error loading survey questions: ' . $e->getMessage());
            $this->error = 'Unable to load survey questions. Please check if the survey exists.';
            $this->surveyQuestions = new Collection();
        }
    }

    public function getSurveyResponsesProperty()
    {
        return SurveyResponse::where('survey_id', $this->surveyId)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function updateResponse($responseId, $questionIndex, $newValue)
    {
        $response = SurveyResponse::findOrFail($responseId);
        $responses = json_decode($response->responses, true);
        $responses[$this->surveyQuestions[$questionIndex]['question']] = $newValue;
        $response->responses = json_encode($responses);
        $response->save();

        session()->flash('message', 'Response updated successfully.');
    }

    public function updatedSelectedRange($value)
    {
        $this->updateDateRange();
        $this->resetPage();
        $this->chartId = uniqid();
    }

    public function updatedStartDate()
    {
        $this->selectedRange = 'custom';
        $this->resetPage();
        $this->chartId = uniqid();
    }

    public function updatedEndDate()
    {
        $this->selectedRange = 'custom';
        $this->resetPage();
        $this->chartId = uniqid();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
        $this->chartId = uniqid();
    }

    public function toggleDataTableCollapse()
    {
        $this->isDataTableCollapsed = !$this->isDataTableCollapsed;
    }

    private function updateDateRange()
    {
        if ($this->selectedRange !== 'custom') {
            $this->startDate = Carbon::now()->subDays($this->selectedRange)->format('Y-m-d');
            $this->endDate = Carbon::now()->format('Y-m-d');
        }
    }

    private function loadViewsData()
    {
        $query = SurveyViewsAnalyticsModel::select(DB::raw('DATE(viewed_at) as date'), DB::raw('TIME(viewed_at) as time'), DB::raw('count(*) as views'))
            ->whereBetween('viewed_at', [$this->startDate, $this->endDate]);

        if ($this->surveyId !== null) {
            $query->where('survey_id', $this->surveyId);
        }

        $query = $query->groupBy('date', 'time')
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc');

        $result = $query->get()->map(function ($item) {
            return [
                'date' => Carbon::parse($item->date)->format('Y-m-d'),
                'time' => $item->time,
                'views' => (int)$item->views,
            ];
        });

        return $result;
    }

    private function getViewsChartModel()
    {
        $viewsData = $this->loadViewsData();

        $lineChartModel = (new LineChartModel())
            ->setTitle('Survey Views Over Time')
            ->setAnimated(true)
            ->withoutLegend()
            ->setColors(['#4299E1'])
            ->setXAxisVisible(true)
            ->setYAxisVisible(true);

        foreach ($viewsData as $data) {
            $dateTime = Carbon::parse($data['date'] . ' ' . $data['time']);
            $lineChartModel->addPoint($dateTime->format('M d H:i'), $data['views'], ['date' => $data['date'], 'time' => $data['time']]);
        }

        return $lineChartModel;
    }

    private function getResponsesChartModel()
    {
        $responsesData = $this->loadResponsesData();

        $lineChartModel = (new LineChartModel())
            ->setTitle('Survey Responses Over Time')
            ->setAnimated(true)
            ->withoutLegend()
            ->setColors(['#48BB78'])
            ->setXAxisVisible(true)
            ->setYAxisVisible(true);

        foreach ($responsesData as $data) {
            $lineChartModel->addPoint(Carbon::parse($data['date'])->format('M d'), $data['responses'], ['date' => $data['date']]);
        }

        return $lineChartModel;
    }

    private function loadResponsesData()
    {
        return SurveyResponse::where('survey_id', $this->surveyId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as responses'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'responses' => (int)$item->responses,
                ];
            });
    }

    private function getQuestionChartModels()
    {
        $chartModels = [];
        foreach ($this->surveyQuestions as $index => $question) {
            if (in_array($question['type'], ['multiple_choice', 'checkbox', 'dropdown'])) {
                $responses = $this->getQuestionResponses($question['question']);
                
                // Check if responses are valid
                if (empty($responses) || !is_array($responses)) {
                    $responses = ['No Data' => 1]; // Provide a default value
                }
                
                $colorCount = max(1, count($responses));
                $colors = $this->generateConsistentColors($colorCount);
                
                $chartModels[$index] = [
                    'pie' => $this->getPieChartModel($question['question'], $responses, $colors),
                    'bar' => $this->getBarChartModel($question['question'], $responses, $colors)
                ];
                
                if (!isset($this->chartTypes[$index])) {
                    $this->chartTypes[$index] = 'pie';
                }
            }
        }
        return $chartModels;
    }

    private function generateConsistentColors($count)
    {
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#FF6384', '#C9CBCF', '#7CFC00', '#00CED1',
            '#FF1493', '#1E90FF', '#32CD32', '#FF8C00', '#8A2BE2',
        ];

        $count = max(1, $count); // Ensure we always generate at least one color

        // If we need more colors than in our predefined array, generate them
        if ($count > count($colors)) {
            for ($i = count($colors); $i < $count; $i++) {
                $colors[] = $this->getRandomColor();
            }
        }

        // Shuffle the colors to ensure variety even with a small number of items
        shuffle($colors);

        return array_slice($colors, 0, $count);
    }

    private function getRandomColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    private function getQuestionResponses($question)
    {
        return SurveyResponse::where('survey_id', $this->surveyId)
            ->get()
            ->flatMap(function ($response) use ($question) {
                $responses = json_decode($response->responses, true);
                if (is_array($responses) && array_key_exists($question, $responses)) {
                    $answer = $responses[$question];
                    // Handle array responses (e.g., for checkbox questions)
                    if (is_array($answer)) {
                        return $answer;
                    }
                    // Handle scalar responses
                    return [$answer];
                }
                return [];
            })
            ->countBy(function ($item) {
                // Convert non-string items to string
                return is_scalar($item) ? (string)$item : json_encode($item);
            })
            ->toArray();
    }

    private function getPieChartModel($question, $responses, $colors)
    {
        $pieChartModel = (new PieChartModel())
            ->setTitle($question)
            ->setAnimated(true)
            ->withoutLegend()
            ->setDataLabelsEnabled(true);

        $total = array_sum(array_filter($responses, 'is_numeric'));
        
        foreach ($responses as $option => $count) {
            $value = is_numeric($count) ? $count : 0;
            $percentage = $total > 0 ? round(($value / $total) * 100, 1) : 0;
            $color = array_shift($colors) ?? $this->getRandomColor();
            $pieChartModel->addSlice("$option ($percentage%)", $value, $color);
            $colors[] = $color;
        }

        if ($total === 0) {
            $pieChartModel->addSlice('No Data', 1, $colors[0] ?? '#cccccc');
        }

        return $pieChartModel;
    }

    private function getBarChartModel($question, $responses, $colors)
    {
        $barChartModel = (new \Asantibanez\LivewireCharts\Models\ColumnChartModel())
            ->setTitle($question)
            ->setAnimated(true)
            ->withoutLegend()
            ->setXAxisVisible(true)
            ->setYAxisVisible(true)
            ->setDataLabelsEnabled(true);

        foreach ($responses as $option => $count) {
            $value = is_numeric($count) ? $count : 0;
            $color = array_shift($colors) ?? $this->getRandomColor();
            $barChartModel->addColumn((string)$option, $value, $color);
            $colors[] = $color;
        }

        if (empty($responses) || array_sum(array_filter($responses, 'is_numeric')) === 0) {
            $barChartModel->addColumn('No Data', 1, $colors[0] ?? '#cccccc');
        }

        return $barChartModel;
    }

    public function toggleChartType($index)
    {
        $this->chartTypes[$index] = $this->chartTypes[$index] === 'pie' ? 'bar' : 'pie';
        $this->dispatch('chartTypeChanged', $index);
    }

    private function getLegendData($chartModel, $type)
    {
        $data = [];
        if ($type === 'pie') {
            foreach ($chartModel->toArray()['data'] as $slice) {
                $data[] = [
                    'title' => $slice['title'],
                    'value' => $slice['value'],
                    'color' => $slice['color'],
                ];
            }
        } else {
            foreach ($chartModel->toArray()['data'] as $column) {
                $data[] = [
                    'title' => $column['title'],
                    'value' => $column['value'],
                    'color' => $column['color'],
                ];
            }
        }
        return $data;
    }

    public function render()
    {
        if (!$this->surveyId) {
            return view('livewire.wave.survey-views-analytics-empty');
        }

        if ($this->error) {
            return view('livewire.wave.survey-views-analytics-error', ['error' => $this->error]);
        }

        $viewsData = $this->loadViewsData();
        
        // Handle pagination
        $page = request()->get('page', 1);
        $perPage = $this->perPage === 'all' ? $viewsData->count() : $this->perPage;
        $paginatedViewsData = new LengthAwarePaginator(
            $viewsData->forPage($page, $perPage),
            $viewsData->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $viewsChartModel = $this->getViewsChartModel();
        $responsesChartModel = $this->getResponsesChartModel();
        $questionChartModels = $this->getQuestionChartModels();

        // Check if there are any valid chart models
        if (empty($questionChartModels)) {
            return view('livewire.wave.survey-views-analytics-no-data', [
                'message' => 'There are no valid questions or responses for this survey.',
                'surveyTitle' => $this->surveyTitle,
            ]);
        }

        // Prepare legend data for each chart
        $legendData = [];
        foreach ($questionChartModels as $index => $models) {
            if (!isset($this->chartTypes[$index])) {
                $this->chartTypes[$index] = 'pie';
            }
            $legendData[$index] = $this->getLegendData($models[$this->chartTypes[$index]], $this->chartTypes[$index]);
        }

        return view('livewire.wave.survey-views-analytics', [
            'viewsData' => $paginatedViewsData,
            'viewsChartModel' => $viewsChartModel,
            'responsesChartModel' => $responsesChartModel,
            'chartId' => $this->chartId,
            'surveyQuestions' => $this->surveyQuestions,
            'surveyResponses' => $this->getSurveyResponsesProperty(),
            'questionChartModels' => $questionChartModels,
            'chartTypes' => $this->chartTypes,
            'legendData' => $legendData,
            'surveyTitle' => $this->surveyTitle,
            'isDataTableCollapsed' => $this->isDataTableCollapsed,
        ]);
    }
}