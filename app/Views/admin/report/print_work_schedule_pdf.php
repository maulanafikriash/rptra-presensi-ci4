<!-- app/Views/admin/report/work_schedule_pdf.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Kerja Pegawai</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Jadwal Kerja Pegawai</h2>
    <p>Nama Pegawai: <?= esc($employee['employee_name']); ?></p>
    <p>Departemen: <?= esc($department['department_name']); ?></p>
    <p>Bulan: <?= date('F', mktime(0, 0, 0, $month, 10)) . ' ' . $year; ?></p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Shift</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($workSchedules as $ws):
                if ($ws['schedule_status'] === null && $ws['shift_id'] !== null) {
                    $status = 'Shift Kerja';
                    $shiftDetail = $shiftModel->find($ws['shift_id']);
                    $shiftTime = $shiftDetail['start_time'] . ' - ' . $shiftDetail['end_time'];
                } elseif ($ws['schedule_status'] === 4) {
                    $status = 'Cuti';
                    $shiftTime = '-';
                } elseif ($ws['schedule_status'] === 5) {
                    $status = 'Libur';
                    $shiftTime = '-';
                } else {
                    $status = 'Tidak Ada Jadwal';
                    $shiftTime = '-';
                }
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d-m-Y', strtotime($ws['schedule_date'])); ?></td>
                    <td><?= $status; ?></td>
                    <td><?= $shiftTime; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
