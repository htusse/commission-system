<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MLM Commission System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">MLM Commission System</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('commission-report') }}" 
                           class="@if(request()->routeIs('commission-report')) border-indigo-500 text-gray-900 dark:text-white @else border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-200 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Commission Report
                        </a>
                        <a href="{{ route('top-distributors') }}" 
                           class="@if(request()->routeIs('top-distributors')) border-indigo-500 text-gray-900 dark:text-white @else border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-200 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Top Distributors
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @yield('content')
    </main>
</body>
</html>
