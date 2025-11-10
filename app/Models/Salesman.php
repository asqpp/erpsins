<?php

namespace App\Models;

use Core\Model;

class Salesman extends Model
{
    protected $table = 'salesmen';
    protected $fillable = [
        'code', 'name', 'email', 'phone', 'address',
        'commission_rate', 'account_id', 'status', 'created_by'
    ];

    public function getWithStats()
    {
        return $this->db->fetchAll("
            SELECT s.*,
                   COUNT(DISTINCT c.id) as total_commissions,
                   COALESCE(SUM(CASE WHEN c.status = 'pending' THEN c.commission_amount ELSE 0 END), 0) as pending_amount,
                   COALESCE(SUM(CASE WHEN c.status = 'paid' THEN c.commission_amount ELSE 0 END), 0) as paid_amount
            FROM salesmen s
            LEFT JOIN commissions c ON s.id = c.salesman_id
            WHERE s.status = 'active'
            GROUP BY s.id
            ORDER BY s.name
        ");
    }

    public function generateCode()
    {
        $lastSalesman = $this->db->fetch("SELECT code FROM salesmen ORDER BY id DESC LIMIT 1");

        if ($lastSalesman) {
            $lastNumber = (int)substr($lastSalesman['code'], 2);
            return 'SM' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        return 'SM001';
    }
}
