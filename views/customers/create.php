<?php
ob_start();
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="flex items-center mb-6">
            <a href="<?php echo url('/customers'); ?>" class="text-gray-400 hover:text-gray-600 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Add New Customer</h1>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <form action="<?php echo url('/customers'); ?>" method="POST" class="space-y-6 p-6">
                <?php echo csrf_field(); ?>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                               value="<?php echo e(old('name')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                        <?php if (isset($_SESSION['errors']['name'])): ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($_SESSION['errors']['name']); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Company -->
                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-700">
                            Company
                        </label>
                        <input type="text" name="company" id="company"
                               value="<?php echo e(old('company')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <input type="email" name="email" id="email"
                               value="<?php echo e(old('email')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                        <?php if (isset($_SESSION['errors']['email'])): ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($_SESSION['errors']['email']); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Phone
                        </label>
                        <input type="text" name="phone" id="phone"
                               value="<?php echo e(old('phone')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>

                    <!-- Address -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">
                            Address
                        </label>
                        <input type="text" name="address" id="address"
                               value="<?php echo e(old('address')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>

                    <!-- City -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">
                            City
                        </label>
                        <input type="text" name="city" id="city"
                               value="<?php echo e(old('city')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>

                    <!-- State -->
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">
                            State
                        </label>
                        <input type="text" name="state" id="state"
                               value="<?php echo e(old('state')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>

                    <!-- ZIP -->
                    <div>
                        <label for="zip" class="block text-sm font-medium text-gray-700">
                            ZIP Code
                        </label>
                        <input type="text" name="zip" id="zip"
                               value="<?php echo e(old('zip')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>

                    <!-- Country -->
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">
                            Country
                        </label>
                        <input type="text" name="country" id="country"
                               value="<?php echo e(old('country') ?: 'USA'); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>

                    <!-- Tax ID -->
                    <div>
                        <label for="tax_id" class="block text-sm font-medium text-gray-700">
                            Tax ID
                        </label>
                        <input type="text" name="tax_id" id="tax_id"
                               value="<?php echo e(old('tax_id')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>

                    <!-- Credit Limit -->
                    <div>
                        <label for="credit_limit" class="block text-sm font-medium text-gray-700">
                            Credit Limit
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="credit_limit" id="credit_limit" step="0.01"
                                   value="<?php echo e(old('credit_limit') ?: '0'); ?>"
                                   class="block w-full rounded-md border-gray-300 pl-7 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border"><?php echo e(old('notes')); ?></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <a href="<?php echo url('/customers'); ?>"
                       class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-save mr-2"></i> Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
unset($_SESSION['errors']);
unset($_SESSION['old']);
$title = 'Add Customer';
require __DIR__ . '/../layouts/app.php';
?>
