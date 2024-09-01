<div>
    <!-- Button to trigger the modal -->
    <button wire:click="toggleFolderModal" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
         New Folder
    </button>

    <!-- Modal -->
    @if($showFolderModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black opacity-50 z-40"></div>

            <!-- Modal content -->
            <div class="bg-white p-6 rounded-lg shadow-lg w-1/3 z-50">
                <h2 class="text-lg font-semibold mb-4">Create New Folder</h2>

                @if (session()->has('message'))
                    <div class="text-green-500">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="createFolder">
                    <div class="mb-4">
                        <label for="folderName" class="block text-gray-700">Folder Name:</label>
                        <input type="text" id="folderName" wire:model="newFolderName" class="w-full p-2 border rounded-lg">
                        @error('newFolderName') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="button" wire:click="toggleFolderModal" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition mr-2">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
