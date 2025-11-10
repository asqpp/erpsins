<?php

namespace App\Models;

use Core\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $fillable = [
        'invoice_number', 'customer_id', 'invoice_date', 'due_date',
        'subtotal', 'tax_rate', 'tax_amount', 'discount', 'total',
        'paid_amount', 'status', 'notes', 'terms', 'created_by'
    ];

    public function getWithCustomer($id)
    {
        return $this->db->fetch("
            SELECT i.*, c.name as customer_name, c.email as customer_email,
                   c.phone as customer_phone, c.company, c.address, c.city,
                   c.state, c.zip, c.country
            FROM invoices i
            LEFT JOIN customers c ON i.customer_id = c.id
            WHERE i.id = :id
        ", ['id' => $id]);
    }

    public function getItems($invoiceId)
    {
        return $this->db->fetchAll("
            SELECT ii.*, p.name as product_name, p.sku
            FROM invoice_items ii
            LEFT JOIN products p ON ii.product_id = p.id
            WHERE ii.invoice_id = :invoice_id
            ORDER BY ii.id ASC
        ", ['invoice_id' => $invoiceId]);
    }

    public function createWithItems($invoiceData, $items)
    {
        $this->db->beginTransaction();

        try {
            // Generate invoice number
            $lastInvoice = $this->db->fetch("SELECT invoice_number FROM invoices ORDER BY id DESC LIMIT 1");
            if ($lastInvoice) {
                $lastNumber = (int)substr($lastInvoice['invoice_number'], -3);
                $invoiceData['invoice_number'] = 'INV-' . date('Y') . '-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $invoiceData['invoice_number'] = 'INV-' . date('Y') . '-001';
            }

            // Create invoice
            $invoiceId = $this->create($invoiceData);

            // Create invoice items
            foreach ($items as $item) {
                $this->db->insert('invoice_items', [
                    'invoice_id' => $invoiceId,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total']
                ]);
            }

            $this->db->commit();
            return $invoiceId;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function generateInvoiceNumber()
    {
        $lastInvoice = $this->db->fetch("SELECT invoice_number FROM invoices ORDER BY id DESC LIMIT 1");

        if ($lastInvoice) {
            $lastNumber = (int)substr($lastInvoice['invoice_number'], -3);
            return 'INV-' . date('Y') . '-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        return 'INV-' . date('Y') . '-001';
    }
}
