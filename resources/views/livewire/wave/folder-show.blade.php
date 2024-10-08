<div>
    @if($folder)
        <h1 class="text-3xl font-bold mb-6">{{ $folder->name }}</h1>

        <!-- Folders Section -->
        <div class="mb-8 relative">
            <h2 class="text-xl font-semibold mb-4">Folders</h2>
            <div class="flex items-start">
                <!-- Slide-out Folders -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" class="absolute left-0 mt-2 w-64 bg-white shadow-lg rounded-lg overflow-hidden z-20">
                        <div class="p-4">
                            @foreach($folders as $f)
                                <a href="{{ route('folder.show', $f->id) }}" class="flex items-center justify-between p-2 hover:bg-gray-100 transition duration-150 ease-in-out {{ $folder->id === $f->id ? 'bg-indigo-100' : '' }}">
                                    <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">{{ $f->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $f->surveys->count() }} surveys</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Current Folder Highlight -->
                <div class="ml-4 flex-grow bg-white shadow rounded-lg p-6 ring-2 ring-indigo-500">
                    <h3 class="text-lg font-semibold mb-4">Analytics for {{ $folder->name }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Placeholder for Drag-and-Drop Charts -->
                        <div class="bg-gray-100 rounded-lg p-4 cursor-move" draggable="true">
                            <h4 class="font-medium text-gray-700">Survey Response Rate</h4>
                            <div class="h-32 bg-blue-200 rounded mt-2"></div>
                        </div>
                        <div class="bg-gray-100 rounded-lg p-4 cursor-move" draggable="true">
                            <h4 class="font-medium text-gray-700">Completion Time Analysis</h4>
                            <div class="h-32 bg-green-200 rounded mt-2"></div>
                        </div>
                        <div class="bg-gray-100 rounded-lg p-4 cursor-move" draggable="true">
                            <h4 class="font-medium text-gray-700">Survey Participation</h4>
                            <div class="h-32 bg-yellow-200 rounded mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Surveys in this folder -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($surveys as $survey)
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2">{{ $survey->title }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($survey->description, 100) }}</p>
                        <a href="{{ route('survey.show', $survey->id) }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">View Survey</a>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $surveys->links() }}

        <!-- Create Folder Modal -->
        @if($showFolderModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="my-modal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Create New Folder</h3>
                    <div class="mt-2 px-7 py-3">
                        <input type="text" wire:model.defer="newFolderName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Folder Name">
                        @error('newFolderName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="items-center px-4 py-3">
                        <button wire:click="createFolder" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                            Create
                        </button>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button wire:click="toggleModal" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- New Survey Views Chart Section -->
        @if($viewsChartModel)
            <div class="mt-8">
                <h2 class="text-2xl font-semibold mb-4">Survey Views Chart</h2>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div x-data="{ chartRendered: false }" x-init="
                        if (!chartRendered) {
                            $nextTick(() => {
                                var chartData = {{ json_encode($viewsChartModel->reactiveData()) }};
                                console.log('Chart Data:', chartData);
                                if (chartData && chartData.datasets && chartData.datasets.length > 0) {
                                    try {
                                        new ApexCharts(document.querySelector('#chart-{{ $chartId }}'), chartData).render();
                                        chartRendered = true;
                                    } catch (error) {
                                        console.error('Error rendering chart:', error);
                                        document.querySelector('#chart-{{ $chartId }}').innerHTML = 'Error rendering chart. Please check console for details.';
                                    }
                                } else {
                                    console.error('Invalid chart data:', chartData);
                                    document.querySelector('#chart-{{ $chartId }}').innerHTML = 'Invalid chart data.';
                                }
                            });
                        }
                    ">
                        <div id="chart-{{ $chartId }}" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Survey Table -->
        <div class="mt-8">
            <h2 class="text-2xl font-semibold mb-4">Surveys in {{ $folder->name }}</h2>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responses</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($surveys as $survey)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $survey->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $survey->responses_count ?? 0 }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $survey->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('survey.show', $survey->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $surveys->links() }}
            </div>
        </div>

        <!-- New Survey Views Table and Chart Section -->
        @if($viewsData)
            <div class="mt-12">
                <h2 class="text-2xl font-semibold mb-4">Survey Views Analysis</h2>
                
                <!-- Views Table -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($viewsData as $datetime => $count)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($datetime)->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $count }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Views Chart -->
                <div class="bg-white shadow rounded-lg p-6">
                    <livewire:livewire-line-chart
                        key="{{ $viewsChartModel->reactiveKey() }}"
                        :line-chart-model="$viewsChartModel"
                    />
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <h2 class="text-2xl font-semibold text-gray-700">No folder selected</h2>
            <p class="mt-2 text-gray-500">Please select a folder to view its contents.</p>
        </div>
    @endif
</div>

