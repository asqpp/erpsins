<?php

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        static $config = [];

        if (empty($config)) {
            $config = [
                'app' => require __DIR__ . '/../config/app.php',
                'database' => require __DIR__ . '/../config/database.php',
            ];
        }

        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}

if (!function_exists('asset')) {
    function asset($path)
    {
        $baseUrl = getBaseUrl();
        return $baseUrl . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url($path = '')
    {
        $baseUrl = getBaseUrl();
        return $baseUrl . '/' . ltrim($path, '/');
    }
}

if (!function_exists('getBaseUrl')) {
    function getBaseUrl()
    {
        static $baseUrl = null;

        if ($baseUrl === null) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $scriptName = dirname($_SERVER['SCRIPT_NAME']);
            $baseUrl = $protocol . '://' . $host . ($scriptName !== '/' ? $scriptName : '');
        }

        return $baseUrl;
    }
}

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('old')) {
    function old($key, $default = '')
    {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('auth')) {
    function auth()
    {
        return new class {
            public function check()
            {
                return isset($_SESSION['user_id']);
            }

            public function user()
            {
                if (!$this->check()) {
                    return null;
                }

                if (!isset($_SESSION['user_data'])) {
                    $userModel = new \App\Models\User();
                    $_SESSION['user_data'] = $userModel->find($_SESSION['user_id']);
                }

                return $_SESSION['user_data'];
            }

            public function id()
            {
                return $_SESSION['user_id'] ?? null;
            }

            public function logout()
            {
                unset($_SESSION['user_id']);
                unset($_SESSION['user_data']);
                session_destroy();
            }

            public function login($user)
            {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_data'] = $user;
                session_regenerate_id(true);
            }
        };
    }
}

if (!function_exists('redirect')) {
    function redirect($path)
    {
        header('Location: ' . url($path));
        exit;
    }
}

if (!function_exists('back')) {
    function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        header('Location: ' . $referer);
        exit;
    }
}

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        die();
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount, $currency = 'USD')
    {
        return '$' . number_format($amount, 2);
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'M d, Y')
    {
        if (empty($date)) {
            return '';
        }
        return date($format, strtotime($date));
    }
}

if (!function_exists('formatDateTime')) {
    function formatDateTime($datetime, $format = 'M d, Y g:i A')
    {
        if (empty($datetime)) {
            return '';
        }
        return date($format, strtotime($datetime));
    }
}

if (!function_exists('activeMenu')) {
    function activeMenu($path)
    {
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/') {
            $currentPath = substr($currentPath, strlen($scriptName));
        }

        return strpos($currentPath, $path) === 0 ? 'active' : '';
    }
}
