<div class="flex flex-col h-full">
    <!-- Logo -->
    <div class="flex items-center h-16 flex-shrink-0 px-4 bg-gray-900">
        <i class="fas fa-chart-line text-indigo-500 text-2xl mr-3"></i>
        <h1 class="text-white text-xl font-bold"><?php echo config('app.name'); ?></h1>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 px-2 py-4">
        <a href="<?php echo url('/dashboard'); ?>"
           class="<?php echo activeMenu('/dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
            <i class="fas fa-tachometer-alt mr-3 text-lg"></i>
            Dashboard
        </a>

        <a href="<?php echo url('/customers'); ?>"
           class="<?php echo activeMenu('/customers') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
            <i class="fas fa-users mr-3 text-lg"></i>
            Customers
        </a>

        <a href="<?php echo url('/products'); ?>"
           class="<?php echo activeMenu('/products') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
            <i class="fas fa-box mr-3 text-lg"></i>
            Products
        </a>

        <a href="<?php echo url('/invoices'); ?>"
           class="<?php echo activeMenu('/invoices') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
            <i class="fas fa-file-invoice mr-3 text-lg"></i>
            Invoices
        </a>

        <!-- Accounting Section -->
        <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Accounting
            </h3>
            <a href="<?php echo url('/accounting/accounts'); ?>"
               class="<?php echo activeMenu('/accounting/accounts') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1">
                <i class="fas fa-book mr-3 text-lg"></i>
                Chart of Accounts
            </a>

            <a href="<?php echo url('/accounting/journals'); ?>"
               class="<?php echo activeMenu('/accounting/journals') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <i class="fas fa-receipt mr-3 text-lg"></i>
                Journal Vouchers
            </a>
        </div>

        <!-- Masters Section -->
        <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Masters
            </h3>
            <a href="<?php echo url('/masters/brokers'); ?>"
               class="<?php echo activeMenu('/masters/brokers') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1">
                <i class="fas fa-handshake mr-3 text-lg"></i>
                Brokers
            </a>

            <a href="<?php echo url('/masters/salesmen'); ?>"
               class="<?php echo activeMenu('/masters/salesmen') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <i class="fas fa-user-tie mr-3 text-lg"></i>
                Salesmen
            </a>
        </div>

        <!-- HR Section -->
        <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Human Resources
            </h3>
            <a href="<?php echo url('/hr/employees'); ?>"
               class="<?php echo activeMenu('/hr/employees') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1">
                <i class="fas fa-id-card mr-3 text-lg"></i>
                Employees
            </a>
        </div>

        <?php if (auth()->user()['role'] === 'admin'): ?>
        <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Administration
            </h3>
            <a href="<?php echo url('/users'); ?>"
               class="<?php echo activeMenu('/users') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1">
                <i class="fas fa-user-shield mr-3 text-lg"></i>
                Users
            </a>

            <a href="<?php echo url('/settings'); ?>"
               class="<?php echo activeMenu('/settings') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <i class="fas fa-cog mr-3 text-lg"></i>
                Settings
            </a>

            <a href="<?php echo url('/activity'); ?>"
               class="<?php echo activeMenu('/activity') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <i class="fas fa-history mr-3 text-lg"></i>
                Activity Log
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <!-- User info -->
    <div class="flex-shrink-0 flex border-t border-gray-800 p-4">
        <div class="flex items-center">
            <div class="h-9 w-9 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                <?php echo strtoupper(substr(auth()->user()['name'], 0, 1)); ?>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-white"><?php echo e(auth()->user()['name']); ?></p>
                <p class="text-xs font-medium text-gray-400"><?php echo ucfirst(auth()->user()['role']); ?></p>
            </div>
        </div>
    </div>
</div>
