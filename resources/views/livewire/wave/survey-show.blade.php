<div>
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-8">{{ $title }}</h1>

        @if (session()->has('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-medium">{{ session('message') }}</p>
            </div>
        @endif

        <form wire:submit.prevent="submit" class="space-y-8">
            @foreach($questions as $index => $question)
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        @if(!empty($question['subheading']))
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $question['subheading'] }}</h2>
                        @endif

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $question['question'] }}
                                <span class="text-sm text-gray-500 ml-1">(Points: {{ $question['points'] }})</span>
                                @if(isset($question['required']) && $question['required'])
                                    <span class="text-red-500 ml-1">*</span>
                                @endif
                            </label>

                            @switch($question['type'])
                                @case('text')
                                @case('email')
                                @case('phone')
                                @case('number')
                                @case('date')
                                @case('website')
                                @case('time')
                                @case('city')
                                    <input type="{{ $question['type'] }}" wire:model="responses.{{ $index }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" @if(isset($question['required']) && $question['required']) required @endif>
                                    @break

                                @case('textarea')
                                    <textarea wire:model="responses.{{ $index }}" rows="3" class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" @if(isset($question['required']) && $question['required']) required @endif></textarea>
                                    @break

                                @case('radio')
                                    <div class="mt-2 space-y-4">
                                        @foreach($question['options'] as $optionIndex => $option)
                                            <div class="flex items-center">
                                                <input id="radio-{{ $index }}-{{ $optionIndex }}" type="radio" wire:model="responses.{{ $index }}" value="{{ $option }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" @if(isset($question['required']) && $question['required']) required @endif>
                                                <label for="radio-{{ $index }}-{{ $optionIndex }}" class="ml-3 block text-sm font-medium text-gray-700">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @break

                                @case('checkbox')
                                    <div class="mt-2 space-y-4">
                                        @foreach($question['options'] as $optionIndex => $option)
                                            <div class="flex items-center">
                                                <input id="checkbox-{{ $index }}-{{ $optionIndex }}" type="checkbox" wire:model="responses.{{ $index }}.{{ $optionIndex }}" value="{{ $option }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                                <label for="checkbox-{{ $index }}-{{ $optionIndex }}" class="ml-3 block text-sm font-medium text-gray-700">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @break

                                @case('dropdown')
                                    <select wire:model="responses.{{ $index }}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" @if(isset($question['required']) && $question['required']) required @endif>
                                        <option value="">Select an option</option>
                                        @foreach($question['options'] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('file')
                                    <input type="file" wire:model="responses.{{ $index }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" @if(isset($question['required']) && $question['required']) required @endif>
                                    @break

                                @case('signature')
                                    <div class="mt-1">
                                        <canvas id="signature-pad-{{ $index }}" class="border border-gray-300 rounded-md" style="width: 100%; height: 200px; background-color: white;"></canvas>
                                        <input type="hidden" id="signature-data-{{ $index }}" wire:model="signatures.{{ $index }}">
                                        <button type="button" onclick="clearSignature({{ $index }})" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Clear</button>
                                    </div>
                                    @break

                                @default
                                    <input type="text" wire:model="responses.{{ $index }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" @if(isset($question['required']) && $question['required']) required @endif>
                            @endswitch
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="flex justify-between items-center mt-8">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Submit Survey
                </button>
                <button wire:click="generateAndDownloadPdf" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Download PDF
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvases = document.querySelectorAll('canvas[id^="signature-pad-"]');
            canvases.forEach(canvas => {
                const index = canvas.id.split('-').pop();
                const signaturePad = canvas.getContext('2d');
                let isDrawing = false;

                // Set the canvas background to white
                signaturePad.fillStyle = 'white';
                signaturePad.fillRect(0, 0, canvas.width, canvas.height);

                function getPointerPosition(e) {
                    const rect = canvas.getBoundingClientRect();
                    const scaleX = canvas.width / rect.width;
                    const scaleY = canvas.height / rect.height;

                    if (e.touches) {
                        return {
                            x: (e.touches[0].clientX - rect.left) * scaleX,
                            y: (e.touches[0].clientY - rect.top) * scaleY
                        };
                    } else {
                        return {
                            x: (e.clientX - rect.left) * scaleX,
                            y: (e.clientY - rect.top) * scaleY
                        };
                    }
                }

                canvas.addEventListener('mousedown', startDrawing);
                canvas.addEventListener('mouseup', stopDrawing);
                canvas.addEventListener('mousemove', draw);
                canvas.addEventListener('touchstart', startDrawing);
                canvas.addEventListener('touchend', stopDrawing);
                canvas.addEventListener('touchmove', draw);

                function startDrawing(e) {
                    isDrawing = true;
                    signaturePad.beginPath();
                    draw(e);
                }

                function stopDrawing() {
                    isDrawing = false;
                    signaturePad.beginPath();
                    let dataUrl = canvas.toDataURL('image/jpeg');
                    document.getElementById(`signature-data-${index}`).value = dataUrl;
                    @this.set(`signatures.${index}`, dataUrl);
                }

                function draw(e) {
                    if (!isDrawing) return;
                    e.preventDefault();
                    const pos = getPointerPosition(e);
                    signaturePad.lineTo(pos.x, pos.y);
                    signaturePad.stroke();
                    signaturePad.beginPath();
                    signaturePad.moveTo(pos.x, pos.y);
                }

                window.clearSignature = function(idx) {
                    if (idx == index) {
                        signaturePad.clearRect(0, 0, canvas.width, canvas.height);
                        // Reset the canvas background to white
                        signaturePad.fillStyle = 'white';
                        signaturePad.fillRect(0, 0, canvas.width, canvas.height);
                        document.getElementById(`signature-data-${index}`).value = '';
                        @this.set(`signatures.${index}`, '');
                    }
                }
            });
        });
    
    
    
        <script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvases = document.querySelectorAll('canvas[id^="signature-pad-"]');
        canvases.forEach(canvas => {
            const index = canvas.id.split('-').pop();
            const signaturePad = canvas.getContext('2d');
            let isDrawing = false;

            // Set the canvas background to white
            signaturePad.fillStyle = 'white';
            signaturePad.fillRect(0, 0, canvas.width, canvas.height);

            function getPointerPosition(e) {
                const rect = canvas.getBoundingClientRect();
                const scaleX = canvas.width / rect.width;
                const scaleY = canvas.height / rect.height;

                if (e.touches) {
                    return {
                        x: (e.touches[0].clientX - rect.left) * scaleX,
                        y: (e.touches[0].clientY - rect.top) * scaleY
                    };
                } else {
                    return {
                        x: (e.clientX - rect.left) * scaleX,
                        y: (e.clientY - rect.top) * scaleY
                    };
                }
            }

            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('touchstart', startDrawing);
            canvas.addEventListener('touchend', stopDrawing);
            canvas.addEventListener('touchmove', draw);

            function startDrawing(e) {
                isDrawing = true;
                signaturePad.beginPath();
                draw(e);
            }

            function stopDrawing() {
                isDrawing = false;
                signaturePad.beginPath();
                let dataUrl = canvas.toDataURL('image/jpeg');
                document.getElementById(`signature-data-${index}`).value = dataUrl;
                @this.set(`signatures.${index}`, dataUrl);
            }

            function draw(e) {
                if (!isDrawing) return;
                e.preventDefault();
                const pos = getPointerPosition(e);
                signaturePad.lineTo(pos.x, pos.y);
                signaturePad.stroke();
                signaturePad.beginPath();
                signaturePad.moveTo(pos.x, pos.y);
            }

            window.clearSignature = function(idx) {
                if (idx == index) {
                    signaturePad.clearRect(0, 0, canvas.width, canvas.height);
                    // Reset the canvas background to white
                    signaturePad.fillStyle = 'white';
                    signaturePad.fillRect(0, 0, canvas.width, canvas.height);
                    document.getElementById(`signature-data-${index}`).value = '';
                    @this.set(`signatures.${index}`, '');
                }
            }
        });
    });
</script>
</div>
