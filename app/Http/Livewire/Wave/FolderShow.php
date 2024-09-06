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

    protected $rules = [
        'newFolderName' => 'required|string|max:255',
    ];

    public function mount($id)
    {
        $this->folder = Folder::findOrFail($id);
        $this->loadFolders();
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

        foreach ($viewsData as $datetime => $count) {
            $lineChartModel->addPoint(Carbon::parse($datetime)->format('H:i'), $count);
        }

        return [
            'chartModel' => $lineChartModel,
            'viewsData' => $viewsData
        ];
    }

    public function render()
    {
        $viewsChartData = $this->getViewsChartModel();

        return view('livewire.wave.folder-show', [
            'surveys' => $this->folder->surveys()->paginate(12),
            'viewsChartModel' => $viewsChartData['chartModel'],
            'viewsData' => $viewsChartData['viewsData']
        ]);
    }
}