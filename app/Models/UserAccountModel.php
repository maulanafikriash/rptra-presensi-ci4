<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAccountModel extends Model
{
    protected $table = 'user_account';
    protected $primaryKey = 'username';
    protected $allowedFields = ['username', 'password', 'employee_id', 'user_role_id'];

    public function getAllUsersWithEmployee()
    {
        $query = "
            SELECT 
                user_account.username AS u_username,
                employee.employee_id AS e_id,
                employee.employee_name AS e_name,
                employee.department_id AS d_id
            FROM employee
            LEFT JOIN user_account ON user_account.employee_id = employee.employee_id
        ";
        return $this->db->query($query)->getResultArray();
    }

    public function getUserByEmployeeId($employeeId)
    {
        return $this->where('employee_id', $employeeId)->first();
    }

    public function getEmployeeById($employeeId)
    {
        return $this->db->table('employee')->where('employee_id', $employeeId)->get()->getRowArray();
    }

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function addUser($data)
    {
        $this->insert($data);
    }

    public function updateUser($data, $username)
    {
        $this->update($username, $data);
    }

    public function deleteUser($username)
    {
        $this->delete($username);
    }

    public function deleteUserAttendance($username)
    {
        $this->db->table('attendance')->set('username', NULL)->where('username', $username)->update();
    }
}
