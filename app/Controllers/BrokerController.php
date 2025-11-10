<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Broker;
use App\Models\Account;

class BrokerController extends Controller
{
    private $brokerModel;
    private $accountModel;

    public function __construct()
    {
        parent::__construct();
        $this->brokerModel = new Broker();
        $this->accountModel = new Account();
    }

    public function index()
    {
        $brokers = $this->brokerModel->getWithStats();

        $this->view('masters/brokers/index', [
            'brokers' => $brokers,
            'user' => auth()->user()
        ]);
    }

    public function create()
    {
        $accounts = $this->accountModel->where(['status' => 'active'], 'account_name ASC');
        $nextCode = $this->brokerModel->generateCode();

        $this->view('masters/brokers/create', [
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

        // Check if code exists
        $existing = $this->brokerModel->first(['code' => $_POST['code']]);
        if ($existing) {
            $errors['code'] = 'Broker code already exists';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $brokerId = $this->brokerModel->create([
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

        if ($brokerId) {
            $this->setFlash('success', 'Broker created successfully');
            $this->redirect('/masters/brokers');
        } else {
            $this->setFlash('error', 'Failed to create broker');
            return $this->back();
        }
    }

    public function edit($id)
    {
        $broker = $this->brokerModel->find($id);

        if (!$broker) {
            $this->setFlash('error', 'Broker not found');
            return $this->redirect('/masters/brokers');
        }

        $accounts = $this->accountModel->where(['status' => 'active'], 'account_name ASC');

        $this->view('masters/brokers/edit', [
            'broker' => $broker,
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

        $broker = $this->brokerModel->find($id);

        if (!$broker) {
            $this->setFlash('error', 'Broker not found');
            return $this->redirect('/masters/brokers');
        }

        $errors = $this->validate($_POST, [
            'code' => 'required|max:50',
            'name' => 'required|min:2|max:255',
            'commission_rate' => 'required|numeric'
        ]);

        // Check if code exists (excluding current)
        $existing = $this->db->fetch("
            SELECT * FROM brokers WHERE code = :code AND id != :id
        ", ['code' => $_POST['code'], 'id' => $id]);

        if ($existing) {
            $errors['code'] = 'Broker code already exists';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $updated = $this->brokerModel->update($id, [
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
            $this->setFlash('success', 'Broker updated successfully');
            $this->redirect('/masters/brokers');
        } else {
            $this->setFlash('error', 'Failed to update broker');
            return $this->back();
        }
    }

    public function destroy($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        // Check if broker has commissions
        $hasCommissions = $this->db->fetch("
            SELECT COUNT(*) as count FROM commissions WHERE broker_id = :id
        ", ['id' => $id])['count'];

        if ($hasCommissions > 0) {
            $this->setFlash('error', 'Cannot delete broker with existing commissions');
            return $this->back();
        }

        $deleted = $this->brokerModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'Broker deleted successfully');
        } else {
            $this->setFlash('error', 'Failed to delete broker');
        }

        $this->redirect('/masters/brokers');
    }
}
