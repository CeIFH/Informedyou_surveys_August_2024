

<div>
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-indigo-900 mb-8">Survey Dashboard</h1>

            <!-- Folders Section -->
            <div class="bg-white shadow-sm rounded-lg mb-8">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center bg-indigo-50">
                    <h2 class="text-xl font-semibold text-indigo-900">Folders</h2>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input 
                                wire:model.live="folderSearch" 
                                type="text" 
                                placeholder="Search folders" 
                                class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pl-10 pr-10 py-2"
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            @if($folderSearch)
                                <button wire:click="clearFolderSearch" type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        <a href="{{ route('survey.create') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            New Survey
                        </a>
                        <button wire:click.stop="toggleFolderModal" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            New Folder
                        </button>
                        <button wire:click="toggleFolderView" class="p-2 text-indigo-600 hover:text-indigo-800 focus:outline-none" title="{{ $folderGridView ? 'List View' : 'Grid View' }}">
                            @if($folderGridView)
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            @endif
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    @if($folderGridView)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            <div wire:click="selectFolder" class="relative group cursor-pointer">
                                <svg class="w-full h-auto text-indigo-200 group-hover:text-indigo-300 transition-colors duration-200" viewBox="0 0 100 80" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 10C0 4.47715 4.47715 0 10 0H30L40 10H90C95.5229 10 100 14.4772 100 20V70C100 75.5228 95.5229 80 90 80H10C4.47715 80 0 75.5228 0 70V10Z"/>
                                </svg>
                                <div class="absolute inset-0 flex flex-col justify-center items-center p-2">
                                    <p class="text-xs font-medium text-indigo-900 text-center">All Surveys</p>
                                    <span class="text-xs text-indigo-600">{{ $totalSurveys }} surveys</span>
                                </div>
                            </div>
                            @foreach($folders as $folder)
                            <div wire:click="selectFolder({{ $folder->id }})" class="relative group cursor-pointer">
                                <svg class="w-full h-auto text-indigo-200 group-hover:text-indigo-300 transition-colors duration-200" viewBox="0 0 100 80" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 10C0 4.47715 4.47715 0 10 0H30L40 10H90C95.5229 10 100 14.4772 100 20V70C100 75.5228 95.5229 80 90 80H10C4.47715 80 0 75.5228 0 70V10Z"/>
                                </svg>
                                <div class="absolute inset-0 flex flex-col justify-center items-center p-2">
                                    <p class="text-xs font-medium text-indigo-900 text-center line-clamp-2" title="{{ $folder->name }}">
                                        {{ Str::limit($folder->name, 20) }}
                                    </p>
                                    <span class="text-xs text-indigo-600">{{ $folder->surveys->count() }} surveys</span>
                                </div>
                                <div class="absolute inset-0 bg-white bg-opacity-90 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                                    <p class="text-xs font-medium text-indigo-900 text-center p-2">{{ $folder->name }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">
                                        <button wire:click="sortFolders('name')" class="flex items-center">
                                            Name
                                            @if($folderSortField === 'name')
                                                <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    @if($folderSortAsc)
                                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    @else
                                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">
                                        <button wire:click="sortFolders('surveys_count')" class="flex items-center">
                                            Surveys
                                            @if($folderSortField === 'surveys_count')
                                                <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    @if($folderSortAsc)
                                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    @else
                                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($folders as $folder)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $folder->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $folder->surveys->count() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button wire:click="editFolder({{ $folder->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <button wire:click="deleteFolder({{ $folder->id }})" class="ml-2 text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $folders->links() }}
                </div>
            </div>
 <!-- After the Surveys section -->
 <div class="mt-8">
        <livewire:wave.survey-views-analytics :survey-id="$selectedSurveyId" />
    </div>
</div>
            <!-- Surveys Section -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center bg-indigo-50">
                    <h2 class="text-xl font-semibold text-indigo-900">
                        @if($selectedFolder)
                            Surveys in "{{ $selectedFolder->name }}"
                        @else
                            All Surveys
                        @endif
                    </h2>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input 
                                wire:model.live="surveySearch" 
                                type="text" 
                                placeholder="Search surveys" 
                                class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pl-10 pr-10 py-2"
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            @if($surveySearch)
                                <button wire:click="clearSurveySearch" type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        <a href="{{ route('survey.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            New Survey
                        </a>
                        <button wire:click="toggleSurveyView" class="p-2 text-indigo-600 hover:text-indigo-800 focus:outline-none" title="{{ $surveyGridView ? 'List View' : 'Grid View' }}">
                            @if($surveyGridView)
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            @endif
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    @if($surveyGridView)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($folderSurveys as $survey)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition duration-150 ease-in-out p-4 flex flex-col h-48">
                                <h3 class="text-sm font-semibold text-indigo-900 mb-2 line-clamp-2" title="{{ $survey->title }}">{{ $survey->title }}</h3>
                                @if(!$selectedFolder && $survey->folder)
                                    <p class="text-xs text-indigo-600 mb-2 line-clamp-1" title="{{ $survey->folder->name }}">
                                        In: {{ $survey->folder->name }}
                                    </p>
                                @endif
                                <div class="flex justify-between items-center text-xs text-gray-500 mb-2">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        <span>{{ $survey->responses_count ?? 0 }} Responses</span>
                                    </div>
                                    <span>{{ $survey->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs text-gray-500 mb-2">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span>{{ $survey->page_visits ?? 0 }} Visits</span>
                                    </div>
                                </div>
                                <div class="mt-auto flex justify-between items-center">
                                    <a href="{{ route('survey.show', $survey->id) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">View</a>
                                    <a href="{{ route('survey.edit', $survey->id) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">Edit</a>
                                    <livewire:wave.survey-dropdown :survey="$survey" :key="'survey-dropdown-'.$survey->id" />
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else


                    
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">
                                        <button wire:click="sortSurveys('title')" class="flex items-center">
                                            Title
                                            @if($surveySortField === 'title')
                                                <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    @if($surveySortAsc)
                                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    @else
                                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">
                                        <button wire:click="sortSurveys('responses_count')" class="flex items-center">
                                            Responses
                                            @if($surveySortField === 'responses_count')
                                                <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    @if($surveySortAsc)
                                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    @else
                                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">
                                        <button wire:click="sortSurveys('created_at')" class="flex items-center">
                                            Created At
                                            @if($surveySortField === 'created_at')
                                                <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    @if($surveySortAsc)
                                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    @else
                                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($folderSurveys as $survey)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $survey->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $survey->responses_count ?? 0 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $survey->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="{{ route('survey.edit', $survey->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <livewire:wave.survey-dropdown :survey="$survey" :key="'survey-dropdown-'.$survey->id" />
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Showing {{ $folderSurveys->firstItem() }} to {{ $folderSurveys->lastItem() }} of {{ $folderSurveys->total() }} results
                    </div>
                    <div>
                        {{ $folderSurveys->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Folder Modal -->
        @if($showFolderModal)
        <div class="fixed inset-0 overflow-y-auto z-50">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="createFolder">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Create New Folder
                                    </h3>
                                    <div class="mt-2">
                                        <input wire:model.defer="newFolderName" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Folder Name">
                                        @error('newFolderName') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Create
                            </button>
                            <button wire:click.stop="toggleFolderModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>

   

