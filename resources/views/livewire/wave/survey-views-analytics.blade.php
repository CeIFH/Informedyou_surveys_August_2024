<div>
    <h2 class="text-2xl font-semibold mb-4">Survey Views</h2>

    <!-- Filters -->
    <div class="mb-4 flex space-x-4">
        <select wire:model="selectedRange" class="form-select">
            @foreach($dateRanges as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        @if($selectedRange === 'custom')
            <input type="date" wire:model="startDate" class="form-input">
            <input type="date" wire:model="endDate" class="form-input">
        @endif

        <select wire:model="perPage" class="form-select">
            <option value="all">View All</option>
            <option value="25">25 per page</option>
            <option value="50">50 per page</option>
            <option value="100">100 per page</option>
        </select>
    </div>

    <!-- Chart -->
    <div class="mb-6" style="height: 300px;" wire:key="{{ $chartId }}">
        <livewire:livewire-line-chart
            :line-chart-model="$lineChartModel"
        />
    </div>

    <!-- Table -->
    <table class="table-auto w-full bg-white shadow-md rounded-lg">
        <thead>
            <tr class="bg-gray-200">
                <th class="px-4 py-2">Date</th>
                <th class="px-4 py-2">Views</th>
            </tr>
        </thead>
        <tbody>
            @foreach($viewsData as $data)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $data['date'] }}</td>
                    <td class="px-4 py-2">{{ $data['views'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($perPage !== 'all')
        <div class="mt-4">
            {{ $viewsData->links() }}
        </div>
    @endif
</div>
