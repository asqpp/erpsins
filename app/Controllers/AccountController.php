<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Account;
use App\Models\AccountGroup;

class AccountController extends Controller
{
    private $accountModel;
    private $groupModel;

    public function __construct()
    {
        parent::__construct();
        $this->accountModel = new Account();
        $this->groupModel = new AccountGroup();
    }

    public function index()
    {
        $accounts = $this->accountModel->getWithGroup();
        $groups = $this->groupModel->all('name ASC');

        $this->view('accounting/accounts/index', [
            'accounts' => $accounts,
            'groups' => $groups,
            'user' => auth()->user()
        ]);
    }

    public function create()
    {
        $groups = $this->groupModel->where(['status' => 'active'], 'name ASC');

        $this->view('accounting/accounts/create', [
            'groups' => $groups,
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
            'account_code' => 'required|max:50',
            'account_name' => 'required|min:2|max:255',
            'group_id' => 'required|numeric'
        ]);

        // Check if account code exists
        $existing = $this->accountModel->first(['account_code' => $_POST['account_code']]);
        if ($existing) {
            $errors['account_code'] = 'Account code already exists';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $accountId = $this->accountModel->create([
            'account_code' => $_POST['account_code'],
            'account_name' => $_POST['account_name'],
            'group_id' => $_POST['group_id'],
            'subgroup_id' => $_POST['subgroup_id'] ?? null,
            'opening_balance' => $_POST['opening_balance'] ?? 0,
            'opening_balance_type' => $_POST['opening_balance_type'] ?? 'debit',
            'current_balance' => $_POST['opening_balance'] ?? 0,
            'description' => $_POST['description'] ?? null,
            'is_bank' => isset($_POST['is_bank']) ? 1 : 0,
            'is_cash' => isset($_POST['is_cash']) ? 1 : 0,
            'bank_details' => $_POST['bank_details'] ?? null,
            'status' => 'active',
            'created_by' => auth()->id()
        ]);

        if ($accountId) {
            $this->setFlash('success', 'Account created successfully');
            $this->redirect('/accounting/accounts');
        } else {
            $this->setFlash('error', 'Failed to create account');
            return $this->back();
        }
    }

    public function edit($id)
    {
        $account = $this->accountModel->find($id);

        if (!$account) {
            $this->setFlash('error', 'Account not found');
            return $this->redirect('/accounting/accounts');
        }

        if ($account['is_system']) {
            $this->setFlash('error', 'Cannot edit system account');
            return $this->redirect('/accounting/accounts');
        }

        $groups = $this->groupModel->where(['status' => 'active'], 'name ASC');

        $this->view('accounting/accounts/edit', [
            'account' => $account,
            'groups' => $groups,
            'user' => auth()->user()
        ]);
    }

    public function update($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $account = $this->accountModel->find($id);

        if (!$account || $account['is_system']) {
            $this->setFlash('error', 'Cannot update this account');
            return $this->redirect('/accounting/accounts');
        }

        $errors = $this->validate($_POST, [
            'account_code' => 'required|max:50',
            'account_name' => 'required|min:2|max:255',
            'group_id' => 'required|numeric'
        ]);

        // Check if account code exists (excluding current)
        $existing = $this->db->fetch("
            SELECT * FROM accounts WHERE account_code = :code AND id != :id
        ", ['code' => $_POST['account_code'], 'id' => $id]);

        if ($existing) {
            $errors['account_code'] = 'Account code already exists';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $updated = $this->accountModel->update($id, [
            'account_code' => $_POST['account_code'],
            'account_name' => $_POST['account_name'],
            'group_id' => $_POST['group_id'],
            'subgroup_id' => $_POST['subgroup_id'] ?? null,
            'description' => $_POST['description'] ?? null,
            'is_bank' => isset($_POST['is_bank']) ? 1 : 0,
            'is_cash' => isset($_POST['is_cash']) ? 1 : 0,
            'bank_details' => $_POST['bank_details'] ?? null,
            'status' => $_POST['status'] ?? 'active'
        ]);

        if ($updated !== false) {
            $this->setFlash('success', 'Account updated successfully');
            $this->redirect('/accounting/accounts');
        } else {
            $this->setFlash('error', 'Failed to update account');
            return $this->back();
        }
    }

    public function ledger($id)
    {
        $account = $this->accountModel->find($id);

        if (!$account) {
            $this->setFlash('error', 'Account not found');
            return $this->redirect('/accounting/accounts');
        }

        $fromDate = $_GET['from_date'] ?? date('Y-m-01');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');

        $ledger = $this->accountModel->getLedger($id, $fromDate, $toDate);

        $this->view('accounting/accounts/ledger', [
            'account' => $account,
            'ledger' => $ledger,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'user' => auth()->user()
        ]);
    }

    public function destroy($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $account = $this->accountModel->find($id);

        if ($account && $account['is_system']) {
            $this->setFlash('error', 'Cannot delete system account');
            return $this->back();
        }

        // Check if account has transactions
        $hasTransactions = $this->db->fetch("
            SELECT COUNT(*) as count FROM journal_entries WHERE account_id = :id
        ", ['id' => $id])['count'];

        if ($hasTransactions > 0) {
            $this->setFlash('error', 'Cannot delete account with existing transactions');
            return $this->back();
        }

        $deleted = $this->accountModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'Account deleted successfully');
        } else {
            $this->setFlash('error', 'Failed to delete account');
        }

        $this->redirect('/accounting/accounts');
    }
}
