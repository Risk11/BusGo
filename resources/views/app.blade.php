<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title inertia>{{ config('app.name', 'BusGo') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-800">

    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <h1 class="text-2xl font-bold text-blue-600">BusGo</h1>
            </div>
        </header>

        <main class="flex-1 py-6 px-4 sm:px-6 lg:px-8">
            @inertia
        </main>

        <footer class="bg-white text-center text-sm text-gray-500 py-4 border-t">
            &copy; {{ date('Y') }} BusGo. All rights reserved.
        </footer>
    </div>
</body>

</html>
