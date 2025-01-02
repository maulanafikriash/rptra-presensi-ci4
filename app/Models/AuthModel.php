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

        return $this->db->table('employee')
            ->where('employee.employee_id', $e_id)
            ->get()
            ->getRowArray(); // Mengambil seluruh kolom dari tabel employee
    }

    public function updateUserAccount($employeeId, $data)
    {
        return $this->db->table('user_account')
            ->where('employee_id', $employeeId)
            ->update($data);
    }
}
