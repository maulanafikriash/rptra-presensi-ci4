<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'department_id'   => 'ADM',
                'department_name' => 'Administrator',
            ],
            [
                'department_id'   => 'PLA',
                'department_name' => 'Pengelola',
            ],
        ];
        // Insert batch data ke tabel department
        $this->db->table('department')->insertBatch($data);
    }
}
