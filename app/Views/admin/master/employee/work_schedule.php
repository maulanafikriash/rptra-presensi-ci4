<div class="container-fluid">
    <h4 class="mb-4 text-gray-800 font-weight-bold"><?= esc($title); ?></h4>
    <div class="row">
        <div class="col-lg-3">
            <a href="<?= base_url('admin/master/employee/detail/' . esc($employee['employee_id'])); ?>" class="btn btn-secondary btn-icon-split mb-4">
                <span class="icon text-white">
                    <i class="fas fa-chevron-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
        <div class="col-lg-5 offset-lg-4" id="flashdataMessage">
            <?= session()->getFlashdata('message'); ?>
        </div>
    </div>

    <!-- Detail Pegawai -->
    <div class="mb-5">
        <div class="row">
            <div class="col-3 text-end font-weight-bold">Nama :</div>
            <div class="col font-weight-bold"><?= esc($employee['employee_name']); ?></div>
        </div>
        <div class="row">
            <div class="col-3 text-end font-weight-bold">Department :</div>
            <div class="col font-weight-bold"><?= esc($department_current['department_name']); ?></div>
        </div>
    </div>
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
    <!-- Filter Bulan dan Tahun -->
    <form action="" method="get" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="month" class="mr-2">Bulan:</label>
            <select name="month" id="month" class="form-control">
                <?php for ($m = 1; $m <= 12; $m++) : ?>
                    <option value="<?= $m; ?>" <?= ($m == $month) ? 'selected' : ''; ?>>
                        <?= $bulanIndonesia[$m]; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group mr-2">
            <label for="year" class="mr-2">Tahun:</label>
            <select name="year" id="year" class="form-control">
                <?php for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++) : ?>
                    <option value="<?= $y; ?>" <?= ($y == $year) ? 'selected' : ''; ?>><?= $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Tombol Export -->
    <div class="d-flex justify-content-end mb-2">
        <a href="<?= base_url('admin/report/print_work_schedule/pdf/' . esc($employee['employee_id']) . '?month=' . esc($month) . '&year=' . esc($year)); ?>" class="btn btn-danger btn-icon-split mr-2" target="_blank">
            <span class="icon text-white">
                <i class="fas fa-file-pdf"></i>
            </span>
            <span class="text">Cetak PDF</span>
        </a>
        <a href="<?= base_url('admin/report/print_work_schedule/excel/' . esc($employee['employee_id']) . '?month=' . esc($month) . '&year=' . esc($year)); ?>" class="btn btn-success btn-icon-split">
            <span class="icon text-white">
                <i class="fas fa-file-excel"></i>
            </span>
            <span class="text">Cetak Excel</span>
        </a>
    </div>

    <!-- Tabel Kalender -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Minggu</th>
                    <th>Senin</th>
                    <th>Selasa</th>
                    <th>Rabu</th>
                    <th>Kamis</th>
                    <th>Jumat</th>
                    <th>Sabtu</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $firstDayOfMonth = date('w', strtotime("$year-$month-01"));
                $currentDate = date('Y-m-d');

                $dayCounter = 0;

                for ($i = 0; $i < 6; $i++) {
                    echo "<tr>";
                    for ($j = 0; $j < 7; $j++) {
                        if ($i === 0 && $j < $firstDayOfMonth) {
                            echo "<td></td>";
                        } elseif (++$dayCounter <= $daysInMonth) {
                            $currentLoopDate = date('Y-m-d', strtotime("$year-$month-$dayCounter"));

                            // Mendapatkan status jadwal kerja
                            if (isset($workSchedules[$dayCounter])) {
                                $status = $workSchedules[$dayCounter]['schedule_status'];
                                $schedule_id = $workSchedules[$dayCounter]['schedule_id'];
                                $shift_id = $workSchedules[$dayCounter]['shift_id'];
                            } else {
                                $status = null; // Tidak ada jadwal
                                $schedule_id = null;
                                $shift_id = null;
                            }

                            // Menentukan teks status dan kelas badge
                            if (is_null($status) && !empty($shift_id)) {
                                // Cari shift berdasarkan shift_id
                                $shiftTime = 'Tidak ada jadwal';
                                foreach ($shifts as $shift) {
                                    if ($shift['shift_id'] == $shift_id) {
                                        // Format waktu shift menjadi 'HH:MM'
                                        $startTime = date('H:i', strtotime($shift['start_time']));
                                        $endTime = date('H:i', strtotime($shift['end_time']));
                                        $shiftTime = esc($startTime) . ' - ' . esc($endTime);
                                        break;
                                    }
                                }
                                $statusText = $shiftTime;
                                $badgeClass = 'badge-success';
                            } elseif ($status == 4) {
                                $statusText = 'Cuti';
                                $badgeClass = 'badge-dark';
                            } elseif ($status == 5) {
                                $statusText = 'Libur';
                                $badgeClass = 'badge-primary';
                            } else {
                                $statusText = 'Tidak Ada Jadwal';
                                $badgeClass = 'badge-secondary';
                            }

                            // Menentukan apakah dapat menambah atau mengedit jadwal
                            // Mengizinkan edit untuk tanggal hari ini dan masa depan
                            $canEdit = $currentLoopDate >= date('Y-m-d');

                            echo "<td>
                                    <strong class='h5'>$dayCounter</strong><br>
                                    <span class='badge $badgeClass'>$statusText</span>
                                    <br>";
                            if ($canEdit) {
                                if ($schedule_id) {
                                    echo "<a href='#' class='btn btn-sm btn-warning mt-2 edit-schedule' data-schedule-id='$schedule_id' data-date='$currentLoopDate' data-status='$status' data-shift-id='$shift_id'>
                                            <i class='fas fa-edit'></i>
                                          </a>";
                                } else {
                                    echo "<a href='#' class='btn btn-sm btn-primary mt-2 add-schedule' data-date='$currentLoopDate' data-employee-id='" . esc($employee['employee_id']) . "' data-department-id='" . esc($employee['department_id']) . "'>
                                            <i class='fas fa-plus'></i>
                                          </a>";
                                }
                            }
                            echo "</td>";
                        } else {
                            echo "<td></td>";
                        }
                    }
                    echo "</tr>";
                    if ($dayCounter >= $daysInMonth) {
                        break;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <h5>Keterangan:</h5>
        <ul class="list-unstyled">
            <li><i class="fas fa-edit text-warning"></i> <strong>Ikon Edit:</strong> Edit Jadwal Kerja</li>
            <li><i class="fas fa-plus text-primary"></i> <strong>Ikon +:</strong> Tambah Jadwal Kerja</li>
            <li><span class="badge badge-success">Shift Kerja</span> Shift Kerja</li>
            <li><span class="badge badge-dark">Cuti</span> Cuti</li>
            <li><span class="badge badge-primary">Libur</span> Libur</li>
            <li><span class="badge badge-secondary">Tidak Ada Jadwal</span> Tidak Ada Jadwal</li>
        </ul>
    </div>
</div>