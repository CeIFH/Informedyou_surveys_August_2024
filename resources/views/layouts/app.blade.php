<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="url" content="{{ url('/') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/views/themes/tailwind/assets/sass/app.scss', 'resources/views/themes/tailwind/assets/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    

    @livewireStyles
    @livewireChartsScripts
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100">
            <!-- Add your navigation content here -->
        </nav>

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
       
    </div>

    @livewireScripts
</body>
</html>