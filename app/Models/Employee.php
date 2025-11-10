<?php

namespace App\Models;

use Core\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $fillable = [
        'employee_code', 'name', 'email', 'phone', 'date_of_birth',
        'date_of_joining', 'department_id', 'designation_id', 'basic_salary',
        'account_id', 'address', 'emergency_contact', 'photo',
        'status', 'created_by'
    ];

    public function getWithDepartment()
    {
        return $this->db->fetchAll("
            SELECT e.*,
                   d.name as department_name,
                   des.name as designation_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN designations des ON e.designation_id = des.id
            ORDER BY e.name
        ");
    }

    public function generateCode()
    {
        $lastEmployee = $this->db->fetch("SELECT employee_code FROM employees ORDER BY id DESC LIMIT 1");

        if ($lastEmployee) {
            $lastNumber = (int)substr($lastEmployee['employee_code'], 3);
            return 'EMP' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return 'EMP0001';
    }
}
