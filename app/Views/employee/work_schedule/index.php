<div class="container-fluid">
    <h3 class="mb-4 text-gray-700 font-weight-bold"><?= esc($title); ?></h3>

    <!-- Form Filter Bulan dan Tahun -->
    <form action="" method="get" class="mb-3">
        <select name="month" class="form-control d-inline w-auto">
            <?php
            $bulanIndonesia = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            foreach ($bulanIndonesia as $m => $bulan) {
                $selected = ($m == $month) ? 'selected' : '';
                echo "<option value='$m' $selected>$bulan</option>";
            }
            ?>
        </select>

        <select name="year" class="form-control d-inline w-auto ml-2">
            <?php
            $currentYear = date('Y');
            for ($y = $currentYear - 5; $y <= $currentYear + 5; $y++) {
                $selected = ($y == $year) ? 'selected' : '';
                echo "<option value='$y' $selected>$y</option>";
            }
            ?>
        </select>

        <button type="submit" class="btn btn-primary ml-2">Filter</button>
    </form>

    <!-- Tabel Jadwal Kerja -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Tanggal</th>
                    <th>Shift Kerja</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = date('Y-m-d', strtotime("$year-$month-$day"));
                    $dayName = date('l', strtotime($date));

                    $hariIndonesia = [
                        'Sunday' => 'Minggu',
                        'Monday' => 'Senin',
                        'Tuesday' => 'Selasa',
                        'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis',
                        'Friday' => 'Jumat',
                        'Saturday' => 'Sabtu',
                    ];
                    $dayName = $hariIndonesia[$dayName] ?? $dayName;

                    if (isset($schedule[$date])) {
                        $statusKerja = $schedule[$date]['status_kerja'];
                        $shiftClass = $schedule[$date]['shift_class'];
                    } else {
                        $statusKerja = 'Tidak Ada Jadwal';
                        $shiftClass = 'secondary';
                    }
                ?>
                    <tr>
                        <td><?= esc($dayName); ?></td>
                        <td><?= esc(date('d-m-Y', strtotime($date))); ?></td>
                        <td><span class="badge badge-<?= esc($shiftClass); ?>"><?= esc($statusKerja); ?></span></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>