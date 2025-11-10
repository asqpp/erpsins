<?php

namespace App\Models;

use Core\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'sku', 'name', 'description', 'category_id', 'price', 'cost',
        'quantity', 'min_quantity', 'unit', 'barcode', 'image',
        'status', 'created_by'
    ];

    public function getWithCategory()
    {
        return $this->db->fetchAll("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.name ASC
        ");
    }

    public function updateStock($productId, $quantity, $operation = 'add')
    {
        $product = $this->find($productId);
        if (!$product) {
            return false;
        }

        $newQuantity = $operation === 'add'
            ? $product['quantity'] + $quantity
            : $product['quantity'] - $quantity;

        if ($newQuantity < 0) {
            return false;
        }

        return $this->update($productId, ['quantity' => $newQuantity]);
    }
}
