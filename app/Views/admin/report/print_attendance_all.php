<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Laporan Kehadiran Pegawai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 20px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .department-info, .date-range {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        
    </style>
</head>

<body>
    <div class="container">
        <?php
        function formatTanggalIndonesia($tanggal)
        {
            $fmt = new IntlDateFormatter(
                'id_ID',
                IntlDateFormatter::FULL,
                IntlDateFormatter::NONE,
                'Asia/Jakarta',
                IntlDateFormatter::GREGORIAN
            );
            $fmt->setPattern('EEEE, dd MMMM yyyy');
            return $fmt->format(new DateTime($tanggal));
        }
        ?>
        <div class="header-section">
            <h2>Laporan Kehadiran Pegawai</h2>
        </div>
        <div class="department-info">
            <p><strong>Department :</strong> <?= htmlspecialchars($dept_name) ?></p>
            <p><strong>ID Department :</strong> <?= htmlspecialchars($dept) ?></p>
        </div>
        <div class="date-range">
            <?php if ($start != null || $end != null) : ?>
                <p><strong>Dari tanggal:</strong> <?= formatTanggalIndonesia($start); ?> <strong>sampai</strong> <?= formatTanggalIndonesia($end); ?></p>
            <?php else : ?>
                <p>Semua tanggal</p>
            <?php endif; ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Shift</th>
                    <th>Check In</th>
                    <th>Status Masuk</th>
                    <th>Check Out</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($attendance as $date => $attendances) : // Looping berdasarkan tanggal ?>
                    <?php foreach ($attendances as $index => $atd) : ?>
                        <?php
                            // Mendapatkan informasi shift
                            $shift_info = array_filter($shift_data, function ($shift) use ($atd) {
                                return $shift['shift_id'] == $atd['shift_id'];
                            });
                            $shift_info = array_values($shift_info);
                            if (!empty($shift_info)) {
                                $shift = $shift_info[0];
                                // Menggunakan helper function untuk mendapatkan status checkout
                                $checkout_status = get_checkout_status($atd, $shift, $atd['attendance_date']);
                            } else {
                                $checkout_status = 'Shift Tidak Ditemukan';
                            }
                        ?>
                        <tr>
                            <?php if ($index === 0) : ?>
                                <td rowspan="<?= count($attendances); ?>"><?= $i++; ?></td>
                                <td rowspan="<?= count($attendances); ?>"><?= formatTanggalIndonesia($date); ?></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($atd['employee_name']); ?></td>
                            <td>
                                <?php
                                    if (!empty($shift_info)) {
                                        echo htmlspecialchars($shift['shift_id']) . " = " . date('H:i', strtotime($shift['start_time'])) . " - " . date('H:i', strtotime($shift['end_time']));
                                    } else {
                                        echo "Shift Tidak Ditemukan";
                                    }
                                ?>
                            </td>
                            <td><?= $atd['in_time'] ? date('H:i:s', strtotime($atd['in_time'])) : 'Belum check in'; ?></td>
                            <td><?= htmlspecialchars($atd['in_status']); ?></td>
                            <td><?= htmlspecialchars($checkout_status); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
