<div>
    <h2 class="text-2xl font-bold mb-4">Folder Analytics</h2>

    @if($folders->isNotEmpty())
        <div class="mb-4">
            <label for="folder-select" class="block text-sm font-medium text-gray-700">Select Folder</label>
            <select id="folder-select" wire:model="selectedFolderId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Select a folder</option>
                @foreach($folders as $folder)
                    <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                @endforeach
            </select>
        </div>

        @if($selectedFolder)
            <h3 class="text-xl font-semibold mb-2">{{ $selectedFolder->name }}</h3>
            @if($folderSurveys->isNotEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Survey Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">View Count</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($folderSurveys as $survey)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $survey->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $survey->view_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">No surveys found in this folder.</p>
            @endif
        @else
            <p class="text-gray-500">No folder selected or the selected folder doesn't exist.</p>
        @endif
    @else
        <p class="text-gray-500">No folders available for analytics.</p>
    @endif
</div>
