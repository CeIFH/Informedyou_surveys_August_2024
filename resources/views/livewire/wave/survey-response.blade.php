<div>
    <!-- <style>
        .survey-input {
            width: 100% !important;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            color: #111827; /* text-gray-900 */
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .survey-input:focus {
            outline: none;
            border-color: #6366f1; /* indigo-500 */
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2); /* Indigo focus ring */
        }

        /* For checkboxes and radio buttons */
        .survey-input[type="checkbox"],
        .survey-input[type="radio"] {
            width: 1rem !important;
            height: 1rem;
            color: #4f46e5; /* indigo-600 */
            border-color: #d1d5db; /* gray-300 */
        }

        .survey-input[type="checkbox"]:focus,
        .survey-input[type="radio"]:focus {
            ring-color: #6366f1; /* indigo-500 */
            ring-offset-color: #ffffff;
            ring-offset-width: 2px;
        }
    </style> -->



    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-8">{{ $survey->title }}</h1>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
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
                        @if($question['type'] == 'text')
                            <input type="text" wire:model.lazy="responses.{{ $index }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" @if(isset($question['required']) && $question['required']) required @endif>
                        @elseif($question['type'] == 'multiple_choice')
                            <div class="mt-2 space-y-4">
                                @foreach($question['options'] as $optionIndex => $option)
                                    <div class="flex items-center">
                                        <input id="radio-{{ $index }}-{{ $optionIndex }}" name="responses[{{ $index }}]" type="radio" wire:model.lazy="responses.{{ $index }}" value="{{ $option }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" @if(isset($question['required']) && $question['required']) required @endif>
                                        <label for="radio-{{ $index }}-{{ $optionIndex }}" class="ml-3 block text-sm font-medium text-gray-700">{{ $option }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question['type'] == 'checkbox')
                            <div class="mt-2 space-y-4">
                                @foreach($question['options'] as $optionIndex => $option)
                                    <div class="flex items-center">
                                        <input id="checkbox-{{ $index }}-{{ $optionIndex }}" type="checkbox" wire:model.lazy="responses.{{ $index }}.{{ $optionIndex }}" value="{{ $option }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="checkbox-{{ $index }}-{{ $optionIndex }}" class="ml-3 block text-sm font-medium text-gray-700">{{ $option }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question['type'] == 'dropdown')
                            <select wire:model.lazy="responses.{{ $index }}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" @if(isset($question['required']) && $question['required']) required @endif>
                                <option value="">Select an option</option>
                                @foreach($question['options'] as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        @elseif($question['type'] == 'textarea')
                            <textarea wire:model.lazy="responses.{{ $index }}" rows="3" class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" @if(isset($question['required']) && $question['required']) required @endif></textarea>
                        @elseif($question['type'] == 'signature')
                            <div class="mt-1">
                                <canvas id="signature-pad-{{ $index }}" class="border border-gray-300 rounded-md" style="width: 100%; height: 200px; background-color: white;"></canvas>
                                <input type="hidden" id="signature-data-{{ $index }}" wire:model.lazy="signatures.{{ $index }}">
                                <button type="button" onclick="clearSignature({{ $index }})" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Clear</button>
                            </div>
                        @elseif(in_array($question['type'], ['email', 'phone', 'number', 'date', 'website', 'time', 'city']))
                            <input type="{{ $question['type'] }}" wire:model.lazy="responses.{{ $index }}" class="survey-input" @if(isset($question['required']) && $question['required']) required @endif>
                        @elseif($question['type'] == 'file')
                            <input type="file" wire:model.lazy="responses.{{ $index }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" @if(isset($question['required']) && $question['required']) required @endif>
                        @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="flex justify-between items-center mt-8">
                <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span wire:loading.remove>Submit Survey</span>
                    <span wire:loading>Submitting...</span>
                </button>
                <button wire:click.prevent="generateAndDownloadPdf" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Download PDF
                </button>
            </div>
        </form>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(app()->environment('local'))
        <div class="mt-4 p-4 bg-gray-100 rounded">
            <h3 class="font-bold">Debug Info:</h3>
            <pre>{{ json_encode($responses, JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', function () {
            let isSubmitting = false;

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

            let startTimes = {};
            let currentQuestion = null;

            function startTimingQuestion(questionIndex) {
                if (currentQuestion !== null) {
                    stopTimingQuestion(currentQuestion);
                }
                startTimes[questionIndex] = new Date();
                currentQuestion = questionIndex;
            }

            // Start timing the first question
            startTimingQuestion(0);

            // Add event listeners to all question divs
            document.querySelectorAll('[id^="question-"]').forEach(function(questionDiv) {
                const questionIndex = questionDiv.id.split('-')[1];

                questionDiv.addEventListener('focusin', function() {
                    startTimingQuestion(questionIndex);
                });
            });

            // Stop timing when form is submitted
            document.querySelector('form').addEventListener('submit', function() {
                if (currentQuestion !== null) {
                    stopTimingQuestion(currentQuestion);
                }
            });

            // Modify the form submission event
            document.querySelector('form').addEventListener('submit', function() {
                isSubmitting = true;
                sessionStorage.setItem('survey_submitted', 'true');
            });

            // Clear form data when leaving the page, but not when submitting
            window.addEventListener('beforeunload', function (e) {
                if (!isSubmitting) {
                    clearFormData();
                }
            });

            // Clear form data when the page is loaded
            window.addEventListener('pageshow', function (event) {
                if (sessionStorage.getItem('survey_submitted') === 'true') {
                    sessionStorage.removeItem('survey_submitted');
                } else {
                    clearFormData();
                }
            });

            function clearFormData() {
                document.querySelectorAll('input, textarea, select').forEach(element => {
                    element.value = '';
                });
                document.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(element => {
                    element.checked = false;
                });
                // Clear signature pads
                canvases.forEach(canvas => {
                    const signaturePad = canvas.getContext('2d');
                    signaturePad.clearRect(0, 0, canvas.width, canvas.height);
                    signaturePad.fillStyle = 'white';
                    signaturePad.fillRect(0, 0, canvas.width, canvas.height);
                });
             
                // Make an AJAX call to clear server-side session
                fetch('/clear-survey-session', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ survey_id: '{{ $survey->id }}' })
                });
            }

            // Listen for Livewire event to clear form data
            Livewire.on('surveyDataCleared', function() {
                clearFormData();
            });
        });
    </script>
    </div>
