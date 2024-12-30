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

    public function getAdmin($username)
    {
        // Ambil data akun berdasarkan username
        $account = $this->db->table('user_accounts')->where('username', $username)->get()->getRowArray();
        if (!$account) {
            return null; // Mengembalikan null jika akun tidak ditemukan
        }

        $e_id = $account['employee_id'];

        return $this->db->table('employee')
            ->select('employee.employee_id AS id,
                      employee.employee_name AS name,
                      employee.gender AS gender,
                      employee.shift_id AS shift,
                      employee.image AS image,
                      employee.birth_date AS birth_date,
                      employee.hire_date AS hire_date')
            ->where('employee.employee_id', $e_id)
            ->get()
            ->getRowArray();
    }

    public function getDataForDashboard()
    {
        // Ambil data shift, employee, department, dan user
        return [
            'shift' => $this->db->table('shift')->get()->getResultArray(),
            'c_shift' => $this->db->table('shift')->countAllResults(),
            'employee' => $this->db->table('employee')->get()->getResultArray(),
            'c_employee' => $this->db->table('employee')->countAllResults(),
            'department' => $this->db->table('department')->get()->getResultArray(),
            'c_department' => $this->db->table('department')->countAllResults(),
            'users' => $this->db->table('user_accounts')->get()->getResultArray(),
            'c_users' => $this->db->table('user_accounts')->countAllResults(),
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
        $query = "
            SELECT 
                d.department_id AS d_id, 
                d.department_name AS d_name, 
                COUNT(e.employee_id) AS qty 
            FROM department d
            LEFT JOIN employee e ON d.department_id = e.department_id
            GROUP BY d.department_id
            ORDER BY d.department_id ASC
        ";
        return $this->db->query($query)->getResultArray();
    }

    public function getEmployeeCountByShift()
    {
        $query = "
            SELECT 
                s.shift_id AS s_id, 
                CONCAT(s.start_time, ' - ', s.end_time) AS shift_time, 
                COUNT(e.employee_id) AS qty 
            FROM shift s
            LEFT JOIN employee e ON s.shift_id = e.shift_id
            GROUP BY s.shift_id
            ORDER BY s.shift_id ASC
        ";
        return $this->db->query($query)->getResultArray();
    }

    public function queryCustom($query)
    {
        return $this->db->query($query)->getResultArray();
    }
}
