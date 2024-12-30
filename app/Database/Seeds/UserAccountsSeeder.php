<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserAccountsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username'     => 'admin',
                'password'     => password_hash('adminrptracibubur@admin.com', PASSWORD_DEFAULT),
                'employee_id'  => '0003',
                'user_role_id' => 1,
            ],
            [
                'username'     => 'admin2',
                'password'     => password_hash('adminrptracibubur02@admin.com', PASSWORD_DEFAULT),
                'employee_id'  => '0004',
                'user_role_id' => 1,
            ],
        ];

        // Insert batch data ke tabel user_accounts
        $this->db->table('user_accounts')->insertBatch($data);
    }
}
