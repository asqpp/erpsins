<?php

namespace App\Middleware;

class GuestMiddleware
{
    public function handle()
    {
        if (auth()->check()) {
            redirect('/dashboard');
            return false;
        }

        return true;
    }
}
