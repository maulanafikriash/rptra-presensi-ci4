<?php

namespace App\Models;

use CodeIgniter\Model;

class ShiftModel extends Model
{
    protected $table = 'shift';
    protected $primaryKey = 'shift_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['start_time', 'end_time'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    public function findShiftById($id)
    {
        // Mengambil data shift berdasarkan shift_id
        return $this->db->table($this->table)
            ->where('shift_id', $id)
            ->get()
            ->getRowArray();
    }

    public function getAllShifts()
    {
        return $this->findAll();
    }

    public function deleteShift($s_id)
    {
        $db = \Config\Database::connect();
        $db->table('schedule')->where('shift_id', $s_id)->update(['shift_id' => NULL]);

        $this->delete($s_id);
        $db->query('ALTER TABLE `shift` AUTO_INCREMENT = 1');
    }
}
