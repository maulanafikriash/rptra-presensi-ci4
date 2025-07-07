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
?>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><?= esc($title); ?></h1>
        <a href="<?= base_url('admin/master/employee/detail/' . esc($employee['employee_id'])); ?>" class="btn btn-md btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-md text-white-50"></i> Kembali ke Detail
        </a>
    </div>

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
                            <?php foreach ($bulanIndonesia as $m => $nama): ?>
                                <option value="<?= $m ?>" <?= ($m == $month) ? 'selected' : '' ?>><?= $nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <label for="year">Tahun</label>
                        <select name="year" id="year" class="form-control">
                            <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>><?= $y ?></option>
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

    <?php if (session()->getFlashdata('message')) : ?>
        <div class="alert alert-success" role="alert" id="flashdataMessage">
            <button class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?= session()->getFlashdata('message'); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Kalender Jadwal Kerja - <?= esc($bulanIndonesia[$month] . ' ' . $year) ?>
            </h6>
            <div>
                <a href="<?= base_url('admin/report/print_work_schedule/pdf/' . esc($employee['employee_id']) . '?month=' . esc($month) . '&year=' . esc($year)); ?>" class="btn btn-sm btn-danger shadow-sm" target="_blank">
                    <i class="fas fa-file-pdf fa-sm text-white-50"></i> Cetak PDF
                </a>
                <a href="<?= base_url('admin/report/print_work_schedule/excel/' . esc($employee['employee_id']) . '?month=' . esc($month) . '&year=' . esc($year)); ?>" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-file-excel fa-sm text-white-50"></i> Cetak Excel
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered calendar-table">
                    <thead class="thead-light">
                        <tr class="text-center">
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
                        $daysInMonth     = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $firstDayOfMonth = date('w', strtotime("$year-$month-01"));
                        $dayCounter      = 1;
                        ?>
                        <?php for ($i = 0; $i < 6; $i++) : ?>
                            <tr>
                                <?php for ($j = 0; $j < 7; $j++) : ?>
                                    <?php if (($i === 0 && $j < $firstDayOfMonth) || $dayCounter > $daysInMonth) : ?>
                                        <td class="bg-light"></td>
                                    <?php else :
                                        $currentLoopDate = date('Y-m-d', strtotime("$year-$month-$dayCounter"));
                                        $isToday         = ($currentLoopDate == date('Y-m-d'));
                                        $canEdit         = $currentLoopDate >= date('Y-m-d');
                                        $schedule        = $workSchedules[$dayCounter] ?? null;

                                        $statusText = 'Tidak Ada Jadwal';
                                        $badgeClass = 'badge-secondary';
                                        if ($schedule) {
                                            $status = $schedule['schedule_status'];
                                            if ($status == '4') {
                                                $statusText = 'Cuti';
                                                $badgeClass = 'badge-dark text-white';
                                            } elseif ($status == '5') {
                                                $statusText = 'Libur';
                                                $badgeClass = 'badge-primary';
                                            } elseif ($schedule['shift_info']) {
                                                $shift = $schedule['shift_info'];
                                                $statusText = date('H:i', strtotime($shift['start_time'])) . ' - ' . date('H:i', strtotime($shift['end_time']));
                                                $badgeClass = 'badge-success';
                                            } else {
                                                $statusText = 'Shift Dihapus';
                                                $badgeClass = 'badge-warning';
                                            }
                                        }
                                    ?>
                                        <td class="day-cell <?= $isToday ? 'bg-primary text-white' : '' ?>" style="height: 120px; vertical-align: top;">
                                            <div class="d-flex flex-column justify-content-between h-100 p-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <strong class="h5"><?= $dayCounter ?></strong>
                                                    <?php if ($canEdit) : ?>
                                                        <?php
                                                        $editButtonClass = $isToday ? 'btn-outline-light' : 'btn-outline-warning';
                                                        $addButtonClass  = $isToday ? 'btn-outline-light' : 'btn-outline-primary';
                                                        ?>
                                                        <?php if ($schedule) : ?>
                                                            <a href="#" class="btn btn-sm <?= $editButtonClass ?> edit-schedule" title="Edit Jadwal"
                                                                data-schedule-id="<?= esc($schedule['schedule_id']) ?>"
                                                                data-date="<?= esc($currentLoopDate) ?>"
                                                                data-status="<?= esc($schedule['schedule_status']) ?>"
                                                                data-shift-id="<?= esc($schedule['shift_id']) ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        <?php else : ?>
                                                            <a href="#" class="btn btn-sm <?= $addButtonClass ?> add-schedule" title="Tambah Jadwal"
                                                                data-date="<?= esc($currentLoopDate) ?>"
                                                                data-employee-id="<?= esc($employee['employee_id']) ?>">
                                                                <i class="fas fa-plus"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-center">
                                                    <span class="badge <?= $badgeClass ?> d-block p-1"><?= esc($statusText) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                    <?php $dayCounter++;
                                    endif; ?>
                                <?php endfor; ?>
                            </tr>
                            <?php if ($dayCounter > $daysInMonth) break; ?>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Keterangan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-2"><span class="badge badge-success p-2 mr-2">&nbsp;</span> Jadwal Kerja Aktif</div>
                <div class="col-md-6 col-lg-3 mb-2"><span class="badge badge-primary p-2 mr-2">&nbsp;</span> Hari Libur</div>
                <div class="col-md-6 col-lg-3 mb-2"><span class="badge badge-dark p-2 mr-2">&nbsp;</span> Cuti Pegawai</div>
                <div class="col-md-6 col-lg-3 mb-2"><span class="badge badge-secondary p-2 mr-2">&nbsp;</span> Belum Ada Jadwal</div>
            </div>
        </div>
    </div>
</div>