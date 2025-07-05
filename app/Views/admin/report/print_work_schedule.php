<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Jadwal Kerja <?= esc($employee_name); ?> - <?= esc($month_name); ?> <?= esc($year); ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 10px;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .info-section {
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 80px;
            font-weight: bold;
        }

        /* Tabel Jadwal Kerja */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid #666;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        .schedule-table thead th {
            background-color: #e9ecef;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }

        .status-cuti {
            background-color: #494d4a;
            color: #fff;
            font-weight: bold;
        }

        .status-libur {
            background-color: #dc3545;
            color: #fff;
            font-weight: bold;
        }

        .hari-libur {
            background-color: #f8f9fa;
        }

        /* CSS Khusus untuk Cetak PDF agar tidak terpotong */
        @media print {
            body {
                font-size: 9pt;
                /* Ukuran font spesifik untuk print */
            }

            .schedule-table {
                page-break-inside: auto;
            }

            .schedule-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .schedule-table thead {
                display: table-header-group;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Jadwal Kerja <?= esc($department_name); ?></h2>
        </div>

        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td>NAMA</td>
                    <td>: <?= esc($employee_name); ?></td>
                </tr>
                <tr>
                    <td>PERIODE</td>
                    <td>: <?= strtoupper(esc($month_name)); ?> <?= esc($year); ?></td>
                </tr>
            </table>
        </div>

        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Hari</th>
                    <th style="width: 35%;">Tanggal</th>
                    <th style="width: 40%;">Shift Kerja</th>
                </tr>
            </thead>
            <tbody>
                <?php
                function getIndonesianDayName($date)
                {
                    $indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $dayIndex = date('w', strtotime($date));
                    return $indonesianDays[$dayIndex];
                }

                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month_number, $year);

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $currentDate = sprintf('%04d-%02d-%02d', $year, $month_number, $day);
                    $dayName = getIndonesianDayName($currentDate);
                    $formattedDate = date('d F Y', strtotime($currentDate));

                    $shiftCellContent = 'Tidak Ada Jadwal';
                    $rowClass = '';
                    $cellClass = '';

                    $dayOfWeek = date('w', strtotime($currentDate));
                    if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                        $rowClass = 'hari-libur';
                    }

                    if (isset($workSchedules[$day]) && $workSchedules[$day] !== null) {
                        $schedule = $workSchedules[$day];
                        switch ($schedule['schedule_status']) {
                            case 4:
                                $shiftCellContent = 'CUTI';
                                $cellClass = 'status-cuti';
                                break;
                            case 5:
                                $shiftCellContent = 'LIBUR';
                                $cellClass = 'status-libur';
                                break;
                            default:
                                if (!empty($schedule['start_time']) && !empty($schedule['end_time'])) {
                                    $startTime = date('H:i', strtotime($schedule['start_time']));
                                    $endTime = date('H:i', strtotime($schedule['end_time']));
                                    $shiftCellContent = esc($startTime) . ' - ' . esc($endTime);

                                    if ($startTime == '05:00' && $endTime == '22:00') {
                                        $shiftCellContent .= ' (tugas luar)';
                                    }
                                }
                                break;
                        }
                    }

                    echo "<tr class='" . esc($rowClass, 'attr') . "'>
                            <td>" . esc($dayName) . "</td>
                            <td>" . esc($formattedDate) . "</td>
                            <td class='" . esc($cellClass, 'attr') . "'>" . $shiftCellContent . "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>