<?php
ob_start();
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <!-- Stats -->
        <div class="mt-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Customers -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-md bg-indigo-500 p-3">
                                    <i class="fas fa-users text-white text-2xl"></i>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                                    <dd class="text-3xl font-semibold text-gray-900"><?php echo number_format($stats['total_customers']); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <a href="<?php echo url('/customers'); ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            View all <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-md bg-green-500 p-3">
                                    <i class="fas fa-box text-white text-2xl"></i>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Products</dt>
                                    <dd class="text-3xl font-semibold text-gray-900"><?php echo number_format($stats['total_products']); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <a href="<?php echo url('/products'); ?>" class="text-sm font-medium text-green-600 hover:text-green-500">
                            View all <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-md bg-blue-500 p-3">
                                    <i class="fas fa-dollar-sign text-white text-2xl"></i>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Revenue (Paid)</dt>
                                    <dd class="text-3xl font-semibold text-gray-900"><?php echo formatCurrency($stats['revenue_paid']); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm text-gray-500">
                            Pending: <?php echo formatCurrency($stats['revenue_pending']); ?>
                        </div>
                    </div>
                </div>

                <!-- Pending Invoices -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-md bg-yellow-500 p-3">
                                    <i class="fas fa-file-invoice text-white text-2xl"></i>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Invoices</dt>
                                    <dd class="text-3xl font-semibold text-gray-900"><?php echo number_format($stats['pending_invoices']); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <a href="<?php echo url('/invoices'); ?>" class="text-sm font-medium text-yellow-600 hover:text-yellow-500">
                            View all <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <!-- Recent Invoices -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Invoices</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <?php if (empty($recentInvoices)): ?>
                        <p class="text-gray-500 text-center py-4">No invoices yet</p>
                    <?php else: ?>
                        <div class="flow-root">
                            <ul class="-my-5 divide-y divide-gray-200">
                                <?php foreach ($recentInvoices as $invoice): ?>
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                <?php echo e($invoice['invoice_number']); ?>
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                <?php echo e($invoice['customer_name']); ?>
                                                <?php if ($invoice['company']): ?>
                                                    (<?php echo e($invoice['company']); ?>)
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <p class="text-sm font-semibold text-gray-900">
                                                <?php echo formatCurrency($invoice['total']); ?>
                                            </p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                <?php if ($invoice['status'] === 'paid'): ?>
                                                    bg-green-100 text-green-800
                                                <?php elseif ($invoice['status'] === 'overdue'): ?>
                                                    bg-red-100 text-red-800
                                                <?php elseif ($invoice['status'] === 'sent'): ?>
                                                    bg-blue-100 text-blue-800
                                                <?php else: ?>
                                                    bg-gray-100 text-gray-800
                                                <?php endif; ?>">
                                                <?php echo ucfirst($invoice['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Low Stock Alert</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <?php if (empty($lowStockProducts)): ?>
                        <p class="text-gray-500 text-center py-4">All products are well stocked</p>
                    <?php else: ?>
                        <div class="flow-root">
                            <ul class="-my-5 divide-y divide-gray-200">
                                <?php foreach ($lowStockProducts as $product): ?>
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                <?php echo e($product['name']); ?>
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                SKU: <?php echo e($product['sku']); ?>
                                                <?php if ($product['category_name']): ?>
                                                    | <?php echo e($product['category_name']); ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <p class="text-sm font-semibold <?php echo $product['quantity'] == 0 ? 'text-red-600' : 'text-yellow-600'; ?>">
                                                <?php echo $product['quantity']; ?> <?php echo e($product['unit']); ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Min: <?php echo $product['min_quantity']; ?>
                                            </p>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Dashboard';
require __DIR__ . '/../layouts/app.php';
?>
