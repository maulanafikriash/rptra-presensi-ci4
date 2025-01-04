<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Jadwal Kerja PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .info {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .info div {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            word-wrap: break-word;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Jadwal Kerja <?= esc($department_name); ?></h2>
    </div>

    <div class="info">
        <h4><strong>Nama :</strong> <?= esc($employee_name); ?></h4>
        <h4><strong>Bulan :</strong> <?= esc($month_name); ?> <?= esc($year); ?></h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>Hari</th>
                <th>Tanggal</th>
                <th>Shift Kerja</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Mendefinisikan hari dalam Bahasa Indonesia
            function getIndonesianDayName($date)
            {
                $indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $dayIndex = date('w', strtotime($date));
                return $indonesianDays[$dayIndex];
            }

            // Mendapatkan jumlah hari dalam bulan menggunakan month_number
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month_number, $year);

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = sprintf('%04d-%02d-%02d', $year, $month_number, $day);
                $dayName = getIndonesianDayName($currentDate);
                $formattedDate = date('d-m-Y', strtotime($currentDate));

                if (isset($workSchedules[$day]) && $workSchedules[$day] !== null) {
                    $schedule = $workSchedules[$day];
                    switch ($schedule['schedule_status']) {
                        case 4:
                            $shiftTime = 'Cuti';
                            break;
                        case 5:
                            $shiftTime = 'Libur';
                            break;
                        default:
                            if (!empty($schedule['start_time']) && !empty($schedule['end_time'])) {
                                // Format waktu menjadi HH:MM
                                $formattedStartTime = date('H:i', strtotime($schedule['start_time']));
                                $formattedEndTime = date('H:i', strtotime($schedule['end_time']));
                                $shiftTime = esc($formattedStartTime) . ' - ' . esc($formattedEndTime);
                            } else {
                                $shiftTime = 'Tidak ada jadwal';
                            }
                            break;
                    }
                } else {
                    $shiftTime = 'Tidak ada jadwal';
                }

                echo "<tr>
                        <td>" . esc($dayName) . "</td>
                        <td>" . esc($formattedDate) . "</td>
                        <td>" . esc($shiftTime) . "</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</body>

</html>