<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table = 'employee';
    protected $primaryKey = 'employee_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'employee_id',
        'employee_name',
        'gender',
        'marital_status',
        'num_children',
        'image',
        'education',
        'employee_address',
        'telephone',
        'email',
        'birth_place',
        'birth_date',
        'hire_date',
        'contraceptive_use',
        'rptra_name',
        'rptra_address',
        'department_id'
    ];

    public function getDepartments($departmentId = null)
    {
        if ($departmentId) {
            return $this->db->table('department')
                ->where('department_id', $departmentId)
                ->get()
                ->getRowArray();
        }
        return $this->db->table('department')->get()->getResultArray();
    }

    public function findEmployeeWithRelations($id)
    {
        return $this->db->table($this->table)
            ->select('employee.*, department.department_name')
            ->join('department', 'employee.department_id = department.department_id', 'left')
            ->where('employee_id', $id)
            ->get()
            ->getRowArray();
    }

    // Mengambil semua data karyawan berdasarkan username
    public function getAllEmployeeData($username)
    {
        $user = $this->db->table('user_account')
            ->where('username', $username)
            ->get()
            ->getRowArray();

        if (!$user) {
            return null;
        }

        // Mengambil data pegawai berdasarkan employee_id
        return $this->db->table('employee')
            ->select('employee.employee_id AS id,
            employee.employee_name AS name,
            employee.gender,
            employee.marital_status,
            employee.num_children,
            employee.image,
            employee.education,
            employee.employee_address,
            employee.telephone,
            employee.email,
            employee.birth_place,
            employee.birth_date,
            employee.hire_date,
            employee.contraceptive_use,
            employee.rptra_name,
            employee.rptra_address,
            department.department_name AS department')
            ->join('department', 'employee.department_id = department.department_id', 'left')
            ->where('employee.employee_id', $user['employee_id'])
            ->get()
            ->getRowArray();
    }

    public function deleteEmployeeWithRelations($id)
    {
        $db = db_connect();
        $db->table('attendance')->where('employee_id', $id)->delete();
        $db->table('user_account')->where('employee_id', $id)->delete();

        $this->delete($id);
    }
}
