<?php

namespace App\Models;

use Core\Model;

class Broker extends Model
{
    protected $table = 'brokers';
    protected $fillable = [
        'code', 'name', 'email', 'phone', 'address',
        'commission_rate', 'account_id', 'status', 'created_by'
    ];

    public function getWithStats()
    {
        return $this->db->fetchAll("
            SELECT b.*,
                   COUNT(DISTINCT c.id) as total_commissions,
                   COALESCE(SUM(CASE WHEN c.status = 'pending' THEN c.commission_amount ELSE 0 END), 0) as pending_amount,
                   COALESCE(SUM(CASE WHEN c.status = 'paid' THEN c.commission_amount ELSE 0 END), 0) as paid_amount
            FROM brokers b
            LEFT JOIN commissions c ON b.id = c.broker_id
            WHERE b.status = 'active'
            GROUP BY b.id
            ORDER BY b.name
        ");
    }

    public function generateCode()
    {
        $lastBroker = $this->db->fetch("SELECT code FROM brokers ORDER BY id DESC LIMIT 1");

        if ($lastBroker) {
            $lastNumber = (int)substr($lastBroker['code'], 3);
            return 'BRK' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        return 'BRK001';
    }
}
