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
    12 => 'Desember',
];

$statusMap = [
    1 => ['class' => 'badge-success', 'text' => 'Hadir'],
    0 => ['class' => 'badge-danger', 'text' => 'Tidak Hadir'],
    2 => ['class' => 'badge-warning', 'text' => 'Izin'],
    3 => ['class' => 'badge-warning', 'text' => 'Sakit'],
    4 => ['class' => 'badge-dark', 'text' => 'Cuti'],
    5 => ['class' => 'badge-primary', 'text' => 'Libur'],
];
?>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><?= esc($title); ?></h1>
        <a href="<?= base_url('admin/master/employee/detail/' . esc($employee['employee_id'])); ?>" class="btn btn-md btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-md text-white-50"></i> Kembali ke Detail
        </a>
    </div>
    
    <?php if (session()->getFlashdata('message')) : ?>
        <div class="alert alert-success" role="alert" id="flashdataMessage">
            <button class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?= session()->getFlashdata('message'); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pegawai</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-4 col-md-3"><strong>Nama</strong></div>
                        <div class="col-8 col-md-9">: <?= esc($employee['employee_name']); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-4 col-md-3"><strong>Departemen</strong></div>
                        <div class="col-8 col-md-9">: <?= esc($department_current['department_name']) . ' ' . esc($employee['rptra_name']); ?></div>
                    </div>
                </div>
            </div>

            <hr>

            <form method="get" class="mt-3">
                <div class="form-row align-items-end">
                    <div class="col-md-3 col-6 mb-2">
                        <label for="month">Bulan</label>
                        <select name="month" id="month" class="form-control">
                            <?php for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?= $m; ?>" <?= ($m == $month) ? 'selected' : ''; ?>>
                                    <?= $bulanIndonesia[$m]; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <label for="year">Tahun</label>
                        <select name="year" id="year" class="form-control">
                            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--) : ?>
                                <option value="<?= $y; ?>" <?= ($y == $year) ? 'selected' : ''; ?>><?= $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Kalender Presensi - <?= $bulanIndonesia[(int)$month] ?> <?= esc($year) ?>
            </h6>
            <div>
                <a href="<?= base_url('admin/report/print_attendance_employee/pdf/' . esc($employee['employee_id']) . '?month=' . esc($month) . '&year=' . esc($year)); ?>" class="btn btn-sm btn-danger shadow-sm" target="_blank">
                    <i class="fas fa-file-pdf fa-sm text-white-50"></i> Cetak PDF
                </a>
                <a href="<?= base_url('admin/report/print_attendance_employee/excel/' . esc($employee['employee_id']) . '?month=' . esc($month) . '&year=' . esc($year)); ?>" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-file-excel fa-sm text-white-50"></i> Cetak Excel
                </a>
            </div>
        </div>
        <div class="card-body">
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
            <hr>
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="attendance-calendar">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 14.28%;">Minggu</th>
                            <th style="width: 14.28%;">Senin</th>
                            <th style="width: 14.28%;">Selasa</th>
                            <th style="width: 14.28%;">Rabu</th>
                            <th style="width: 14.28%;">Kamis</th>
                            <th style="width: 14.28%;">Jumat</th>
                            <th style="width: 14.28%;">Sabtu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $firstDayOfMonth = date('w', strtotime("$year-$month-01"));
                        $todayDate = date('Y-m-d');
                        $dayCounter = 1;
                        ?>

                        <?php for ($i = 0; $i < 6; $i++) : ?>
                            <tr>
                                <?php for ($j = 0; $j < 7; $j++) : ?>
                                    <?php
                                    if (($i === 0 && $j < $firstDayOfMonth) || $dayCounter > $daysInMonth) {
                                        echo '<td class="bg-light"></td>';
                                    } else {
                                        $currentLoopDateStr = "$year-$month-$dayCounter";
                                        $currentLoopDate = date('Y-m-d', strtotime($currentLoopDateStr));
                                        $isToday = ($currentLoopDate == $todayDate);
                                        $isFuture = ($currentLoopDate > $todayDate);

                                        $td_class = $isToday ? 'bg-primary text-white font-weight-bold' : '';
                                        echo "<td class='day-cell {$td_class}' style='min-height: 120px; vertical-align: top;'>";
                                    ?>
                                        <div class="d-flex justify-content-between align-items-start">
                                            <strong class="h5"><?= $dayCounter ?></strong>
                                            <?php
                                            $status = $attendance[$dayCounter]['presence_status'] ?? null;
                                            if (!$isFuture && !in_array($status, [4, 5])) { // Can't edit leave/holiday
                                                echo "<a href='#' title='Edit Presensi' data-target='#editAttendanceModal' data-toggle='modal' data-day='{$dayCounter}'><i class='fas fa-edit text-info'></i></a>";
                                            }
                                            ?>
                                        </div>

                                        <div class="mt-2">
                                            <?php
                                            if ($isFuture) {
                                                echo "<span class='badge badge-secondary'>Belum Ada Data</span>";
                                            } elseif (isset($attendance[$dayCounter])) {
                                                $attData = $attendance[$dayCounter];
                                                $statusInfo = $statusMap[$attData['presence_status']] ?? null;

                                                if ($statusInfo) {
                                                    echo "<span class='badge {$statusInfo['class']} d-block'>{$statusInfo['text']}</span>";
                                                }

                                                // Location Icons for 'Hadir'
                                                if ($attData['presence_status'] == 1) {
                                                    echo "<div class='mt-2'>";
                                                    if (!empty($attData['check_in_latitude'])) {
                                                        echo "<a href='#' data-target='#mapModal' title='Lokasi Masuk' class='mx-1' onclick=\"showMap({$attData['check_in_latitude']}, {$attData['check_in_longitude']}, '{$employee['employee_name']} - Check In')\">
                                                                <i class='fas fa-map-marker-alt' style='font-size: 1.2rem; color: #0af06d !important;;'></i>
                                                              </a>";
                                                    }
                                                    if (!empty($attData['check_out_latitude'])) {
                                                        echo "<a href='#' data-target='#mapModal' title='Lokasi Keluar' class='mx-1' onclick=\"showMap({$attData['check_out_latitude']}, {$attData['check_out_longitude']}, '{$employee['employee_name']} - Check Out')\">
                                                                <i class='fas fa-map-marker-alt' style='font-size: 1.2rem; color: #f5253a !important;;'></i>
                                                              </a>";
                                                    }
                                                    echo "</div>";
                                                }
                                            } else {
                                                echo "<span class='badge badge-danger d-block'>Tidak Hadir</span>";
                                            }
                                            ?>
                                        </div>
                                    <?php
                                        echo "</td>";
                                        $dayCounter++;
                                    }
                                    ?>
                                <?php endfor; ?>
                            </tr>
                            <?php if ($dayCounter > $daysInMonth) break; ?>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h5>Keterangan Ikon:</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-edit text-info"></i> : Edit Status Presensi</li>
                        <li><i class="fas fa-map-marker-alt text-success"></i> : Lokasi Presensi Masuk</li>
                        <li><i class="fas fa-map-marker-alt text-danger"></i> : Lokasi Presensi Keluar</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Keterangan Status:</h5>
                    <span class="badge badge-success">Hadir</span>
                    <span class="badge badge-danger">Tidak Hadir</span>
                    <span class="badge badge-warning">Izin/Sakit</span>
                    <span class="badge badge-dark">Cuti</span>
                    <span class="badge badge-primary">Libur</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #attendance-calendar .day-cell {
        min-height: 120px;
        vertical-align: top;
        padding: 8px;
    }

    #attendance-calendar a {
        color: inherit;
    }

    #attendance-calendar .bg-primary a,
    #attendance-calendar .bg-primary i {
        color: #fff !important;
    }
</style>