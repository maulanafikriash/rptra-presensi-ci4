<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'start_time' => '06:00:00',
                'end_time'   => '14:00:00',
            ],
            [
                'start_time' => '10:00:00',
                'end_time'   => '18:00:00',
            ],
            [
                'start_time' => '05:00:00',
                'end_time'   => '22:00:00',
            ],
        ];

        // Insert batch data ke tabel shift
        $this->db->table('shift')->insertBatch($data);
    }
}
