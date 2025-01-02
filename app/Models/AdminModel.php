<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function getDataForDashboard()
    {
        // Ambil data employee, department, dan user
        return [
            'c_shift' => $this->db->table('shift')->countAllResults(),
            'employee' => $this->db->table('employee')->get()->getResultArray(),
            'c_employee' => $this->db->table('employee')->countAllResults(),
            'department' => $this->db->table('department')->get()->getResultArray(),
            'c_department' => $this->db->table('department')->countAllResults(),
            'users' => $this->db->table('user_account')->get()->getResultArray(),
            'c_users' => $this->db->table('user_account')->countAllResults(),
        ];
    }

    public function getDepartment()
    {
        // Mengambil data jumlah karyawan per departemen dengan LEFT JOIN
        return $this->db->table('department')
            ->select('department.department_name AS d_name,
                      department.department_id AS d_id,
                      COUNT(attendance.employee_id) AS d_quantity')
            ->join('attendance', 'department.department_id = attendance.department_id', 'left')
            ->groupBy('d_name')
            ->get()
            ->getResultArray();
    }

    public function getDepartmentEmployees($d_id)
    {
        return $this->db->table('attendance')
            ->select('attendance.employee_id AS e_id,
                      employee.employee_name AS e_name,
                      employee.image AS e_image,
                      employee.hire_date AS e_hdate')
            ->join('employee', 'attendance.employee_id = employee.employee_id')
            ->where('attendance.department_id', $d_id)
            ->get()
            ->getResultArray();
    }

    public function getEmployeeCountByDepartment()
    {
        return $this->db->table('department d')
            ->select('d.department_id AS d_id, d.department_name AS d_name, COUNT(e.employee_id) AS qty')
            ->join('employee e', 'd.department_id = e.department_id', 'left')
            ->groupBy('d.department_id')
            ->orderBy('d.department_id', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getShiftCount()
    {
        return $this->db->table('shift')->countAllResults();
    }

    // Metode baru untuk mengambil semua shift
    public function getAllShifts()
    {
        return $this->db->table('shift')
            ->select('shift_id, start_time, end_time')
            ->orderBy('shift_id', 'ASC')
            ->get()
            ->getResultArray();
    }
}
