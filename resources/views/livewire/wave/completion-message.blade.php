<div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
    <div class="text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
            {{ $surveyTitle }} Completed
        </h2>
        <div class="mt-6">
            <h3 class="text-2xl font-bold text-gray-800">
                {{ $messageTitle }}
            </h3>
            <div class="mt-4 text-lg leading-6 text-gray-500">
                {!! $messageContent !!}
            </div>
        </div>
        <div class="mt-6">
            @if($redirectType === 'button')
                <a href="{{ $redirectUrl }}" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Continue
                </a>
            @else
                <p>You will be redirected in <span id="countdown">{{ $redirectDelay }}</span> seconds.</p>
            @endif
        </div>
    </div>
</div>

@if($redirectType === 'automatic')
<script>
    let countdown = {{ $redirectDelay }};
    const countdownElement = document.getElementById('countdown');
    const intervalId = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        if (countdown <= 0) {
            clearInterval(intervalId);
            window.location.href = '{{ $redirectUrl }}';
        }
    }, 1000);
</script>
@endif