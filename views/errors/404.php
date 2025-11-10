<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="h-full">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="text-center">
            <i class="fas fa-exclamation-triangle text-indigo-600 text-6xl mb-4"></i>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">404</h1>
            <p class="text-xl text-gray-600 mb-8">Page not found</p>
            <a href="<?php echo url('/'); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-home mr-2"></i> Go back home
            </a>
        </div>
    </div>
</body>
</html>
