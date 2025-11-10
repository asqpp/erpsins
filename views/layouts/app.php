<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Dashboard'; ?> - <?php echo config('app.name'); ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full">

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen"
         x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Mobile sidebar -->
    <div x-show="sidebarOpen"
         x-cloak
         class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 lg:hidden"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>
    </div>

    <!-- Static sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
        <div class="flex flex-col flex-grow bg-gray-900 overflow-y-auto">
            <?php include __DIR__ . '/../components/sidebar.php'; ?>
        </div>
    </div>

    <!-- Main content area -->
    <div class="lg:pl-64 flex flex-col flex-1">

        <!-- Top navbar -->
        <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow">
            <button @click="sidebarOpen = true" class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 lg:hidden">
                <span class="sr-only">Open sidebar</span>
                <i class="fas fa-bars text-xl"></i>
            </button>

            <div class="flex flex-1 justify-between px-4">
                <div class="flex flex-1">
                    <form class="flex w-full md:ml-0" action="<?php echo url('/search'); ?>" method="GET">
                        <label for="search-field" class="sr-only">Search</label>
                        <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center">
                                <i class="fas fa-search"></i>
                            </div>
                            <input id="search-field"
                                   class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                                   placeholder="Search..."
                                   type="search"
                                   name="q">
                        </div>
                    </form>
                </div>

                <div class="ml-4 flex items-center md:ml-6">
                    <!-- Notifications -->
                    <button type="button" class="rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="sr-only">View notifications</span>
                        <i class="fas fa-bell text-xl"></i>
                    </button>

                    <!-- Profile dropdown -->
                    <div class="relative ml-3" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open" type="button" class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                                    <?php echo strtoupper(substr(auth()->user()['name'], 0, 1)); ?>
                                </div>
                            </button>
                        </div>

                        <div x-show="open"
                             x-cloak
                             @click.away="open = false"
                             class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                            <div class="px-4 py-2 border-b">
                                <p class="text-sm font-medium text-gray-900"><?php echo e(auth()->user()['name']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e(auth()->user()['email']); ?></p>
                            </div>
                            <a href="<?php echo url('/profile'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Your Profile
                            </a>
                            <a href="<?php echo url('/settings'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </a>
                            <a href="<?php echo url('/logout'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Sign out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <main class="flex-1">
            <?php if (isset($_SESSION['flash'])): ?>
                <?php include __DIR__ . '/../components/flash-messages.php'; ?>
            <?php endif; ?>

            <?php echo $content ?? ''; ?>
        </main>
    </div>

</body>
</html>
