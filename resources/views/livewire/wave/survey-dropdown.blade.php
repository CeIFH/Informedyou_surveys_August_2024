<div class="relative inline-block text-left" x-data="{ open: false }">
    <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-1 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10 3a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM10 8.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM10 14a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
        </svg>
    </button>

    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50" style="display: none;">
        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
            <a href="{{ route('survey.edit', $survey->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Edit</a>
            <button wire:click="duplicateSurvey" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Duplicate</button>
            <button wire:click="deleteSurvey" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-100 hover:text-red-900" role="menuitem">Delete</button>
        </div>
    </div>
</div>

