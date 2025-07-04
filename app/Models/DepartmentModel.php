<?php 
namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'department';
    protected $primaryKey = 'department_id';
    protected $allowedFields = ['department_id', 'department_name'];

    public function deleteDepartmentWithRelations($d_id)
    {
        $db = db_connect();

        // Delete related attendance records
        $db->table('attendance')->where('department_id', $d_id)->delete();

        // Delete the department
        $this->delete($d_id);
    }

    public function getDepartmentById($id)
    {
        return $this->where('department_id', $id)->first();
    }
}