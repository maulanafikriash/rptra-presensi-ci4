<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function getAccount($username)
    {
        $account = $this->db->table('user_account')->where('username', $username)->get()->getRowArray();
        if (!$account) {
            return null;
        }

        $e_id = $account['employee_id'];

        $employee = $this->db->table('employee')
            ->select('employee.*, department.department_name')
            ->join('department', 'employee.department_id = department.department_id', 'left')
            ->where('employee.employee_id', $e_id)
            ->get()
            ->getRowArray();

        return $employee;
    }

    public function updateUserAccount($employeeId, $data)
    {
        return $this->db->table('user_account')
            ->where('employee_id', $employeeId)
            ->update($data);
    }

    public function getUserByUsername($username)
    {
        return $this->db->table('user_account')
            ->select('
            user_account.username,
            user_account.password,
            user_account.user_role_id,
            user_role.user_role_name,
            user_account.employee_id,
            employee.rptra_name,
            employee.rptra_address
        ')
            ->join('user_role', 'user_account.user_role_id = user_role.user_role_id')
            ->join('employee', 'user_account.employee_id = employee.employee_id')
            ->where('user_account.username', $username)
            ->get()
            ->getRow();
    }
}
