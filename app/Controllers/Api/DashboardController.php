<?php

namespace App\Controllers\Api;

use Core\Controller;

class DashboardController extends Controller
{
    public function stats()
    {
        $stats = [
            'total_customers' => $this->db->fetch("SELECT COUNT(*) as count FROM customers WHERE status = 'active'")['count'] ?? 0,
            'total_products' => $this->db->fetch("SELECT COUNT(*) as count FROM products WHERE status = 'active'")['count'] ?? 0,
            'total_invoices' => $this->db->fetch("SELECT COUNT(*) as count FROM invoices")['count'] ?? 0,
            'pending_invoices' => $this->db->fetch("SELECT COUNT(*) as count FROM invoices WHERE status IN ('sent', 'overdue')")['count'] ?? 0,
        ];

        $revenue = $this->db->fetch("SELECT
            SUM(CASE WHEN status = 'paid' THEN total ELSE 0 END) as paid,
            SUM(CASE WHEN status IN ('sent', 'overdue') THEN total ELSE 0 END) as pending,
            SUM(total) as total
            FROM invoices
        ");

        $stats['revenue_paid'] = $revenue['paid'] ?? 0;
        $stats['revenue_pending'] = $revenue['pending'] ?? 0;
        $stats['revenue_total'] = $revenue['total'] ?? 0;

        // Monthly revenue for chart
        $monthlyRevenue = $this->db->fetchAll("
            SELECT
                DATE_FORMAT(invoice_date, '%Y-%m') as month,
                SUM(total) as revenue
            FROM invoices
            WHERE invoice_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY month
            ORDER BY month ASC
        ");

        $stats['monthly_revenue'] = $monthlyRevenue;

        $this->json($stats);
    }
}
