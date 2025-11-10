<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;

class InvoiceController extends Controller
{
    private $invoiceModel;
    private $customerModel;
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->invoiceModel = new Invoice();
        $this->customerModel = new Customer();
        $this->productModel = new Product();
    }

    public function index()
    {
        $invoices = $this->db->fetchAll("
            SELECT i.*, c.name as customer_name, c.company
            FROM invoices i
            LEFT JOIN customers c ON i.customer_id = c.id
            ORDER BY i.created_at DESC
        ");

        $this->view('invoices/index', [
            'invoices' => $invoices,
            'user' => auth()->user()
        ]);
    }

    public function create()
    {
        $customers = $this->customerModel->where(['status' => 'active'], 'name ASC');
        $products = $this->productModel->where(['status' => 'active'], 'name ASC');
        $nextInvoiceNumber = $this->invoiceModel->generateInvoiceNumber();

        $this->view('invoices/create', [
            'customers' => $customers,
            'products' => $products,
            'nextInvoiceNumber' => $nextInvoiceNumber,
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
            'customer_id' => 'required|numeric',
            'invoice_date' => 'required',
            'due_date' => 'required',
        ]);

        if (empty($_POST['items']) || !is_array($_POST['items'])) {
            $errors['items'] = 'At least one item is required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        // Prepare invoice data
        $invoiceData = [
            'customer_id' => $_POST['customer_id'],
            'invoice_date' => $_POST['invoice_date'],
            'due_date' => $_POST['due_date'],
            'subtotal' => $_POST['subtotal'] ?? 0,
            'tax_rate' => $_POST['tax_rate'] ?? 0,
            'tax_amount' => $_POST['tax_amount'] ?? 0,
            'discount' => $_POST['discount'] ?? 0,
            'total' => $_POST['total'] ?? 0,
            'status' => $_POST['status'] ?? 'draft',
            'notes' => $_POST['notes'] ?? null,
            'terms' => $_POST['terms'] ?? null,
            'created_by' => auth()->id()
        ];

        // Prepare items
        $items = [];
        foreach ($_POST['items'] as $item) {
            if (!empty($item['description'])) {
                $items[] = [
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'total' => $item['total'] ?? 0
                ];
            }
        }

        try {
            $invoiceId = $this->invoiceModel->createWithItems($invoiceData, $items);

            $this->setFlash('success', 'Invoice created successfully');
            $this->redirect('/invoices/' . $invoiceId);
        } catch (\Exception $e) {
            $this->setFlash('error', 'Failed to create invoice: ' . $e->getMessage());
            return $this->back();
        }
    }

    public function show($id)
    {
        $invoice = $this->invoiceModel->getWithCustomer($id);

        if (!$invoice) {
            $this->setFlash('error', 'Invoice not found');
            return $this->redirect('/invoices');
        }

        $items = $this->invoiceModel->getItems($id);

        $payments = $this->db->fetchAll("
            SELECT * FROM payments
            WHERE invoice_id = :id
            ORDER BY payment_date DESC
        ", ['id' => $id]);

        $this->view('invoices/show', [
            'invoice' => $invoice,
            'items' => $items,
            'payments' => $payments,
            'user' => auth()->user()
        ]);
    }

    public function edit($id)
    {
        $invoice = $this->invoiceModel->find($id);

        if (!$invoice || $invoice['status'] === 'paid') {
            $this->setFlash('error', 'Invoice cannot be edited');
            return $this->redirect('/invoices/' . $id);
        }

        $customers = $this->customerModel->where(['status' => 'active'], 'name ASC');
        $products = $this->productModel->where(['status' => 'active'], 'name ASC');
        $items = $this->invoiceModel->getItems($id);

        $this->view('invoices/edit', [
            'invoice' => $invoice,
            'items' => $items,
            'customers' => $customers,
            'products' => $products,
            'user' => auth()->user()
        ]);
    }

    public function update($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $invoice = $this->invoiceModel->find($id);

        if (!$invoice) {
            $this->setFlash('error', 'Invoice not found');
            return $this->redirect('/invoices');
        }

        $updated = $this->invoiceModel->update($id, [
            'customer_id' => $_POST['customer_id'],
            'invoice_date' => $_POST['invoice_date'],
            'due_date' => $_POST['due_date'],
            'subtotal' => $_POST['subtotal'] ?? 0,
            'tax_rate' => $_POST['tax_rate'] ?? 0,
            'tax_amount' => $_POST['tax_amount'] ?? 0,
            'discount' => $_POST['discount'] ?? 0,
            'total' => $_POST['total'] ?? 0,
            'status' => $_POST['status'] ?? 'draft',
            'notes' => $_POST['notes'] ?? null,
            'terms' => $_POST['terms'] ?? null
        ]);

        if ($updated !== false) {
            $this->setFlash('success', 'Invoice updated successfully');
            $this->redirect('/invoices/' . $id);
        } else {
            $this->setFlash('error', 'Failed to update invoice');
            return $this->back();
        }
    }

    public function destroy($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $invoice = $this->invoiceModel->find($id);

        if ($invoice && $invoice['status'] === 'paid') {
            $this->setFlash('error', 'Cannot delete paid invoice');
            return $this->back();
        }

        $deleted = $this->invoiceModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'Invoice deleted successfully');
        } else {
            $this->setFlash('error', 'Failed to delete invoice');
        }

        $this->redirect('/invoices');
    }
}
