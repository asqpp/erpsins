<?php

namespace App\Controllers\Api;

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

    public function search()
    {
        $query = $_GET['q'] ?? '';

        if (empty($query)) {
            return $this->json(['products' => []]);
        }

        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE (p.name LIKE :query OR p.sku LIKE :query OR p.description LIKE :query)
                AND p.status = 'active'
                ORDER BY p.name ASC
                LIMIT 10";

        $products = $this->db->fetchAll($sql, ['query' => "%{$query}%"]);

        $this->json(['products' => $products]);
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
            return $this->json(['error' => 'Product not found'], 404);
        }

        $this->json(['product' => $product]);
    }
}
