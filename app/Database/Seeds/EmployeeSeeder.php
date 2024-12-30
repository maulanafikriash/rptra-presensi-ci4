<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'employee_name'     => 'Jone Doe',
                'gender'            => 'Laki-Laki',
                'marital_status'    => 'Kawin',
                'num_children'      => 2,
                'image'             => 'default.png',
                'education'         => 'S1 Teknik Industri',
                'employee_address'  => 'Jl. Merpati No. 1, Jakarta',
                'telephone'         => '081234567890',
                'email'             => 'admin@example.com',
                'birth_date'        => '1990-05-15',
                'hire_date'         => '2024-10-15',
                'contraceptive_use' => 'Tidak',
                'rptra_name'        => 'Cibubur Berseri',
                'rptra_address'     => 'Jl. H. Abdul Rahman II, RT.3/RW.10, Cibubur, Kec. Ciracas, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13720',
                'shift_id'          => 1,
                'department_id'     => 'ADM',
            ],
            [
                'employee_name'     => 'Jane Doe',
                'gender'            => 'Perempuan',
                'marital_status'    => 'Belum Kawin',
                'num_children'      => 0,
                'image'             => 'default.png',
                'education'         => 'S1 Ekonomi',
                'employee_address'  => 'Jl. Kenari No. 2, Jakarta',
                'telephone'         => '081987654321',
                'email'             => 'jane@example.com',
                'birth_date'        => '1992-03-20',
                'hire_date'         => '2024-11-18',
                'contraceptive_use' => 'Tidak',
                'rptra_name'        => 'Cibubur Berseri',
                'rptra_address'     => 'Jl. H. Abdul Rahman II, RT.3/RW.10, Cibubur, Kec. Ciracas, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13720',
                'shift_id'          => 2,
                'department_id'     => 'ADM',
            ],
        ];

        // Insert batch data ke tabel employee
        $this->db->table('employee')->insertBatch($data);
    }
}
