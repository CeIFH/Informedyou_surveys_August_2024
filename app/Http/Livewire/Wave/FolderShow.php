<?php

namespace App\Http\Livewire\Wave;

use Livewire\Component;
use App\Models\Folder;
use App\Models\Survey;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Carbon\Carbon;

class FolderShow extends Component
{
    use WithPagination;

    public $folder;
    public $folders = [];
    public $showFolderModal = false;
    public $newFolderName = '';
    public $chartId;
    public $folderId;

    protected $rules = [
        'newFolderName' => 'required|string|max:255',
    ];

    public function mount($id = null)
    {
        $this->folderId = $id;
        $this->loadFolder();
        $this->loadFolders();
        $this->chartId = 'chart-' . uniqid();
    }

    public function loadFolder()
    {
        if ($this->folderId) {
            $this->folder = Folder::findOrFail($this->folderId);
        } else {
            // Handle the case when no folder ID is provided
            // For example, you could redirect to a default folder or show an error message
            $this->folder = null;
        }
    }

    public function loadFolders()
    {
        $this->folders = Folder::all();
    }

    public function toggleModal()
    {
        $this->showFolderModal = !$this->showFolderModal;
    }

    public function createFolder()
    {
        $this->validate();

        Folder::create(['name' => $this->newFolderName]);

        $this->newFolderName = '';
        $this->showFolderModal = false;
        $this->loadFolders();
        $this->dispatch('folderListUpdated');
    }

    #[On('folderListUpdated')]
    public function handleFolderListUpdated()
    {
        $this->loadFolders();
    }

    public function getViewsChartModel()
    {
        $surveys = $this->folder->surveys;
        $viewsData = [];

        foreach ($surveys as $survey) {
            $views = $survey->views()
                ->where('created_at', '>=', now()->subDay())
                ->orderBy('created_at')
                ->get()
                ->groupBy(function ($view) {
                    return $view->created_at->format('Y-m-d H:i');
                });

            foreach ($views as $datetime => $groupedViews) {
                $viewsData[$datetime] = ($viewsData[$datetime] ?? 0) + count($groupedViews);
            }
        }

        ksort($viewsData);

        $lineChartModel = (new LineChartModel())
            ->setTitle('Survey Views (Last 24 Hours)')
            ->setAnimated(true)
            ->withDataLabels();

        // Ensure there's at least one data point
        if (empty($viewsData)) {
            $lineChartModel->addPoint(now()->format('H:i'), 0);
        } else {
            foreach ($viewsData as $datetime => $count) {
                $lineChartModel->addPoint(Carbon::parse($datetime)->format('H:i'), $count);
            }
        }

        \Log::info('Views Chart Data', [
            'dataCount' => $lineChartModel->data()->count(),
            'firstPoint' => $lineChartModel->data()->first(),
        ]);

        return [
            'chartModel' => $lineChartModel,
            'viewsData' => $viewsData
        ];
    }

    public function render()
    {
        $viewsChartData = $this->folder ? $this->getViewsChartModel() : null;

        return view('livewire.wave.folder-show', [
            'surveys' => $this->folder ? $this->folder->surveys()->paginate(12) : collect(),
            'viewsChartModel' => $viewsChartData ? $viewsChartData['chartModel'] : null,
            'viewsData' => $viewsChartData ? $viewsChartData['viewsData'] : [],
            'chartId' => $this->chartId,
        ]);
    }
}