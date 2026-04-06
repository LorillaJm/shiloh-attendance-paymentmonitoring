<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8">
                <svg class="mx-auto h-24 w-24 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            
            <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">500</h1>
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Server Error</h2>
            
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Something went wrong on our end. We're working to fix it. Please try again later.
            </p>
            
            <div class="space-y-3">
                <a href="<?php echo e(url()->previous()); ?>" class="inline-block px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                    Go Back
                </a>
                <a href="<?php echo e(route('filament.admin.pages.dashboard')); ?>" class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors ml-3">
                    Go to Dashboard
                </a>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('app.debug') && isset($exception)): ?>
                <div class="mt-8 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-left overflow-auto max-h-64">
                    <p class="text-xs text-yellow-800 dark:text-yellow-200 font-mono whitespace-pre-wrap">
                        <?php echo e($exception->getMessage()); ?>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($exception->getFile()): ?>
                        <br><br>
                        File: <?php echo e($exception->getFile()); ?>

                        Line: <?php echo e($exception->getLine()); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\HUAWEI\OneDrive\shiloh-attendance-paymentmonitoring\resources\views/errors/500.blade.php ENDPATH**/ ?>