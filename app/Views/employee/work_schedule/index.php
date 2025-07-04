<div class="container-fluid">


    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?= esc($title); ?></h6>
        </div>
        <div class="card-body">
            <form action="" method="get" class="row g-3 align-items-end mb-4">
                <div class="col-md-5 col-12 mb-2">
                    <label for="month" class="form-label">Bulan</label>
                    <select name="month" id="month" class="form-control">
                        <?php
                        $bulanIndonesia = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        foreach ($bulanIndonesia as $m => $namaBulan) {
                            $selected = ($m == $month) ? 'selected' : '';
                            echo "<option value='{$m}' {$selected}>{$namaBulan}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-5 col-12 mb-2">
                    <label for="year" class="form-label">Tahun</label>
                    <select name="year" id="year" class="form-control">
                        <?php
                        $currentYear = date('Y');
                        for ($y = $currentYear - 5; $y <= $currentYear + 5; $y++) {
                            $selected = ($y == $year) ? 'selected' : '';
                            echo "<option value='{$y}' {$selected}>{$y}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 col-12">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter fa-sm"></i> Filter
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">Hari</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Shift Kerja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $hariIndonesia = [
                            'Sunday'    => 'Minggu', 'Monday'    => 'Senin', 'Tuesday'   => 'Selasa',
                            'Wednesday' => 'Rabu',   'Thursday'  => 'Kamis', 'Friday'    => 'Jumat',
                            'Saturday'  => 'Sabtu'
                        ];

                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            $date = date('Y-m-d', strtotime("$year-$month-$day"));
                            $dayNameEng = date('l', strtotime($date));
                            $dayNameIndo = $hariIndonesia[$dayNameEng] ?? $dayNameEng;

                            $isWeekend = ($dayNameEng === 'Saturday' || $dayNameEng === 'Sunday');
                            $trClass = $isWeekend ? 'bg-light text-muted' : '';

                            if (isset($schedule[$date])) {
                                $statusKerja = $schedule[$date]['status_kerja'];
                                $shiftClass = $schedule[$date]['shift_class'];
                            } else {
                                $statusKerja = 'Tidak Ada Jadwal';
                                $shiftClass = 'secondary';
                            }
                        ?>
                            <tr class="<?= $trClass; ?>">
                                <td class="mobile-font-small">
                                    <?= esc($dayNameIndo); ?>
                                </td>
                                <td class="text-center mobile-font-small">
                                    <?= esc(date('d-m-Y', strtotime($date))); ?>
                                </td>
                                <td class="text-center mobile-font-small">
                                    <span class="badge badge-<?= esc($shiftClass); ?> p-2">
                                        <?= esc($statusKerja); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>
    @media (max-width: 767px) {
        .mobile-font-small {
            font-size: 0.8rem;
            white-space: nowrap;
            vertical-align: middle;
        }
        
        .mobile-font-small .badge {
            padding: 0.3em 0.6em;
            font-size: 0.75rem;
        }
    }
</style>