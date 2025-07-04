<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Biodata <?= esc($department_current['department_name']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 50px !important;
            height: 50px !important;
            object-fit: cover;
        }

        .header h2 {
            margin: 10px 0 0 0;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
        }

        .details th,
        .details td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .details th {
            background-color: #f2f2f2;
            text-align: left;
            width: 30%;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Biodata <?= esc($department_current['department_name']); ?></h2>
    </div>

    <table class="details">
        <?php
        $details = [
            'Nama' => $employee['employee_name'],
            'Jenis Kelamin' => $employee['gender'] == 'Laki-Laki' ? 'Laki-Laki' : 'Perempuan',
            'Tempat/Tanggal Lahir' => ($employee['birth_place'] ?? '[Belum diisi]') . ', ' . date('d F Y', strtotime($employee['birth_date'])),
            'Status Perkawinan' => $employee['marital_status'],
            'Jumlah Anak' => $employee['num_children'] ?? 0,
            'Pendidikan' => $employee['education'],
            'Email' => $employee['email'],
            'No. Telepon' => $employee['telephone'],
            'Alamat' => $employee['employee_address'],
            'Tanggal Bergabung' => date('d F Y', strtotime($employee['hire_date'])),
            'Penggunaan Kontrasepsi' => $employee['contraceptive_use'] ?? 'Tidak',
            'Department' => $department_current['department_name'],
        ];

        $rptraDetails = [
            'Nama RPTRA' => $employee['rptra_name'],
            'Alamat RPTRA' => $employee['rptra_address'],
        ];
        ?>

        <?php foreach ($details as $label => $value) : ?>
            <tr>
                <th><?= esc($label); ?></th>
                <td><?= esc($value); ?></td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <th colspan="2" style="background-color: #e9ecef; text-align: center;"><strong>Saat ini Mengelola:</strong></th>
        </tr>
        <?php foreach ($rptraDetails as $label => $value) : ?>
            <tr>
                <th><?= esc($label); ?></th>
                <td><?= esc($value); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>

</html>