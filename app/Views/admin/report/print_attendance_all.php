<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran Seluruh <?= htmlspecialchars($dept_name) ?>
        <?= htmlspecialchars($rptra_name) ?></title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
        }

        .container {
            margin: 0;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-section h3 {
            margin: 0;
            font-size: 14px;
        }

        .header-section p {
            margin: 5px 0;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .italic {
            font-style: italic;
        }

        .text-muted {
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        function formatTanggalID($tanggal)
        {
            return date('d-m-Y', strtotime($tanggal));
        }
        $isSingleDay = ($start === $end);
        $rptraName = isset($rptra_name) ? htmlspecialchars($rptra_name) : htmlspecialchars(session()->get('rptra_name'));
        ?>

        <div class="header-section">
            <h3>LAPORAN KEHADIRAN SELURUH <?= strtoupper(htmlspecialchars($dept_name)) ?> <?= strtoupper($rptraName) ?></h3>
            <?php if ($isSingleDay): ?>
                <p><strong>Tanggal:</strong> <?= formatTanggalID($start); ?></p>
            <?php else: ?>
                <p><strong>Periode:</strong> <?= formatTanggalID($start); ?> s/d <?= formatTanggalID($end); ?></p>
            <?php endif; ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th>Nama</th>
                    <th>Shift</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status Masuk</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php if (empty($attendance)): ?>
                    <tr>
                        <td colspan="7">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                <?php else: ?>
                    <?php
                    $all_attendances = [];
                    foreach ($attendance as $date_group) {
                        $all_attendances = array_merge($all_attendances, $date_group);
                    }
                    $currentDate = null;
                    ?>
                    <?php foreach ($all_attendances as $atd): ?>
                        <?php
                        // jika tanggal saat ini berbeda dengan tanggal sebelumnya
                        $rowStyle = '';
                        if ($atd['attendance_date'] !== $currentDate) {
                            $rowStyle = 'background-color: #f2f2f2;';
                            $currentDate = $atd['attendance_date'];
                        }
                        ?>
                        <tr style="<?= $rowStyle; ?>">
                            <td><?= $i++; ?></td>
                            <td><?= formatTanggalID($atd['attendance_date']); ?></td>
                            <?php
                            $status = $atd['presence_status'];
                            switch ($status) {
                                case '1': // Hadir
                            ?>
                                    <td class="text-left"><?= htmlspecialchars($atd['employee_name']); ?></td>
                                    <td><?= (!empty($atd['shift_id'])) ? htmlspecialchars($atd['shift_id']) . " (" . substr($atd['shift_start'], 0, 5) . "-" . substr($atd['shift_end'], 0, 5) . ")" : 'N/A'; ?></td>
                                    <td><?= $atd['in_time'] ? substr($atd['in_time'], 0, 5) : '-'; ?></td>
                                    <td><?= $atd['out_time'] ? substr($atd['out_time'], 0, 5) : '-'; ?></td>
                                    <td><?= htmlspecialchars($atd['in_status'] ?? '-'); ?></td>
                                <?php
                                    break;

                                case '2': // Izin
                                case '3': // Sakit
                                case '4': // Cuti
                                case '5': // Libur
                                case '0': // Tidak Hadir
                                ?>
                                    <td class="text-left"><?= htmlspecialchars($atd['employee_name']); ?></td>
                                    <td colspan="4" class="italic"><?= htmlspecialchars($atd['presence_status_text']); ?></td>
                                <?php
                                    break;

                                default: // Tidak Ada Data
                                ?>
                                    <td colspan="5" class="italic text-muted"><?= htmlspecialchars($atd['presence_status_text']); ?></td>
                            <?php
                                    break;
                            }
                            ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>