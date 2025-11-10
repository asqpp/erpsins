<?php

namespace App\Models;

use Core\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'name', 'email', 'phone', 'company', 'address', 'city',
        'state', 'zip', 'country', 'tax_id', 'credit_limit',
        'balance', 'status', 'notes', 'created_by'
    ];

    public function getWithInvoiceStats()
    {
        return $this->db->fetchAll("
            SELECT c.*,
                   COUNT(i.id) as invoice_count,
                   COALESCE(SUM(CASE WHEN i.status = 'paid' THEN i.total ELSE 0 END), 0) as total_paid,
                   COALESCE(SUM(CASE WHEN i.status IN ('sent', 'overdue') THEN i.total ELSE 0 END), 0) as total_pending
            FROM customers c
            LEFT JOIN invoices i ON c.id = i.customer_id
            WHERE c.status = 'active'
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
    }
}
