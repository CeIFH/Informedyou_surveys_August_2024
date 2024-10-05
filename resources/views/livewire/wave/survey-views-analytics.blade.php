<div id="survey-analytics-section">
    @if(!$surveyId)
        <div class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-3 rounded relative" role="alert">
            <p class="font-bold">No survey selected</p>
            <p class="text-sm">Please select a survey to view its analytics.</p>
        </div>
    @else
        <h2 class="text-2xl font-semibold mb-4">Survey Analytics: {{ $surveyTitle }}</h2>

        <!-- Date Range Selector -->
        <div class="mb-4 flex items-center space-x-4">
            <select wire:model.live="selectedRange" class="form-select">
                @foreach($dateRanges as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @if($selectedRange === 'custom')
                <input type="date" wire:model.live="startDate" class="form-input">
                <input type="date" wire:model.live="endDate" class="form-input">
            @endif
        </div>

        <!-- Charts Section -->
        <div class="mb-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Views Over Time</h3>
                    <div style="height: 400px;">
                        <livewire:livewire-line-chart
                            :line-chart-model="$viewsChartModel"
                        />
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Responses Over Time</h3>
                    <div style="height: 400px;">
                        <livewire:livewire-line-chart
                            :line-chart-model="$responsesChartModel"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Charts Section -->
        <div class="mb-8 space-y-8">
            @if($surveyQuestions instanceof \Illuminate\Support\Collection)
                @foreach($surveyQuestions->chunk(2) as $chunk)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($chunk as $index => $question)
                            @if(isset($questionChartModels[$index]))
                                <div class="bg-white p-4 rounded-lg shadow">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold">{{ $question['question'] }}</h3>
                                        <button wire:click="toggleChartType({{ $index }})" class="px-3 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Switch to {{ $chartTypes[$index] === 'pie' ? 'Bar' : 'Pie' }} Chart
                                        </button>
                                    </div>
                                    <div style="height: 400px;">
                                        @if($chartTypes[$index] === 'pie')
                                            <livewire:livewire-pie-chart
                                                :pie-chart-model="$questionChartModels[$index]['pie']"
                                                wire:key="pie-chart-{{ $index }}-{{ $chartId }}-{{ md5(json_encode($legendData[$index])) }}"
                                            />
                                        @else
                                            <livewire:livewire-column-chart
                                                :column-chart-model="$questionChartModels[$index]['bar']"
                                                wire:key="bar-chart-{{ $index }}-{{ $chartId }}-{{ md5(json_encode($legendData[$index])) }}"
                                            />
                                        @endif
                                    </div>
                                    <div class="mt-4">
                                        <h4 class="font-semibold mb-2">Legend:</h4>
                                        <div class="flex flex-wrap">
                                            @foreach($legendData[$index] as $item)
                                                <div class="flex items-center mr-4 mb-2">
                                                    <div class="w-4 h-4 mr-2" style="background-color: {{ $item['color'] }}"></div>
                                                    <span>{{ $item['title'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            @else
                <!-- ... (code for non-collection surveyQuestions remains the same) ... -->
            @endif
        </div>

        <!-- Survey Questions and Responses Table -->
        @if(count($surveyQuestions) > 0 && $this->surveyResponses->count() > 0)
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Survey Responses</h3>
                    <button wire:click="toggleDataTableCollapse" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        {{ $isDataTableCollapsed ? 'Expand' : 'Collapse' }} Table
                    </button>
                </div>
                @if(!$isDataTableCollapsed)
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white shadow-md rounded-lg">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-2">Response ID</th>
                                    @foreach($surveyQuestions as $question)
                                        <th class="px-4 py-2">{{ $question['question'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->surveyResponses as $response)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $response->id }}</td>
                                        @foreach($surveyQuestions as $index => $question)
                                            <td class="px-4 py-2">
                                                @php
                                                    $responses = json_decode($response->responses, true) ?? [];
                                                    $currentResponse = $responses[$question['question']] ?? '';
                                                @endphp
                                                @if($question['type'] === 'multiple_choice' || $question['type'] === 'checkbox' || $question['type'] === 'dropdown')
                                                    <select 
                                                        wire:change="updateResponse({{ $response->id }}, {{ $index }}, $event.target.value)" 
                                                        class="form-select w-full"
                                                        style="width: 150px; height: 45px; min-width: 150px; min-height: 45px;"
                                                    >
                                                        <option value="">Select an option</option>
                                                        @foreach($question['options'] ?? [] as $option)
                                                            <option value="{{ $option }}" {{ $currentResponse == $option ? 'selected' : '' }}>
                                                                {{ $option }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <textarea 
                                                        wire:change="updateResponse({{ $response->id }}, {{ $index }}, $event.target.value)" 
                                                        class="form-textarea w-full"
                                                        rows="2"
                                                        style="white-space: pre-wrap; word-wrap: break-word; width: 150px; height: 45px; min-width: 150px; min-height: 45px; resize: vertical;"
                                                    >{{ $currentResponse }}</textarea>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $this->surveyResponses->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="mb-8 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">No data available.</strong>
                <span class="block sm:inline">There are no questions or responses for this survey.</span>
            </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('chartTypeChanged', (index) => {
            Livewire.dispatch('$refresh');
        });
    });
</script>
@endpush