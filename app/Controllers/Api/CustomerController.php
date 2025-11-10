<?php

namespace App\Controllers\Api;

use Core\Controller;
use App\Models\Customer;

class CustomerController extends Controller
{
    private $customerModel;

    public function __construct()
    {
        parent::__construct();
        $this->customerModel = new Customer();
    }

    public function search()
    {
        $query = $_GET['q'] ?? '';

        if (empty($query)) {
            return $this->json(['customers' => []]);
        }

        $sql = "SELECT * FROM customers
                WHERE (name LIKE :query OR email LIKE :query OR company LIKE :query)
                AND status = 'active'
                ORDER BY name ASC
                LIMIT 10";

        $customers = $this->db->fetchAll($sql, ['query' => "%{$query}%"]);

        $this->json(['customers' => $customers]);
    }
}
