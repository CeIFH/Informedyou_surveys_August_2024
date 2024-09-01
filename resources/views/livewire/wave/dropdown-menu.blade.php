 <!-- TALL Stack Dropdown -->
 <x-dropdown icon="ellipsis-vertical" static>
                    <x-dropdown.items text="Duplicate" href="{{ route('survey.duplicate', $survey->id) }}" />
                    <x-dropdown.items text="Export" href="{{ route('survey.export', $survey->id) }}" />
                    <x-dropdown.items text="Delete" href="{{ route('survey.delete', $survey->id) }}" separator />
                </x-dropdown>
            </div>
        </div>
        @endforeach
    </div>
</div>
