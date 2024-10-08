<div x-data="{ showModal: @entangle('showComingSoonModal') }">
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-extrabold text-center mb-8 bg-gradient-to-r from-purple-400 to-pink-600 text-transparent bg-clip-text">Your Dashboard Hub</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($tools as $tool)
                <div class="icon-container bg-white bg-opacity-40 backdrop-filter backdrop-blur-lg rounded-2xl shadow-xl p-8 text-center transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-2xl cursor-pointer"
                     wire:click="enterTool('{{ $tool['name'] }}')"
                >
                    <div class="relative mb-6 group">
                        <div class="icon-bg absolute inset-0 bg-gradient-to-br opacity-20 rounded-full transition-all duration-300 ease-in-out" style="background: {{ $tool['color'] }};"></div>
                        <svg class="icon w-20 h-20 mx-auto text-gray-700 relative z-10 transition-all duration-300 ease-in-out" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $tool['icon'] }}"></path>
                        </svg>
                    </div>
                    <h2 class="tool-name text-2xl font-bold text-gray-800 mb-3 transition-all duration-300 ease-in-out">{{ $tool['name'] }}</h2>
                    <p class="text-gray-600 text-lg">Click to enter</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Coming Soon Modal -->
<div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Coming Soon!
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                The {{ $comingSoonTool }} feature is coming soon. Stay tuned for updates!
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="showModal = false" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('tool-entered', function(toolName) {
            alert('Entering ' + toolName);
        });
    });
</script>

<style>
    @keyframes float {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(5deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }
    .icon-container:hover .icon {
        animation: float 3s ease-in-out infinite;
    }
    .icon-container:hover .icon-bg {
        transform: scale(1.2);
        opacity: 0.9;
    }
    .icon-container:hover .tool-name {
        transform: translateY(-5px);
    }
</style>
</div>
