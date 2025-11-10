<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
    }

    public function index()
    {
        $products = $this->productModel->getWithCategory();

        $categories = $this->db->fetchAll("SELECT * FROM categories WHERE status = 'active' ORDER BY name");

        $this->view('products/index', [
            'products' => $products,
            'categories' => $categories,
            'user' => auth()->user()
        ]);
    }

    public function create()
    {
        $categories = $this->db->fetchAll("SELECT * FROM categories WHERE status = 'active' ORDER BY name");

        $this->view('products/create', [
            'categories' => $categories,
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
            'sku' => 'required|max:100',
            'name' => 'required|min:2|max:255',
            'price' => 'required|numeric',
        ]);

        // Check if SKU exists
        $existing = $this->productModel->first(['sku' => $_POST['sku']]);
        if ($existing) {
            $errors['sku'] = 'SKU already exists';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $productId = $this->productModel->create([
            'sku' => $_POST['sku'],
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? null,
            'category_id' => $_POST['category_id'] ?? null,
            'price' => $_POST['price'],
            'cost' => $_POST['cost'] ?? 0,
            'quantity' => $_POST['quantity'] ?? 0,
            'min_quantity' => $_POST['min_quantity'] ?? 10,
            'unit' => $_POST['unit'] ?? 'pcs',
            'barcode' => $_POST['barcode'] ?? null,
            'status' => 'active',
            'created_by' => auth()->id()
        ]);

        if ($productId) {
            $this->setFlash('success', 'Product created successfully');
            $this->redirect('/products');
        } else {
            $this->setFlash('error', 'Failed to create product');
            return $this->back();
        }
    }

    public function show($id)
    {
        $product = $this->db->fetch("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
        ", ['id' => $id]);

        if (!$product) {
            $this->setFlash('error', 'Product not found');
            return $this->redirect('/products');
        }

        $this->view('products/show', [
            'product' => $product,
            'user' => auth()->user()
        ]);
    }

    public function edit($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            $this->setFlash('error', 'Product not found');
            return $this->redirect('/products');
        }

        $categories = $this->db->fetchAll("SELECT * FROM categories WHERE status = 'active' ORDER BY name");

        $this->view('products/edit', [
            'product' => $product,
            'categories' => $categories,
            'user' => auth()->user()
        ]);
    }

    public function update($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $product = $this->productModel->find($id);

        if (!$product) {
            $this->setFlash('error', 'Product not found');
            return $this->redirect('/products');
        }

        $errors = $this->validate($_POST, [
            'sku' => 'required|max:100',
            'name' => 'required|min:2|max:255',
            'price' => 'required|numeric',
        ]);

        // Check if SKU exists (excluding current product)
        $existing = $this->db->fetch("
            SELECT * FROM products WHERE sku = :sku AND id != :id
        ", ['sku' => $_POST['sku'], 'id' => $id]);

        if ($existing) {
            $errors['sku'] = 'SKU already exists';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $updated = $this->productModel->update($id, [
            'sku' => $_POST['sku'],
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? null,
            'category_id' => $_POST['category_id'] ?? null,
            'price' => $_POST['price'],
            'cost' => $_POST['cost'] ?? 0,
            'quantity' => $_POST['quantity'] ?? 0,
            'min_quantity' => $_POST['min_quantity'] ?? 10,
            'unit' => $_POST['unit'] ?? 'pcs',
            'barcode' => $_POST['barcode'] ?? null,
            'status' => $_POST['status'] ?? 'active'
        ]);

        if ($updated !== false) {
            $this->setFlash('success', 'Product updated successfully');
            $this->redirect('/products/' . $id);
        } else {
            $this->setFlash('error', 'Failed to update product');
            return $this->back();
        }
    }

    public function destroy($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $deleted = $this->productModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'Product deleted successfully');
        } else {
            $this->setFlash('error', 'Failed to delete product');
        }

        $this->redirect('/products');
    }
}
