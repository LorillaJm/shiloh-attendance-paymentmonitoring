<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8">
                <svg class="mx-auto h-16 w-16 sm:h-24 sm:w-24 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            
            <h1 class="text-4xl sm:text-6xl font-bold text-gray-900 dark:text-white mb-4">419</h1>
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Session Expired</h2>
            
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Your session has expired due to inactivity. Please refresh the page to continue.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <button onclick="window.location.reload()" class="w-full sm:w-auto inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Refresh Page
                </button>
                <a href="/admin" class="w-full sm:w-auto inline-block px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors text-center">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh after 3 seconds
        setTimeout(function() {
            window.location.reload();
        }, 3000);
    </script>
</body>
</html>
<?php /**PATH C:\Users\HUAWEI\OneDrive\shiloh-attendance-paymentmonitoring\resources\views/errors/419.blade.php ENDPATH**/ ?>