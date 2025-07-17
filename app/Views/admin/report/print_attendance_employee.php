<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Kehadiran - <?= htmlspecialchars($employee['employee_name']) ?>- RPTRA <?= htmlspecialchars(session()->get('rptra_name')) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .info-section {
            margin-bottom: 15px;
        }

        h3,
        h4 {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #e9ecef;
        }

        td.text-left {
            text-align: left;
        }
    </style>
</head>

<body>
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
        // Format: Selasa, 11 Juni 2025
        $fmt->setPattern('EEEE, dd MMMM yyyy');
        return $fmt->format(new DateTime($tanggal));
    }

    // Map untuk menerjemahkan status numerik ke teks
    $statusMap = [
        1 => 'Hadir',
        0 => 'Tidak Hadir',
        2 => 'Izin',
        3 => 'Sakit',
        4 => 'Cuti',
        5 => 'Libur',
        null => 'Tidak Ada Data'
    ];
    ?>

    <div class="header-section">
        <h3>DAFTAR HADIR <?= strtoupper(htmlspecialchars($dept_name)) ?> <?= strtoupper(htmlspecialchars(session()->get('rptra_name'))) ?></h3>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td class="text-left" style="width: 20%;"><strong>Nama</strong></td>
                <td class="text-left"> <?= htmlspecialchars($employee['employee_name']); ?></td>
            </tr>
            <tr>
                <td class="text-left"><strong>Periode</strong></td>
                <td class="text-left">
                    <?php
                    $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                    $formatter->setPattern('MMMM yyyy');
                    echo $formatter->format(new DateTime("$year-$month-01"));
                    ?>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Tanggal</th>
                <th>Shift</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status Masuk</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($attendance as $att) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td class="text-left"><?= formatTanggalIndonesia($att['attendance_date']); ?></td>

                    <?php if ($att['presence_status'] == 1) : // Kondisi jika status 'Hadir' 
                    ?>

                        <td class="text-center">
                            <?php
                            if (!empty($att['shift_id']) && !empty($att['shift_start']) && !empty($att['shift_end'])) {
                                echo htmlspecialchars($att['shift_id']) . " (" . date('H:i', strtotime($att['shift_start'])) . " - " . date('H:i', strtotime($att['shift_end'])) . ")";
                            } else {
                                echo "Shift Tidak Ditemukan";
                            }
                            ?>
                        </td>
                        <td><?= $att['in_time'] ? date('H:i:s', strtotime($att['in_time'])) : '-'; ?></td> 
                        <td>
                            <?php
                            if (function_exists('get_checkout_status')) {
                                echo htmlspecialchars(get_checkout_status($att, [
                                    'start_time' => $att['shift_start'],
                                    'end_time' => $att['shift_end']
                                ], $att['attendance_date']));
                            } elseif (!empty($att['out_time'])) {
                                echo date('H:i:s', strtotime($att['out_time']));
                            } else {
                                echo 'Belum check out';
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($att['in_status'] ?? '-'); ?></td>

                    <?php else : // Kondisi jika status BUKAN 'Hadir' (Izin, Sakit, Libur, dll) 
                    ?>
                        <td colspan="4">
                            <p><?= $statusMap[$att['presence_status']] ?? 'Error'; ?></p>
                        </td>

                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>