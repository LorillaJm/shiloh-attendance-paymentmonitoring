<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8">
                <svg class="mx-auto h-24 w-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">403</h1>
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Access Forbidden</h2>
            
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                {{ $message ?? 'You do not have permission to access this resource.' }}
            </p>
            
            <div class="space-y-3">
                <a href="{{ url()->previous() }}" class="inline-block px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                    Go Back
                </a>
                <a href="{{ route('filament.admin.pages.dashboard') }}" class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors ml-3">
                    Go to Dashboard
                </a>
            </div>
            
            @if(config('app.debug') && isset($exception))
                <div class="mt-8 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg text-left">
                    <p class="text-sm text-red-800 dark:text-red-200 font-mono">
                        {{ $exception->getMessage() }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
