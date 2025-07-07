<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserAccountSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username'     => 'admincb',
                'password'     => password_hash('rptracibuburberseri@admin.com', PASSWORD_DEFAULT),
                'employee_id'  => '0003',
                'user_role_id' => 1,
            ],
            [
                'username'     => 'adminsc',
                'password'     => password_hash('rptrasusukanceria@admin.com', PASSWORD_DEFAULT),
                'employee_id'  => '0004',
                'user_role_id' => 1,
            ],
        ];

        // Insert batch data ke tabel user_accounts
        $this->db->table('user_account')->insertBatch($data);
    }
}
