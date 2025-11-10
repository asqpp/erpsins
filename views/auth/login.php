<?php
ob_start();
?>

<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <i class="fas fa-chart-line text-indigo-600 text-5xl"></i>
        </div>
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900"><?php echo config('app.name'); ?></h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Sign in to your account
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form class="space-y-6" action="<?php echo url('/login'); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input id="email"
                               name="email"
                               type="email"
                               autocomplete="email"
                               required
                               value="<?php echo e(old('email')); ?>"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <?php if (isset($_SESSION['errors']['email'])): ?>
                        <p class="mt-2 text-sm text-red-600"><?php echo e($_SESSION['errors']['email']); ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password"
                               name="password"
                               type="password"
                               autocomplete="current-password"
                               required
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <?php if (isset($_SESSION['errors']['password'])): ?>
                        <p class="mt-2 text-sm text-red-600"><?php echo e($_SESSION['errors']['password']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember"
                               name="remember"
                               type="checkbox"
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Sign in
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-2 text-gray-500">Don't have an account?</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="<?php echo url('/register'); ?>"
                       class="flex w-full justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Register now
                    </a>
                </div>
            </div>

            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-xs text-blue-800 font-semibold mb-2">Demo Credentials:</p>
                <ul class="text-xs text-blue-700 space-y-1">
                    <li><strong>Admin:</strong> admin@example.com / admin123</li>
                    <li><strong>Manager:</strong> manager@example.com / admin123</li>
                    <li><strong>User:</strong> user@example.com / admin123</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
unset($_SESSION['errors']);
unset($_SESSION['old']);
$title = 'Login';
require __DIR__ . '/../layouts/guest.php';
?>
