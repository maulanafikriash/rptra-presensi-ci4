<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Card: Riwayat Presensi -->
    <div class="card shadow mb-4">
        <!-- Card Header: Judul dan Filter -->
        <div class="card-header py-3 d-sm-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary mb-2 mb-sm-0"><?= esc($title); ?></h6>
            
            <!-- Form Filter (Responsive) -->
            <form action="" method="get">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label class="sr-only" for="month">Bulan</label>
                        <select name="month" id="month" class="custom-select custom-select-sm">
                            <?php
                            $bulanIndonesia = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                            foreach ($bulanIndonesia as $m => $bulan) {
                                $selected = ($m == $month) ? 'selected' : '';
                                echo "<option value='$m' $selected>$bulan</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="sr-only" for="year">Tahun</label>
                        <select name="year" id="year" class="custom-select custom-select-sm">
                            <?php
                            $currentYear = date('Y');
                            for ($y = $currentYear - 5; $y <= $currentYear; $y++) {
                                $selected = ($y == $year) ? 'selected' : '';
                                echo "<option value='$y' $selected>$y</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter fa-sm"></i> Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <!-- Summary Boxes (2 kolom di mobile) -->
            <div class="row mb-4">
                <div class="col-6 col-lg-3 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['hadir'] ?></div>
                                </div>
                                <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Izin/Sakit</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['izin_sakit'] ?></div>
                                </div>
                                <div class="col-auto"><i class="fas fa-exclamation-circle fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpha</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['alpha'] ?></div>
                                </div>
                                <div class="col-auto"><i class="fas fa-times-circle fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Libur/Cuti</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['libur_cuti'] ?></div>
                                </div>
                                <div class="col-auto"><i class="fas fa-calendar-alt fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Presensi -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Hari</th>
                            <th>Tanggal</th>
                            <th>Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $today = date('Y-m-d');
                        $hariIndonesia = [
                            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                        ];

                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            $date = date('Y-m-d', strtotime("$year-$month-$day"));
                            $dayName = date('l', strtotime($date));
                            $dayNameIndo = $hariIndonesia[$dayName] ?? $dayName;
                            
                            $rowClass = ($dayName == 'Saturday' || $dayName == 'Sunday') ? 'bg-light' : '';
                            $status = 'Tidak Hadir';
                            $statusClass = 'danger';

                            if ($date > $today) {
                                $status = '-';
                                $statusClass = 'light text-dark';
                            } elseif (isset($attendance[$date])) {
                                switch ($attendance[$date]) {
                                    case 1: $status = 'Hadir'; $statusClass = 'success'; break;
                                    case 2: $status = 'Izin'; $statusClass = 'warning'; break;
                                    case 3: $status = 'Sakit'; $statusClass = 'warning'; break;
                                    case 4: $status = 'Cuti'; $statusClass = 'dark'; break;
                                    case 5: $status = 'Libur'; $statusClass = 'primary'; break;
                                }
                            }
                        ?>
                            <tr class="<?= $rowClass ?>">
                                <td><?= esc($dayNameIndo); ?></td>
                                <td><?= esc(date('d-m-Y', strtotime($date))); ?></td>
                                <td><span class="badge badge-<?= esc($statusClass); ?> p-2"><?= esc($status); ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<style>
@media screen and (max-width: 425px) {
    #dataTable th, #dataTable td {
        font-size: 0.8rem;
        padding: 0.4rem; 
    }
    .badge {
        font-size: 0.75rem;
    }
}
</style>