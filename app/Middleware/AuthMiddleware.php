<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function handle()
    {
        if (!auth()->check()) {
            $_SESSION['intended'] = $_SERVER['REQUEST_URI'];
            redirect('/login');
            return false;
        }

        return true;
    }
}
