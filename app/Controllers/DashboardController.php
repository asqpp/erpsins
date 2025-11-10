<?php

namespace App\Controllers;

use Core\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $stats = [
            'total_customers' => $this->db->fetch("SELECT COUNT(*) as count FROM customers WHERE status = 'active'")['count'] ?? 0,
            'total_products' => $this->db->fetch("SELECT COUNT(*) as count FROM products WHERE status = 'active'")['count'] ?? 0,
            'total_invoices' => $this->db->fetch("SELECT COUNT(*) as count FROM invoices")['count'] ?? 0,
            'pending_invoices' => $this->db->fetch("SELECT COUNT(*) as count FROM invoices WHERE status IN ('sent', 'overdue')")['count'] ?? 0,
        ];

        // Revenue statistics
        $revenue = $this->db->fetch("SELECT
            SUM(CASE WHEN status = 'paid' THEN total ELSE 0 END) as paid,
            SUM(CASE WHEN status IN ('sent', 'overdue') THEN total ELSE 0 END) as pending,
            SUM(total) as total
            FROM invoices
        ");

        $stats['revenue_paid'] = $revenue['paid'] ?? 0;
        $stats['revenue_pending'] = $revenue['pending'] ?? 0;
        $stats['revenue_total'] = $revenue['total'] ?? 0;

        // Recent invoices
        $recentInvoices = $this->db->fetchAll("
            SELECT i.*, c.name as customer_name, c.company
            FROM invoices i
            LEFT JOIN customers c ON i.customer_id = c.id
            ORDER BY i.created_at DESC
            LIMIT 5
        ");

        // Low stock products
        $lowStockProducts = $this->db->fetchAll("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.quantity <= p.min_quantity AND p.status = 'active'
            ORDER BY p.quantity ASC
            LIMIT 5
        ");

        // Recent activity
        $recentActivity = $this->db->fetchAll("
            SELECT a.*, u.name as user_name
            FROM activity_log a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT 10
        ");

        $this->view('dashboard/index', [
            'stats' => $stats,
            'recentInvoices' => $recentInvoices,
            'lowStockProducts' => $lowStockProducts,
            'recentActivity' => $recentActivity,
            'user' => auth()->user()
        ]);
    }
}
