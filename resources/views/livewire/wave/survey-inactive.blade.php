<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-8">{{ $survey->title }}</h1>
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
        <p class="font-bold">Survey Inactive</p>
        <p>{{ $inactiveMessage }}</p>
    </div>
</div>