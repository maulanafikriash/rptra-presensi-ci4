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

    public function getDataForDashboard($rptraName)
    {
        // Hitung shift (global)
        $c_shift     = $this->db->table('shift')->countAllResults();

        // Ambil pegawai hanya untuk rptra yang sama
        $employeeBuilder = $this->db->table('employee')
            ->where('rptra_name', $rptraName);
        $employees      = $employeeBuilder->get()->getResultArray();
        $c_employee     = count($employees);

        $department      = $this->db->table('department')->get()->getResultArray();
        $c_department    = count($department);

        // User account untuk pegawai di rptra yang sama
        $userBuilder = $this->db->table('user_account ua')
            ->select('ua.username, ua.employee_id, e.rptra_name')
            ->join('employee e', 'ua.employee_id = e.employee_id')
            ->where('e.rptra_name', $rptraName);
        $users       = $userBuilder->get()->getResultArray();
        $c_users     = count($users);

        return [
            'c_shift'     => $c_shift,
            'employee'    => $employees,
            'c_employee'  => $c_employee,
            'department'  => $department,
            'c_department' => $c_department,
            'users'       => $users,
            'c_users'     => $c_users,
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

    public function getEmployeeCountByDepartment(string $rptraName)
    {
        return $this->db->table('department d')
            ->select('
            d.department_id AS d_id,
            d.department_name AS d_name,
            COUNT(e.employee_id)   AS qty
        ')
            ->join(
                'employee e',
                'd.department_id = e.department_id AND e.rptra_name = ' . $this->db->escape($rptraName),
                'left'
            )
            ->groupBy('d.department_id')
            ->orderBy('d.department_id', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getShiftCount()
    {
        return $this->db->table('shift')->countAllResults();
    }

    public function getAllShifts()
    {
        return $this->db->table('shift')
            ->select('shift_id, start_time, end_time')
            ->orderBy('shift_id', 'ASC')
            ->get()
            ->getResultArray();
    }
}
