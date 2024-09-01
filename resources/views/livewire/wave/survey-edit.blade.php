not being used!!!
<div>
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- SECTION: Survey Title -->
        <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-8">Edit Survey</h1>
        <div class="flex justify-end">
    <button wire:click="goHome" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-2 2v7a2 2 0 01-2 2h-4a2 2 0 01-2-2v-7m6 0l2 2"></path>
        </svg>
        Home
    </button>
</div>

        <!-- SECTION: Survey Title and Import -->
        <div class="mb-8 sm:flex sm:items-center sm:justify-between">
            <!-- Survey Title Input -->
            <div class="w-full sm:w-1/2 mb-4 sm:mb-0">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Survey Title</label>
                <input type="text" id="title" wire:model="title" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <!-- Import Survey Form (optional for edit) -->
            <div class="w-full sm:w-1/2 sm:ml-4">
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
        </div>

        <!-- SECTION: Folder Selection -->
        <div class="mb-8">
            <label for="folder" class="block text-sm font-medium text-gray-700 mb-1">Select Folder</label>
            <div class="flex items-center">
                <select id="folder" wire:model="selectedFolder" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">No Folder</option>
                    @foreach($folders as $folder)
                        <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                    @endforeach
                </select>
                <button type="button" wire:click="toggleFolderModal" class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    New Folder
                </button>
            </div>
        </div>

        <!-- SECTION: Survey Questions -->
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
                        <div class="mb-4">
                            <label for="type-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select id="type-{{ $index }}" wire:model="questions.{{ $index }}.type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="text">Text</option>
                                <option value="radio">Multiple Choice</option>
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

                        <!-- Options (for multiple choice, checkbox, dropdown) -->
                        @if(in_array($question['type'], ['radio', 'checkbox', 'dropdown']))
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                                @foreach($question['options'] as $optionIndex => $option)
                                    <div class="flex items-center mb-2">
                                        <input type="text" wire:model="questions.{{ $index }}.options.{{ $optionIndex }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Option">
                                        <button type="button" wire:click="removeOption({{ $index }}, {{ $optionIndex }})" class="ml-2 text-red-500 hover:text-red-700">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addOption({{ $index }})" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Add Option
                                </button>
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

        <!-- SECTION: Save Survey Button -->
        <div class="mt-8 flex justify-end">
            <button type="button" wire:click="updateSurvey" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Save Changes
            </button>
        </div>
    </div>

    <!-- SECTION: Create Folder Modal -->
    @if($showFolderModal)
    <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Create New Folder
                    </h3>
                    <div class="mt-2">
                        <input type="text" wire:model="newFolderName" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Folder Name">
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="createFolder" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Create
                    </button>
                    <button type="button" wire:click="toggleFolderModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
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