<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Employee;

class EmployeeController extends Controller
{
    private $employeeModel;

    public function __construct()
    {
        parent::__construct();
        $this->employeeModel = new Employee();
    }

    public function index()
    {
        $employees = $this->employeeModel->getWithDepartment();

        $this->view('hr/employees/index', [
            'employees' => $employees,
            'user' => auth()->user()
        ]);
    }

    public function create()
    {
        $departments = $this->db->fetchAll("SELECT * FROM departments WHERE status = 'active' ORDER BY name");
        $designations = $this->db->fetchAll("SELECT * FROM designations WHERE status = 'active' ORDER BY name");
        $nextCode = $this->employeeModel->generateCode();

        $this->view('hr/employees/create', [
            'departments' => $departments,
            'designations' => $designations,
            'nextCode' => $nextCode,
            'user' => auth()->user()
        ]);
    }

    public function store()
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $errors = $this->validate($_POST, [
            'employee_code' => 'required|max:50',
            'name' => 'required|min:2|max:255',
            'date_of_joining' => 'required',
            'basic_salary' => 'required|numeric'
        ]);

        $existing = $this->employeeModel->first(['employee_code' => $_POST['employee_code']]);
        if ($existing) {
            $errors['employee_code'] = 'Employee code already exists';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $employeeId = $this->employeeModel->create([
            'employee_code' => $_POST['employee_code'],
            'name' => $_POST['name'],
            'email' => $_POST['email'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
            'date_of_joining' => $_POST['date_of_joining'],
            'department_id' => $_POST['department_id'] ?? null,
            'designation_id' => $_POST['designation_id'] ?? null,
            'basic_salary' => $_POST['basic_salary'],
            'address' => $_POST['address'] ?? null,
            'emergency_contact' => $_POST['emergency_contact'] ?? null,
            'status' => 'active',
            'created_by' => auth()->id()
        ]);

        if ($employeeId) {
            $this->setFlash('success', 'Employee created successfully');
            $this->redirect('/hr/employees');
        } else {
            $this->setFlash('error', 'Failed to create employee');
            return $this->back();
        }
    }

    public function edit($id)
    {
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            $this->setFlash('error', 'Employee not found');
            return $this->redirect('/hr/employees');
        }

        $departments = $this->db->fetchAll("SELECT * FROM departments WHERE status = 'active' ORDER BY name");
        $designations = $this->db->fetchAll("SELECT * FROM designations WHERE status = 'active' ORDER BY name");

        $this->view('hr/employees/edit', [
            'employee' => $employee,
            'departments' => $departments,
            'designations' => $designations,
            'user' => auth()->user()
        ]);
    }

    public function update($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            $this->setFlash('error', 'Employee not found');
            return $this->redirect('/hr/employees');
        }

        $errors = $this->validate($_POST, [
            'employee_code' => 'required|max:50',
            'name' => 'required|min:2|max:255',
            'date_of_joining' => 'required',
            'basic_salary' => 'required|numeric'
        ]);

        $existing = $this->db->fetch("
            SELECT * FROM employees WHERE employee_code = :code AND id != :id
        ", ['code' => $_POST['employee_code'], 'id' => $id]);

        if ($existing) {
            $errors['employee_code'] = 'Employee code already exists';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        $updated = $this->employeeModel->update($id, [
            'employee_code' => $_POST['employee_code'],
            'name' => $_POST['name'],
            'email' => $_POST['email'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
            'date_of_joining' => $_POST['date_of_joining'],
            'department_id' => $_POST['department_id'] ?? null,
            'designation_id' => $_POST['designation_id'] ?? null,
            'basic_salary' => $_POST['basic_salary'],
            'address' => $_POST['address'] ?? null,
            'emergency_contact' => $_POST['emergency_contact'] ?? null,
            'status' => $_POST['status'] ?? 'active'
        ]);

        if ($updated !== false) {
            $this->setFlash('success', 'Employee updated successfully');
            $this->redirect('/hr/employees');
        } else {
            $this->setFlash('error', 'Failed to update employee');
            return $this->back();
        }
    }

    public function destroy($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $deleted = $this->employeeModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'Employee deleted successfully');
        } else {
            $this->setFlash('error', 'Failed to delete employee');
        }

        $this->redirect('/hr/employees');
    }
}
