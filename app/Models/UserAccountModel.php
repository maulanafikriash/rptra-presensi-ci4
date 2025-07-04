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
        return $this->db->table('user_account ua')
            ->select([
                'ua.username AS u_username',
                'e.employee_id    AS e_id',
                'e.employee_name  AS e_name',
                'e.department_id  AS d_id',
                'e.rptra_name     AS rptra_name',
            ])
            ->join('employee e', 'ua.employee_id = e.employee_id', 'right')
            ->orderBy('e.employee_id')
            ->get()
            ->getResultArray();
    }

    public function getUserByEmployeeId($employeeId)
    {
        return $this->db->table('user_account ua')
            ->select([
                'ua.username',
                'ua.user_role_id',
                'e.employee_id',
                'e.rptra_name',
                'e.department_id'
            ])
            ->join('employee e', 'ua.employee_id = e.employee_id')
            ->where('ua.employee_id', $employeeId)
            ->get()
            ->getRowArray();
    }

    public function getEmployeeById($employeeId)
    {
        return $this->db->table('employee')
            ->select(['employee_id', 'employee_name', 'department_id', 'rptra_name'])
            ->where('employee_id', $employeeId)
            ->get()
            ->getRowArray();
    }

    public function getUserByUsername($username)
    {
        return $this->db->table('user_account ua')
            ->select([
                'ua.username',
                'ua.password',
                'ua.user_role_id',
                'e.employee_id',
                'e.rptra_name',
                'e.department_id'
            ])
            ->join('employee e', 'ua.employee_id = e.employee_id')
            ->where('ua.username', $username)
            ->get()
            ->getRowArray();
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
