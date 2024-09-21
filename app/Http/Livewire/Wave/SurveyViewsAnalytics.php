<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\SurveyViewsAnalytics as SurveyViewsAnalyticsModel;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Pagination\LengthAwarePaginator;

class SurveyViewsAnalytics extends Component
{
    use WithPagination;

    public $surveyId;
    public $viewsData;
    public $startDate;
    public $endDate;
    public $perPage = 'all';
    public $dateRanges = [
        '7' => 'Last 7 days',
        '30' => 'Last 30 days',
        '90' => 'Last 90 days',
        'custom' => 'Custom range',
    ];
    public $selectedRange = '30';
    public $chartId;

    protected $queryString = ['startDate', 'endDate', 'perPage', 'selectedRange'];

    public function mount($surveyId = null)
    {
        $this->surveyId = $surveyId;
        $this->updateDateRange();
        $this->chartId = uniqid();
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

    private function updateDateRange()
    {
        if ($this->selectedRange !== 'custom') {
            $this->startDate = Carbon::now()->subDays($this->selectedRange)->format('Y-m-d');
            $this->endDate = Carbon::now()->format('Y-m-d');
        }
    }

    private function loadViewsData()
    {
        // If surveyId is null, load aggregate data for all surveys
        $query = SurveyViewsAnalyticsModel::select(DB::raw('DATE(viewed_at) as date'), DB::raw('count(*) as views'))
            ->whereBetween('viewed_at', [$this->startDate, $this->endDate]);

        if ($this->surveyId !== null) {
            $query->where('survey_id', $this->surveyId);
        }

        return $query->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('Y-m-d'),
                    'views' => (int)$item->views,
                ];
            });
    }

    public function render()
    {
        $this->viewsData = $this->loadViewsData();

        $lineChartModel = (new LineChartModel())
            ->setTitle($this->surveyId ? 'Survey Views' : 'All Surveys Views') // Dynamic title
            ->setAnimated(true)
            ->withoutLegend()
            ->setColors(['#6875f5'])
            ->setXAxisVisible(true)
            ->setYAxisVisible(true);

        foreach ($this->viewsData as $data) {
            $lineChartModel->addPoint($data['date'], $data['views'], ['date' => $data['date']]);
        }

        // Pagination
        if ($this->perPage !== 'all') {
            $page = $this->page;
            $perPage = (int)$this->perPage;
            $items = $this->viewsData->forPage($page, $perPage);
            $paginatedData = new LengthAwarePaginator(
                $items,
                $this->viewsData->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $paginatedData = $this->viewsData;
        }

        return view('livewire.wave.survey-views-analytics', [
            'viewsData' => $paginatedData,
            'lineChartModel' => $lineChartModel,
            'chartId' => $this->chartId,
        ]);
    }
}
