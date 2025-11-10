<?php

// Start session
session_start();

// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Error reporting
if (getenv('APP_DEBUG') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set('America/New_York');

// Autoloader
spl_autoload_register(function ($class) {
    $prefix_core = 'Core\\';
    $prefix_app = 'App\\';
    $base_dir_core = __DIR__ . '/../core/';
    $base_dir_app = __DIR__ . '/../app/';

    if (strncmp($prefix_core, $class, strlen($prefix_core)) === 0) {
        $relative_class = substr($class, strlen($prefix_core));
        $file = $base_dir_core . str_replace('\\', '/', $relative_class) . '.php';
    } elseif (strncmp($prefix_app, $class, strlen($prefix_app)) === 0) {
        $relative_class = substr($class, strlen($prefix_app));
        $file = $base_dir_app . str_replace('\\', '/', $relative_class) . '.php';
    } else {
        return;
    }

    if (file_exists($file)) {
        require $file;
    }
});

// Load helpers
require __DIR__ . '/../core/helpers.php';

// Initialize router
$router = new Core\Router();

// Load routes
require __DIR__ . '/../routes/web.php';

// Dispatch the request
$router->dispatch();
