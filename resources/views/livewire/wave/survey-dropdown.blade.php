<div>
    <div class="relative" x-data="{ open: @entangle('showDropdown') }">
        <button @click="open = !open" class="text-indigo-600 hover:text-indigo-900">
            Options
        </button>
        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
            <a href="#" wire:click.prevent="duplicateSurvey" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Duplicate</a>
            <a href="#" wire:click.prevent="confirmDelete" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Delete</a>
        </div>
    </div>

    @if($showDeleteConfirmation)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="delete-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Survey</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete this survey? This action cannot be undone.
                    </p>
                    <div class="mt-4">
                        <input type="text" wire:model.defer="deleteConfirmationText" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               placeholder="Type DELETE to confirm">
                        @error('deleteConfirmationText') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button wire:click="deleteSurvey" 
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Delete Survey
                    </button>
                    <button wire:click="cancelDelete" 
                            class="mt-3 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>