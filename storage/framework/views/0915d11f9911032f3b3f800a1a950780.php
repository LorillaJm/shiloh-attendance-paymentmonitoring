<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8">
                <svg class="mx-auto h-16 w-16 sm:h-24 sm:w-24 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            
            <h1 class="text-4xl sm:text-6xl font-bold text-gray-900 dark:text-white mb-4">404</h1>
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Page Not Found</h2>
            
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                The page you are looking for doesn't exist or has been moved.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="<?php echo e(url()->previous()); ?>" class="w-full sm:w-auto inline-block px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors text-center">
                    Go Back
                </a>
                <a href="<?php echo e(route('filament.admin.pages.dashboard')); ?>" class="w-full sm:w-auto inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-center">
                    Go to Dashboard
                </a>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('app.debug') && isset($exception)): ?>
                <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-left">
                    <p class="text-sm text-blue-800 dark:text-blue-200 font-mono">
                        <?php echo e($exception->getMessage()); ?>

                    </p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\HUAWEI\OneDrive\shiloh-attendance-paymentmonitoring\resources\views/errors/404.blade.php ENDPATH**/ ?>