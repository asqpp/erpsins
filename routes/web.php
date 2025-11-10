<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

// Guest routes (only accessible when not logged in)
$router->get('/', function() {
    redirect('/login');
});

$router->get('/login', 'AuthController@showLogin', [GuestMiddleware::class]);
$router->post('/login', 'AuthController@login', [GuestMiddleware::class]);

$router->get('/register', 'AuthController@showRegister', [GuestMiddleware::class]);
$router->post('/register', 'AuthController@register', [GuestMiddleware::class]);

// Logout route
$router->get('/logout', 'AuthController@logout');

// Protected routes (require authentication)
$router->get('/dashboard', 'DashboardController@index', [AuthMiddleware::class]);

// Customer routes
$router->get('/customers', 'CustomerController@index', [AuthMiddleware::class]);
$router->get('/customers/create', 'CustomerController@create', [AuthMiddleware::class]);
$router->post('/customers', 'CustomerController@store', [AuthMiddleware::class]);
$router->get('/customers/{id}', 'CustomerController@show', [AuthMiddleware::class]);
$router->get('/customers/{id}/edit', 'CustomerController@edit', [AuthMiddleware::class]);
$router->post('/customers/{id}', 'CustomerController@update', [AuthMiddleware::class]);
$router->post('/customers/{id}/delete', 'CustomerController@destroy', [AuthMiddleware::class]);

// Product routes
$router->get('/products', 'ProductController@index', [AuthMiddleware::class]);
$router->get('/products/create', 'ProductController@create', [AuthMiddleware::class]);
$router->post('/products', 'ProductController@store', [AuthMiddleware::class]);
$router->get('/products/{id}', 'ProductController@show', [AuthMiddleware::class]);
$router->get('/products/{id}/edit', 'ProductController@edit', [AuthMiddleware::class]);
$router->post('/products/{id}', 'ProductController@update', [AuthMiddleware::class]);
$router->post('/products/{id}/delete', 'ProductController@destroy', [AuthMiddleware::class]);

// Invoice routes
$router->get('/invoices', 'InvoiceController@index', [AuthMiddleware::class]);
$router->get('/invoices/create', 'InvoiceController@create', [AuthMiddleware::class]);
$router->post('/invoices', 'InvoiceController@store', [AuthMiddleware::class]);
$router->get('/invoices/{id}', 'InvoiceController@show', [AuthMiddleware::class]);
$router->get('/invoices/{id}/edit', 'InvoiceController@edit', [AuthMiddleware::class]);
$router->post('/invoices/{id}', 'InvoiceController@update', [AuthMiddleware::class]);
$router->post('/invoices/{id}/delete', 'InvoiceController@destroy', [AuthMiddleware::class]);
$router->get('/invoices/{id}/pdf', 'InvoiceController@pdf', [AuthMiddleware::class]);

// User management routes (admin only)
$router->get('/users', 'UserController@index', [AuthMiddleware::class]);
$router->get('/users/create', 'UserController@create', [AuthMiddleware::class]);
$router->post('/users', 'UserController@store', [AuthMiddleware::class]);
$router->get('/users/{id}/edit', 'UserController@edit', [AuthMiddleware::class]);
$router->post('/users/{id}', 'UserController@update', [AuthMiddleware::class]);
$router->post('/users/{id}/delete', 'UserController@destroy', [AuthMiddleware::class]);

// Settings
$router->get('/settings', 'SettingsController@index', [AuthMiddleware::class]);
$router->post('/settings', 'SettingsController@update', [AuthMiddleware::class]);

// Activity log
$router->get('/activity', 'ActivityController@index', [AuthMiddleware::class]);

// ============================================================================
// ACCOUNTING MODULE ROUTES
// ============================================================================

// Chart of Accounts / Accounts
$router->get('/accounting/accounts', 'AccountController@index', [AuthMiddleware::class]);
$router->get('/accounting/accounts/create', 'AccountController@create', [AuthMiddleware::class]);
$router->post('/accounting/accounts', 'AccountController@store', [AuthMiddleware::class]);
$router->get('/accounting/accounts/{id}/edit', 'AccountController@edit', [AuthMiddleware::class]);
$router->post('/accounting/accounts/{id}', 'AccountController@update', [AuthMiddleware::class]);
$router->post('/accounting/accounts/{id}/delete', 'AccountController@destroy', [AuthMiddleware::class]);
$router->get('/accounting/accounts/{id}/ledger', 'AccountController@ledger', [AuthMiddleware::class]);

// Journal Vouchers
$router->get('/accounting/journals', 'JournalController@index', [AuthMiddleware::class]);
$router->get('/accounting/journals/create', 'JournalController@create', [AuthMiddleware::class]);
$router->post('/accounting/journals', 'JournalController@store', [AuthMiddleware::class]);
$router->get('/accounting/journals/{id}', 'JournalController@show', [AuthMiddleware::class]);
$router->post('/accounting/journals/{id}/post', 'JournalController@post', [AuthMiddleware::class]);
$router->post('/accounting/journals/{id}/delete', 'JournalController@destroy', [AuthMiddleware::class]);

// ============================================================================
// MASTERS MODULE ROUTES
// ============================================================================

// Brokers
$router->get('/masters/brokers', 'BrokerController@index', [AuthMiddleware::class]);
$router->get('/masters/brokers/create', 'BrokerController@create', [AuthMiddleware::class]);
$router->post('/masters/brokers', 'BrokerController@store', [AuthMiddleware::class]);
$router->get('/masters/brokers/{id}/edit', 'BrokerController@edit', [AuthMiddleware::class]);
$router->post('/masters/brokers/{id}', 'BrokerController@update', [AuthMiddleware::class]);
$router->post('/masters/brokers/{id}/delete', 'BrokerController@destroy', [AuthMiddleware::class]);

// Salesmen
$router->get('/masters/salesmen', 'SalesmanController@index', [AuthMiddleware::class]);
$router->get('/masters/salesmen/create', 'SalesmanController@create', [AuthMiddleware::class]);
$router->post('/masters/salesmen', 'SalesmanController@store', [AuthMiddleware::class]);
$router->get('/masters/salesmen/{id}/edit', 'SalesmanController@edit', [AuthMiddleware::class]);
$router->post('/masters/salesmen/{id}', 'SalesmanController@update', [AuthMiddleware::class]);
$router->post('/masters/salesmen/{id}/delete', 'SalesmanController@destroy', [AuthMiddleware::class]);

// ============================================================================
// HR MODULE ROUTES
// ============================================================================

// Employees
$router->get('/hr/employees', 'EmployeeController@index', [AuthMiddleware::class]);
$router->get('/hr/employees/create', 'EmployeeController@create', [AuthMiddleware::class]);
$router->post('/hr/employees', 'EmployeeController@store', [AuthMiddleware::class]);
$router->get('/hr/employees/{id}/edit', 'EmployeeController@edit', [AuthMiddleware::class]);
$router->post('/hr/employees/{id}', 'EmployeeController@update', [AuthMiddleware::class]);
$router->post('/hr/employees/{id}/delete', 'EmployeeController@destroy', [AuthMiddleware::class]);

// API endpoints (return JSON)
$router->get('/api/customers/search', 'Api\\CustomerController@search', [AuthMiddleware::class]);
$router->get('/api/products/search', 'Api\\ProductController@search', [AuthMiddleware::class]);
$router->get('/api/products/{id}', 'Api\\ProductController@show', [AuthMiddleware::class]);
$router->get('/api/dashboard/stats', 'Api\\DashboardController@stats', [AuthMiddleware::class]);
$router->get('/api/accounts/search', 'Api\\AccountController@search', [AuthMiddleware::class]);
$router->get('/api/brokers/search', 'Api\\BrokerController@search', [AuthMiddleware::class]);
$router->get('/api/salesmen/search', 'Api\\SalesmanController@search', [AuthMiddleware::class]);
