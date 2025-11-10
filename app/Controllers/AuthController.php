<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function showLogin()
    {
        $this->view('auth/login');
    }

    public function login()
    {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request. Please try again.');
            return $this->back();
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validation
        $errors = $this->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        // Attempt authentication
        $user = $this->userModel->authenticate($email, $password);

        if (!$user) {
            $this->setFlash('error', 'Invalid credentials. Please try again.');
            $_SESSION['old'] = ['email' => $email];
            return $this->back();
        }

        // Log activity
        $this->logActivity($user['id'], 'login', 'auth', null, 'User logged in');

        // Login user
        auth()->login($user);

        // Redirect to intended page or dashboard
        $intended = $_SESSION['intended'] ?? '/dashboard';
        unset($_SESSION['intended']);

        $this->redirect($intended);
    }

    public function showRegister()
    {
        $this->view('auth/register');
    }

    public function register()
    {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request. Please try again.');
            return $this->back();
        }

        // Validation
        $errors = $this->validate($_POST, [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Check password confirmation
        if ($_POST['password'] !== $_POST['password_confirmation']) {
            $errors['password_confirmation'] = 'Passwords do not match';
        }

        // Check if email exists
        if ($this->userModel->emailExists($_POST['email'])) {
            $errors['email'] = 'Email already registered';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        // Create user
        $userId = $this->userModel->createUser([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'role' => 'user',
            'status' => 'active'
        ]);

        if ($userId) {
            $this->setFlash('success', 'Registration successful! Please login.');
            $this->redirect('/login');
        } else {
            $this->setFlash('error', 'Registration failed. Please try again.');
            return $this->back();
        }
    }

    public function logout()
    {
        // Log activity
        if (auth()->check()) {
            $this->logActivity(auth()->id(), 'logout', 'auth', null, 'User logged out');
        }

        auth()->logout();
        $this->redirect('/login');
    }

    private function logActivity($userId, $action, $module, $recordId = null, $description = null)
    {
        $sql = "INSERT INTO activity_log (user_id, action, module, record_id, description, ip_address, user_agent)
                VALUES (:user_id, :action, :module, :record_id, :description, :ip_address, :user_agent)";

        $this->db->query($sql, [
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}
