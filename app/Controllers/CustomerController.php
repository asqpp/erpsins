<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Customer;

class CustomerController extends Controller
{
    private $customerModel;

    public function __construct()
    {
        parent::__construct();
        $this->customerModel = new Customer();
    }

    public function index()
    {
        $customers = $this->customerModel->getWithInvoiceStats();

        $this->view('customers/index', [
            'customers' => $customers,
            'user' => auth()->user()
        ]);
    }

    public function create()
    {
        $this->view('customers/create', [
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
            'name' => 'required|min:2|max:255',
            'email' => 'email',
            'phone' => 'max:50',
        ]);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $customerId = $this->customerModel->create([
            'name' => $_POST['name'],
            'email' => $_POST['email'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'company' => $_POST['company'] ?? null,
            'address' => $_POST['address'] ?? null,
            'city' => $_POST['city'] ?? null,
            'state' => $_POST['state'] ?? null,
            'zip' => $_POST['zip'] ?? null,
            'country' => $_POST['country'] ?? 'USA',
            'tax_id' => $_POST['tax_id'] ?? null,
            'credit_limit' => $_POST['credit_limit'] ?? 0,
            'notes' => $_POST['notes'] ?? null,
            'status' => 'active',
            'created_by' => auth()->id()
        ]);

        if ($customerId) {
            $this->setFlash('success', 'Customer created successfully');
            $this->redirect('/customers');
        } else {
            $this->setFlash('error', 'Failed to create customer');
            return $this->back();
        }
    }

    public function show($id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            $this->setFlash('error', 'Customer not found');
            return $this->redirect('/customers');
        }

        $invoices = $this->db->fetchAll("
            SELECT * FROM invoices
            WHERE customer_id = :id
            ORDER BY created_at DESC
        ", ['id' => $id]);

        $this->view('customers/show', [
            'customer' => $customer,
            'invoices' => $invoices,
            'user' => auth()->user()
        ]);
    }

    public function edit($id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            $this->setFlash('error', 'Customer not found');
            return $this->redirect('/customers');
        }

        $this->view('customers/edit', [
            'customer' => $customer,
            'user' => auth()->user()
        ]);
    }

    public function update($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $customer = $this->customerModel->find($id);

        if (!$customer) {
            $this->setFlash('error', 'Customer not found');
            return $this->redirect('/customers');
        }

        $errors = $this->validate($_POST, [
            'name' => 'required|min:2|max:255',
            'email' => 'email',
        ]);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $updated = $this->customerModel->update($id, [
            'name' => $_POST['name'],
            'email' => $_POST['email'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'company' => $_POST['company'] ?? null,
            'address' => $_POST['address'] ?? null,
            'city' => $_POST['city'] ?? null,
            'state' => $_POST['state'] ?? null,
            'zip' => $_POST['zip'] ?? null,
            'country' => $_POST['country'] ?? 'USA',
            'tax_id' => $_POST['tax_id'] ?? null,
            'credit_limit' => $_POST['credit_limit'] ?? 0,
            'notes' => $_POST['notes'] ?? null,
            'status' => $_POST['status'] ?? 'active'
        ]);

        if ($updated) {
            $this->setFlash('success', 'Customer updated successfully');
            $this->redirect('/customers/' . $id);
        } else {
            $this->setFlash('error', 'Failed to update customer');
            return $this->back();
        }
    }

    public function destroy($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        // Check if customer has invoices
        $invoiceCount = $this->db->fetch("
            SELECT COUNT(*) as count FROM invoices WHERE customer_id = :id
        ", ['id' => $id])['count'];

        if ($invoiceCount > 0) {
            $this->setFlash('error', 'Cannot delete customer with existing invoices');
            return $this->back();
        }

        $deleted = $this->customerModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'Customer deleted successfully');
        } else {
            $this->setFlash('error', 'Failed to delete customer');
        }

        $this->redirect('/customers');
    }
}
