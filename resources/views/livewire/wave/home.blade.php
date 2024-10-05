<!-- Main Container -->
<div>
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <h1 class="text-3xl font-bold text-indigo-900 mb-8">Survey Dashboard</h1>

            <!-- Company Switcher -->
            <div class="mb-4">
                <label for="company-select">Select Company:</label>
                <select wire:model.live="selectedCompanyId">
                    @foreach($userCompanies as $company)
                        <option value="{{ $company->id }}">
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Folders Section -->
            <div class="bg-white shadow-sm rounded-lg mb-8">
                <!-- Folders Header -->
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center bg-indigo-50">
                    <h2 class="text-xl font-semibold text-indigo-900">Folders</h2>
                    <div class="flex items-center space-x-4">
                        <!-- Folder Search -->
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
                        
                        <!-- New Survey Button -->
                        <a href="{{ route('survey.create') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            New Survey
                        </a>
                        
                        <!-- New Folder Button -->
                        <button wire:click.stop="toggleFolderModal" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            New Folder
                        </button>
                        
                        <!-- Toggle Folder View Button -->
                        <button wire:click="toggleFolderView" class="p-2 text-indigo-600 hover:text-indigo-800 focus:outline-none" title="{{ $folderGridView ? 'List View' : 'Grid View' }}">
                            @if($folderGridView)
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            @endif
                        </button>
                    </div>
                </div>
                
             <!-- Folders Content -->
<div class="p-4">
    @if($folderGridView)
        <!-- Grid View -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <!-- All Surveys Folder -->
            <div wire:click="selectFolder(null, 'All Surveys')" class="relative group cursor-pointer">
                <svg class="w-full h-auto text-indigo-200 group-hover:text-indigo-300 transition-colors duration-200" viewBox="0 0 100 80" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 10C0 4.47715 4.47715 0 10 0H30L40 10H90C95.5229 10 100 14.4772 100 20V70C100 75.5228 95.5229 80 90 80H10C4.47715 80 0 75.5228 0 70V10Z"/>
                </svg>
                <div class="absolute inset-0 flex flex-col justify-center items-center p-2">
                    <p class="text-xs font-medium text-indigo-900 text-center">All Surveys</p>
                    <span class="text-xs text-indigo-600">{{ $totalCompanySurveys }} surveys</span>
                </div>
            </div>
            
            <!-- Individual Folders -->
            @foreach($folders as $folder)
            <div class="relative group cursor-pointer" x-data="{ open: false }">
                <div wire:click="selectFolder({{ $folder->id }}, '{{ $folder->name }}')" class="h-full">
                    <svg class="w-full h-auto text-indigo-200 group-hover:text-indigo-300 transition-colors duration-200" viewBox="0 0 100 80" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 10C0 4.47715 4.47715 0 10 0H30L40 10H90C95.5229 10 100 14.4772 100 20V70C100 75.5228 95.5229 80 90 80H10C4.47715 80 0 75.5228 0 70V10Z"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col justify-center items-center p-2">
                        <p class="text-xs font-medium text-indigo-900 text-center line-clamp-2" title="{{ $folder->name }}">
                            {{ Str::limit($folder->name, 20) }}
                        </p>
                        <span class="text-xs text-indigo-600">{{ $folder->surveys->count() }} surveys</span>
                    </div>
                </div>
                <div class="absolute bottom-0 right-0 mb-1 mr-1">
                    <button @click.stop="open = !open" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="origin-bottom-right absolute right-0 mb-6 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                            <button wire:click.stop="toggleFolderModal({{ $folder->id }})" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full text-left" role="menuitem">Edit</button>
                            <button wire:click.stop="confirmDeleteFolder({{ $folder->id }})" @click="open = false" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 hover:text-red-900 w-full text-left" role="menuitem">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
                        <!-- List View -->
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
                                        <button wire:click="toggleFolderModal({{ $folder->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <button wire:click="confirmDeleteFolder({{ $folder->id }})" class="ml-2 text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                
                <!-- Folders Pagination -->
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $folders->links() }}
                </div>
            </div>
            <div>
            <livewire:wave.folder-analytics />
        </div>
        



            <!-- Surveys Section -->
            <div class="bg-white shadow-sm rounded-lg">
                <!-- Surveys Header -->
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center bg-indigo-50">
                    <h2 class="text-xl font-semibold text-indigo-900">
                        @if($selectedFolder)
                            Surveys in "{{ $selectedFolder->name }}"
                        @else
                            All Surveys
                        @endif
                    </h2>
                    <div class="flex items-center space-x-4">
                        <!-- Survey Search -->
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
                        
                        <!-- New Survey Button -->
                        <a href="{{ route('survey.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            New Survey
                        </a>
                        
                        <!-- Toggle Survey View Button -->
                        <button wire:click="toggleSurveyView" class="p-2 text-indigo-600 hover:text-indigo-800 focus:outline-none" title="{{ $surveyGridView ? 'List View' : 'Grid View' }}">
                            @if($surveyGridView)
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            @endif
                        </button>
                    </div>
                </div>
                
                <!-- Surveys Content -->
                <div class="p-4">
                    @if($surveyGridView)
                        <!-- Grid View -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($folderSurveys as $survey)
                            <div wire:click="selectSurvey({{ $survey->id }})" class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition duration-150 ease-in-out p-4 flex flex-col h-48 relative">
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
                                        <span>{{ $survey->completionCount ?? 0 }} Responses</span>
                                    </div>
                                    <span>{{ $survey->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs text-gray-500 mb-2">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span>{{ $survey->view_count ?? 0 }} Visits</span>
                                    </div>
                                </div>
                                <div class="mt-auto flex justify-between items-center">
                                    <a href="{{ route('survey.show', $survey->id) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">View</a>
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click.stop="open = !open" class="text-gray-500 hover:text-gray-700">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                                <a href="{{ route('survey.edit', ['surveyId' => $survey->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Edit</a>
                                                <button wire:click="duplicateSurvey({{ $survey->id }})" class="block w-full text-left px-4 py-2 text-sm text-indigo-600 hover:bg-gray-100 hover:text-indigo-900" role="menuitem">Duplicate</button>
                                                <button wire:click="confirmDeleteSurvey({{ $survey->id }})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 hover:text-red-900" role="menuitem">Delete22</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <!-- List View -->
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
                                        <button wire:click="sortSurveys('view_count')" class="flex items-center">
                                            Visits
                                            @if($surveySortField === 'view_count')
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
                                <tr wire:click="selectSurvey({{ $survey->id }})" class="cursor-pointer hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $survey->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{  $survey->completionCount ?? 0 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{  $survey->view_count ?? 0 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $survey->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="{{ route('survey.edit', ['surveyId' => $survey->id]) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <button wire:click="duplicateSurvey({{ $survey->id }})" class="ml-2 text-indigo-600 hover:text-indigo-900">Duplicate</button>
                                        <button wire:click="confirmDeleteSurvey({{ $survey->id }})" class="ml-2 text-red-600 hover:text-red-900">Delete33</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                
                <!-- Surveys Pagination -->
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


      <!-- Survey Views Analytics -->
            @if($selectedSurveyId)
    <div class="mt-8 mx-8 bg-white shadow-sm rounded-lg">
        <div class="px-6 py-5 sm:px-8 border-b border-gray-200 flex justify-between items-center bg-indigo-50">
            <h2 class="text-xl font-semibold text-indigo-900">Survey Analytics</h2>
        </div>
        <div class="p-6">
            <livewire:wave.survey-views-analytics :survey-id="$selectedSurveyId" :key="'survey-'.$selectedSurveyId" />
        </div>
    </div>
@else
    <div class="mt-8 mx-8 bg-white shadow-sm rounded-lg">
        <div class="px-6 py-5 sm:px-8 border-b border-gray-200 flex justify-between items-center bg-indigo-50">
            <h2 class="text-xl font-semibold text-indigo-900">Survey Analytics</h2>
        </div>
        <div class="p-6">
            <div class="bg-gray-100 border border-gray-300 text-gray-700 px-6 py-4 rounded relative" role="alert">
                <p class="font-bold">No survey selected</p>
                <p class="text-sm">Please select a survey to view its analytics.</p>
            </div>
        </div>
    </div>
@endif



        


        @include('livewire.wave.home-modal')


<!-- Scripts -->
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('companyChanged', () => {
            // Refresh the component when company changes
            @this.$refresh();
        });
        @this.on('folderSelected', () => {
            // Refresh the component when a folder is selected
            @this.$refresh();
        });
        @this.on('folderCreated', () => {
            // Refresh the component when a new folder is created
            @this.$refresh();

            @this.on('closeDropdown', () => {
            // Close all dropdowns
            document.querySelectorAll('[x-data]').forEach((el) => {
                if (el.__x) {
                    el.__x.updateElements(el);
                }
            });
        });
    });

        });
    

    document.addEventListener('livewire:load', function () {
        Livewire.on('closeDropdown', function () {
            Alpine.store('dropdownOpen', false);
        });
    });



@push('scripts')

    document.addEventListener('livewire:initialized', () => {
        let scrollPosition = 0;

     

        Livewire.hook('commit', ({ component, finish }) => {
            finish(() => {
                if (scrollPosition > 0) {
                    window.scrollTo(0, scrollPosition);
                    scrollPosition = 0;
                }
            });
        });
    });

@endpush

@push('scripts')

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('refreshPage', () => {
            location.reload();
        });
    });

@endpush

@push('scripts')
    
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('reloadPage', () => {
                // Reload the page
                window.location.reload();
            });

            // Handle scrolling to survey analytics after page load
            if (window.location.hash === '#survey-analytics') {
                const analyticsSection = document.getElementById('survey-analytics-section');
                if (analyticsSection) {
                    analyticsSection.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });

    @endpush
    </script>