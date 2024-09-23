<div>
    <h2 class="text-2xl font-bold mb-4">Survey Analytics</h2>

    @if($surveys->isNotEmpty())
        <div class="mb-4">
            <label for="survey-select" class="block text-sm font-medium text-gray-700">Select Survey</label>
            <select id="survey-select" wire:model="selectedSurveyId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                @foreach($surveys as $survey)
                    <option value="{{ $survey->id }}">{{ $survey->title }}</option>
                @endforeach
            </select>
        </div>

        @if($selectedSurvey)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $selectedSurvey->title }}</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">View Count</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $selectedSurvey->view_count }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Folder</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $selectedSurvey->folder->name ?? 'No Folder' }}</dd>
                        </div>
                        <!-- Add more survey details as needed -->
                    </dl>
                </div>
            </div>
        @else
            <p class="text-gray-500">No survey selected.</p>
        @endif
    @else
        <p class="text-gray-500">No surveys available for analytics.</p>
    @endif
</div>
