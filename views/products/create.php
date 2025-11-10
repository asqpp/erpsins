<?php
ob_start();
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="flex items-center mb-6">
            <a href="<?php echo url('/products'); ?>" class="text-gray-400 hover:text-gray-600 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Add New Product</h1>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <form action="<?php echo url('/products'); ?>" method="POST" class="space-y-6 p-6">
                <?php echo csrf_field(); ?>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- SKU -->
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="sku" id="sku" required
                               value="<?php echo e(old('sku')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                        <?php if (isset($_SESSION['errors']['sku'])): ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($_SESSION['errors']['sku']); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                               value="<?php echo e(old('name')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                        <?php if (isset($_SESSION['errors']['name'])): ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($_SESSION['errors']['name']); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border"><?php echo e(old('description')); ?></textarea>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">
                            Category
                        </label>
                        <select name="category_id" id="category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo old('category_id') == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Barcode -->
                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700">
                            Barcode
                        </label>
                        <input type="text" name="barcode" id="barcode"
                               value="<?php echo e(old('barcode')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">
                            Price <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="price" id="price" step="0.01" required
                                   value="<?php echo e(old('price') ?: '0'); ?>"
                                   class="block w-full rounded-md border-gray-300 pl-7 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                        </div>
                        <?php if (isset($_SESSION['errors']['price'])): ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($_SESSION['errors']['price']); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Cost -->
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700">
                            Cost
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="cost" id="cost" step="0.01"
                                   value="<?php echo e(old('cost') ?: '0'); ?>"
                                   class="block w-full rounded-md border-gray-300 pl-7 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">
                            Quantity
                        </label>
                        <input type="number" name="quantity" id="quantity"
                               value="<?php echo e(old('quantity') ?: '0'); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>

                    <!-- Min Quantity -->
                    <div>
                        <label for="min_quantity" class="block text-sm font-medium text-gray-700">
                            Minimum Quantity
                        </label>
                        <input type="number" name="min_quantity" id="min_quantity"
                               value="<?php echo e(old('min_quantity') ?: '10'); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>

                    <!-- Unit -->
                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700">
                            Unit
                        </label>
                        <select name="unit" id="unit"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                            <option value="pcs" <?php echo old('unit') === 'pcs' ? 'selected' : ''; ?>>Pieces</option>
                            <option value="box" <?php echo old('unit') === 'box' ? 'selected' : ''; ?>>Box</option>
                            <option value="kg" <?php echo old('unit') === 'kg' ? 'selected' : ''; ?>>Kilogram</option>
                            <option value="lb" <?php echo old('unit') === 'lb' ? 'selected' : ''; ?>>Pound</option>
                            <option value="liter" <?php echo old('unit') === 'liter' ? 'selected' : ''; ?>>Liter</option>
                            <option value="meter" <?php echo old('unit') === 'meter' ? 'selected' : ''; ?>>Meter</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <a href="<?php echo url('/products'); ?>"
                       class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-save mr-2"></i> Save Product
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
$title = 'Add Product';
require __DIR__ . '/../layouts/app.php';
?>
