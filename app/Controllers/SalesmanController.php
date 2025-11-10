<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Salesman;
use App\Models\Account;

class SalesmanController extends Controller
{
    private $salesmanModel;
    private $accountModel;

    public function __construct()
    {
        parent::__construct();
        $this->salesmanModel = new Salesman();
        $this->accountModel = new Account();
    }

    public function index()
    {
        $salesmen = $this->salesmanModel->getWithStats();

        $this->view('masters/salesmen/index', [
            'salesmen' => $salesmen,
            'user' => auth()->user()
        ]);
    }

    public function create()
    {
        $accounts = $this->accountModel->where(['status' => 'active'], 'account_name ASC');
        $nextCode = $this->salesmanModel->generateCode();

        $this->view('masters/salesmen/create', [
            'accounts' => $accounts,
            'nextCode' => $nextCode,
            'user' => auth()->user()
        ]);
    }

    public function store()
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $errors = $this->validate($_POST, [
            'code' => 'required|max:50',
            'name' => 'required|min:2|max:255',
            'commission_rate' => 'required|numeric'
        ]);

        $existing = $this->salesmanModel->first(['code' => $_POST['code']]);
        if ($existing) {
            $errors['code'] = 'Salesman code already exists';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $salesmanId = $this->salesmanModel->create([
            'code' => $_POST['code'],
            'name' => $_POST['name'],
            'email' => $_POST['email'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'address' => $_POST['address'] ?? null,
            'commission_rate' => $_POST['commission_rate'],
            'account_id' => $_POST['account_id'] ?? null,
            'status' => 'active',
            'created_by' => auth()->id()
        ]);

        if ($salesmanId) {
            $this->setFlash('success', 'Salesman created successfully');
            $this->redirect('/masters/salesmen');
        } else {
            $this->setFlash('error', 'Failed to create salesman');
            return $this->back();
        }
    }

    public function edit($id)
    {
        $salesman = $this->salesmanModel->find($id);

        if (!$salesman) {
            $this->setFlash('error', 'Salesman not found');
            return $this->redirect('/masters/salesmen');
        }

        $accounts = $this->accountModel->where(['status' => 'active'], 'account_name ASC');

        $this->view('masters/salesmen/edit', [
            'salesman' => $salesman,
            'accounts' => $accounts,
            'user' => auth()->user()
        ]);
    }

    public function update($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $salesman = $this->salesmanModel->find($id);

        if (!$salesman) {
            $this->setFlash('error', 'Salesman not found');
            return $this->redirect('/masters/salesmen');
        }

        $errors = $this->validate($_POST, [
            'code' => 'required|max:50',
            'name' => 'required|min:2|max:255',
            'commission_rate' => 'required|numeric'
        ]);

        $existing = $this->db->fetch("
            SELECT * FROM salesmen WHERE code = :code AND id != :id
        ", ['code' => $_POST['code'], 'id' => $id]);

        if ($existing) {
            $errors['code'] = 'Salesman code already exists';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $updated = $this->salesmanModel->update($id, [
            'code' => $_POST['code'],
            'name' => $_POST['name'],
            'email' => $_POST['email'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'address' => $_POST['address'] ?? null,
            'commission_rate' => $_POST['commission_rate'],
            'account_id' => $_POST['account_id'] ?? null,
            'status' => $_POST['status'] ?? 'active'
        ]);

        if ($updated !== false) {
            $this->setFlash('success', 'Salesman updated successfully');
            $this->redirect('/masters/salesmen');
        } else {
            $this->setFlash('error', 'Failed to update salesman');
            return $this->back();
        }
    }

    public function destroy($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $hasCommissions = $this->db->fetch("
            SELECT COUNT(*) as count FROM commissions WHERE salesman_id = :id
        ", ['id' => $id])['count'];

        if ($hasCommissions > 0) {
            $this->setFlash('error', 'Cannot delete salesman with existing commissions');
            return $this->back();
        }

        $deleted = $this->salesmanModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'Salesman deleted successfully');
        } else {
            $this->setFlash('error', 'Failed to delete salesman');
        }

        $this->redirect('/masters/salesmen');
    }
}
