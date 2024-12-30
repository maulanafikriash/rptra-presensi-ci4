<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Presensi Pegawai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        h3,
        h4 {
            margin: 0;
        }

        /* CSS untuk badge warna */
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }

        .badge-hadir {
            background-color: green;
        }

        .badge-tidak-hadir {
            background-color: red;
        }

        .badge-izin {
            background-color: yellow;
            color: black;
        }

        .badge-sakit {
            background-color: yellow;
            color: black;
        }

        .badge-cuti {
            background-color: black;
        }

        .badge-libur {
            background-color: blue;
        }

        .badge-tidak-ada-data {
            background-color: gray;
        }
    </style>
</head>

<body>
    <h3>Riwayat Presensi Pegawai</h3>
    <h4>Nama : <?= $employee['employee_name']; ?></h4>
    <h4>
        Bulan :
        <?php
        $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        $formatter->setPattern('MMMM yyyy'); // Format: Nama Bulan Tahun
        echo $formatter->format(new DateTime("$year-$month-01"));
        ?>
    </h4>

    <table>
        <thead>
            <tr>
                <th>Hari/Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendance as $date => $status) : ?>
                <tr>
                    <td>
                        <?php
                        $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                        $formatter->setPattern('EEEE, dd-MM-yyyy'); // Format: Hari, tanggal-bulan-tahun
                        echo $formatter->format(new DateTime($date));
                        ?>
                    </td>
                    <td>
                        <?php
                        switch ($status) {
                            case 'Hadir':
                                $badgeClass = 'badge-hadir';
                                break;
                            case 'Tidak Hadir':
                                $badgeClass = 'badge-tidak-hadir';
                                break;
                            case 'Izin':
                                $badgeClass = 'badge-izin';
                                break;
                            case 'Sakit':
                                $badgeClass = 'badge-sakit';
                                break;
                            case 'Cuti':
                                $badgeClass = 'badge-cuti';
                                break;
                            case 'Libur':
                                $badgeClass = 'badge-libur';
                                break;
                            case 'Tidak Ada Data':
                                $badgeClass = 'badge-tidak-ada-data';
                                break;
                            default:
                                $badgeClass = '';
                        }
                        ?>
                        <span class="badge <?= $badgeClass; ?>"><?= $status; ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>