<?php if (isset($_SESSION['flash']['success'])): ?>
<div class="rounded-md bg-green-50 p-4 mb-4 border border-green-200" x-data="{ show: true }" x-show="show">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-check-circle text-green-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-green-800">
                <?php echo e($_SESSION['flash']['success']); unset($_SESSION['flash']['success']); ?>
            </p>
        </div>
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button @click="show = false" type="button" class="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100 focus:outline-none">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['flash']['error'])): ?>
<div class="rounded-md bg-red-50 p-4 mb-4 border border-red-200" x-data="{ show: true }" x-show="show">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-circle text-red-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-red-800">
                <?php echo e($_SESSION['flash']['error']); unset($_SESSION['flash']['error']); ?>
            </p>
        </div>
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button @click="show = false" type="button" class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100 focus:outline-none">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['flash']['warning'])): ?>
<div class="rounded-md bg-yellow-50 p-4 mb-4 border border-yellow-200" x-data="{ show: true }" x-show="show">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-yellow-800">
                <?php echo e($_SESSION['flash']['warning']); unset($_SESSION['flash']['warning']); ?>
            </p>
        </div>
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button @click="show = false" type="button" class="inline-flex rounded-md bg-yellow-50 p-1.5 text-yellow-500 hover:bg-yellow-100 focus:outline-none">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['flash']['info'])): ?>
<div class="rounded-md bg-blue-50 p-4 mb-4 border border-blue-200" x-data="{ show: true }" x-show="show">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-blue-800">
                <?php echo e($_SESSION['flash']['info']); unset($_SESSION['flash']['info']); ?>
            </p>
        </div>
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button @click="show = false" type="button" class="inline-flex rounded-md bg-blue-50 p-1.5 text-blue-500 hover:bg-blue-100 focus:outline-none">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
