<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'user_role_id' => 1,
                'user_role_name' => 'Admin',
            ],
            [
                'user_role_id' => 2,
                'user_role_name' => 'Employee',
            ],
        ];

        $this->db->table('user_role')->insertBatch($data);
    }
}
