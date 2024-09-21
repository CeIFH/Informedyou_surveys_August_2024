<div class="bg-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">
                @if($isEditMode)
                    Edit Survey
                @else
                    Create Survey
                @endif
            </h1>
            <div class="flex space-x-4">
                <button wire:click="goHome" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-2 2v7a2 2 0 01-2 2h-4a2 2 0 01-2-2v-7m6 0l2 2"></path>
                    </svg>
                    Home
                </button>

                <!-- SECTION: Save Survey Button -->
                <button wire:click="saveSurvey" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    @if($isEditMode)
                        Update Survey
                    @else
                        Save Survey
                    @endif
                </button>
            </div>
        </div>

        <div class="flex justify-end mt-4">
            @if($isEditMode && isset($surveyId))
                <button onclick="window.open('{{ route('survey.show', $surveyId) }}', '_blank')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75a4.5 4.5 0 00-7.5 0M12 9.75a3 3 0 11-6 0 3 3 0 016 0zm9.75 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Preview Survey
                </button>
            @endif
        </div>

        <div>
            <livewire:wave.build-with-ai />
            <!-- Import Survey Form -->
            <form wire:submit.prevent="importSurvey" enctype="multipart/form-data" class="flex items-center">
                <input type="file" wire:model="file" class="sr-only" id="file-upload">
                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 mr-2">
                    <span class="px-3 py-2 border border-gray-300 rounded-md">Choose file</span>
                </label>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Import Survey
                </button>
            </form>
        </div>

        <!-- Main Content Section -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <!-- Survey Title and Folder Selection -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Survey Title</label>
                        <input type="text" id="title" wire:model="title" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="folder" class="block text-sm font-medium text-gray-700">Select Folder</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <select id="folder" wire:model="selectedFolderId" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded rounded-l-md sm:text-sm border-gray-300">
                                <option value="">No Folder</option>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                                @endforeach
                            </select>
                            <!-- create new folder button -->
                            <livewire:wave.folder-create />
                        </div>
                    </div>
                </div>

                <!-- Survey Questions Section -->
                <ul class="space-y-4">
                    @foreach($questions as $index => $question)
                        <div 
                            wire:dragover.prevent 
                            wire:drop.prevent="drop({{ $index }}, 'before')" 
                            class="dropzone"
                            style="height: 4px; background-color: #e5e7eb; transition: all 0.2s;">
                        </div>
                        
                        <li
                            draggable="true"
                            wire:dragstart="startDragging({{ $index }})"
                            wire:dragend="endDragging"
                            wire:dragover.prevent
                            wire:drop.prevent="drop({{ $index }})" 
                            class="draggable bg-white shadow overflow-hidden sm:rounded-lg"
                        >
                            <div class="px-4 py-5 sm:p-6">
                                <!-- Question Header -->
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Question {{ $index + 1 }}</h3>
                                    <div class="flex space-x-2">
                                        <button type="button" wire:click="toggleSubheading({{ $index }})" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Subheading
                                        </button>
                                        <button type="button" wire:click="toggleRequired({{ $index }})" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            {{ $question['required'] ? 'Optional' : 'Required' }}
                                        </button>
                                        <button type="button" wire:click="togglePoints({{ $index }})" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Points
                                        </button>
                                    </div>
                                </div>

                                <!-- Subheading Input (if enabled) -->
                                @if($question['show_subheading'])
                                    <div class="mb-4">
                                        <label for="subheading-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">Subheading</label>
                                        <input type="text" id="subheading-{{ $index }}" wire:model="questions.{{ $index }}.subheading" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Subheading (optional)">
                                    </div>
                                @endif

                                <!-- Question Input -->
                                <div class="mb-4">
                                    <label for="question-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                                    <input type="text" id="question-{{ $index }}" wire:model="questions.{{ $index }}.question" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Question">
                                </div>

                                <!-- Required Indicator -->
                                @if($question['required'])
                                    <p class="text-sm text-red-500 mb-4">This question is required</p>
                                @endif

                                <!-- Question Type Selector -->
                                <div class="mb-2">
                                    <label class="block text-sm font-medium text-gray-700">Question Type</label>
                                    <select wire:model="questions.{{ $index }}.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="text">Text</option>
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="dropdown">Dropdown</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="email">Email</option>
                                        <option value="phone">Phone</option>
                                        <option value="number">Number</option>
                                        <option value="date">Date</option>
                                        <option value="website">Website</option>
                                        <option value="time">Time</option>
                                        <option value="city">City</option>
                                        <option value="file">File</option>
                                        <option value="signature">Signature</option>
                                    </select>
                                </div>

                                @if(in_array($question['type'], ['multiple_choice', 'checkbox', 'dropdown']))
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-gray-700">Options</label>
                                        @foreach($question['options'] as $optionIndex => $option)
                                            <div class="flex items-center mt-1">
                                                <input type="text" wire:model="questions.{{ $index }}.options.{{ $optionIndex }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                <button type="button" wire:click="removeOption({{ $index }}, {{ $optionIndex }})" class="ml-2 text-red-500 hover:text-red-700">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                        <button type="button" wire:click="addOption({{ $index }})" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Add Option</button>
                                    </div>
                                @endif

                                <!-- Points Input (if enabled) -->
                                @if($question['show_points'])
                                    <div class="mb-4">
                                        <label for="points-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                                        <input type="number" id="points-{{ $index }}" wire:model="questions.{{ $index }}.points" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Points (optional)">
                                    </div>
                                @endif

                                <!-- Remove Question Button -->
                                <button type="button" wire:click="removeQuestion({{ $index }})" class="text-red-500 hover:text-red-700">Remove Question</button>
                            </div>
                        </li>

                        <div 
                            wire:dragover.prevent 
                            wire:drop.prevent="drop({{ $index }}, 'after')" 
                            class="dropzone"
                            style="height: 4px; background-color: #e5e7eb; transition: all 0.2s;">
                        </div>
                    @endforeach
                </ul>

                <!-- SECTION: Add Question Button -->
                <div class="mt-8 flex justify-between">
                    <button type="button" wire:click="addQuestion" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Add Question
                    </button>
                </div>

                <!-- SECTION: Completion Messages Configuration -->
                <div class="mt-8">
                    <h2 class="text-lg font-medium text-gray-900">Completion Messages</h2>
                    
                    @foreach($completionMessages as $index => $message)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" wire:model="completionMessages.{{ $index }}.title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Content</label>
                                <textarea wire:model="completionMessages.{{ $index }}.content" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Condition (optional)</label>
                                <input type="text" wire:model="completionMessages.{{ $index }}.condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            
                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="radio" wire:model="defaultMessageIndex" value="{{ $index }}" class="rounded-full border-gray-300 text-indigo-600 shadow-sm">
                                    <span class="ml-2 text-sm text-gray-600">Set as default message</span>
                                </label>
                            </div> 
                            
                            @if($index > 0)
                                <button type="button" wire:click="removeCompletionMessage({{ $index }})" class="text-red-600 hover:text-red-800">Remove</button>
                            @endif
                        </div>
                    @endforeach
                    
                    <button type="button" wire:click="addCompletionMessage" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Add Completion Message
                    </button>
                </div>

                <!-- New Redirect Options -->
                <div class="mt-8" x-data="{ showDelay: @entangle('redirectType').defer === 'automatic' }">
                    <h3 class="text-lg font-medium text-gray-900">Redirect Options</h3>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Redirect URL</label>
                        <input type="url" wire:model.defer="redirectUrl" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="https://example.com or leave blank to return to survey page">
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Redirect Type</label>
                        <select wire:model="redirectType" x-on:change="showDelay = $event.target.value === 'automatic'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="button">Button Press</option>
                            <option value="automatic">Automatic</option>
                        </select>
                    </div>

                    <div x-show="showDelay" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Redirect Delay (seconds)</label>
                        <input type="number" wire:model.defer="redirectDelay" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <!-- Survey Options Section -->
                <div class="mt-8" x-data="{ isActive: @entangle('isActive') }">
                    <h2 class="text-lg font-medium text-gray-900">Survey Options</h2>
                    
                    <!-- Active Status Toggle -->
                    <div class="mt-4 flex items-center">
                        <span class="mr-3 text-sm font-medium text-gray-700">Survey Active</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="isActive" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Inactive Message (only shown when survey is inactive) -->
                    <div class="mt-4" x-show="!isActive">
                        <label for="inactiveMessage" class="block text-sm font-medium text-gray-700">Message for Inactive Survey</label>
                        <textarea id="inactiveMessage" wire:model="inactiveMessage" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="This survey is currently inactive."></textarea>
                    </div>
                </div>

                <!-- SECTION: Save Survey Button -->
                <div class="mt-8 flex justify-end">
                    <button type="button" wire:click="saveSurvey" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white {{ $isEditMode ? 'bg-green-600 hover:bg-green-700' : 'bg-indigo-600 hover:bg-indigo-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ $isEditMode ? 'Save Update' : 'Save Survey' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <style>
        .draggable.dragging {
            opacity: 0.5;
        }

        .dropzone {
            transition: all 0.2s ease-in-out;
        }

        .dropzone.over {
            height: 20px;
            background-color: #d1e7dd;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropzones = document.querySelectorAll('.dropzone');
            const draggables = document.querySelectorAll('.draggable');

            draggables.forEach(draggable => {
                draggable.addEventListener('dragstart', () => {
                    draggable.classList.add('dragging');
                });

                draggable.addEventListener('dragend', () => {
                    draggable.classList.remove('dragging');
                });
            });

            dropzones.forEach(dropzone => {
                dropzone.addEventListener('dragover', () => {
                    dropzone.classList.add('over');
                });

                dropzone.addEventListener('dragleave', () => {
                    dropzone.classList.remove('over');
                });

                dropzone.addEventListener('drop', () => {
                    dropzone.classList.remove('over');
                });
            });
        });
    </script>
</div>