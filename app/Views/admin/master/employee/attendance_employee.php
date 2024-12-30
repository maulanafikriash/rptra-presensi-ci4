<div class="container-fluid">
    <h4 class="mb-4 text-gray-800"><?= esc($title); ?></h4>
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
            <div class="col"><?= esc($employee['employee_name']); ?></div>
        </div>
        <div class="row">
            <div class="col-3 text-end font-weight-bold">Department :</div>
            <div class="col"><?= esc($department_current['department_name']); ?></div>
        </div>
    </div>

    <!-- Filter Bulan dan Tahun -->
    <form action="" method="get" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="month" class="mr-2">Bulan:</label>
            <select name="month" id="month" class="form-control">
                <?php for ($m = 1; $m <= 12; $m++) : ?>
                    <option value="<?= $m; ?>" <?= ($m == $month) ? 'selected' : ''; ?>><?= date('F', mktime(0, 0, 0, $m, 1)); ?></option>
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
        <a href="<?= base_url('admin/report/print_attendance_employee/pdf/' . esc($employee['employee_id']) . '?month=' . esc($month) . '&year=' . esc($year)); ?>" class="btn btn-danger btn-icon-split mr-2" target="_blank">
            <span class="icon text-white">
                <i class="fas fa-file-pdf"></i>
            </span>
            <span class="text">Cetak PDF</span>
        </a>
        <a href="<?= base_url('admin/report/print_attendance_employee/excel/' . esc($employee['employee_id']) . '?month=' . esc($month) . '&year=' . esc($year)); ?>" class="btn btn-success btn-icon-split">
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

                // Membuat array status presensi berdasarkan hari
                $attendanceData = [];
                foreach ($attendance as $att) {
                    if (isset($att['date'])) {
                        $day = (int) date('j', strtotime($att['date']));
                        $attendanceData[$day] = [
                            'presence_status' => $att['presence_status'],
                            'check_in_latitude' => $att['check_in_latitude'],
                            'check_in_longitude' => $att['check_in_longitude'],
                            'check_out_latitude' => $att['check_out_latitude'],
                            'check_out_longitude' => $att['check_out_longitude'],
                        ];
                    }
                }

                $dayCounter = 0;

                for ($i = 0; $i < 6; $i++) {
                    echo "<tr>";
                    for ($j = 0; $j < 7; $j++) {
                        if ($i === 0 && $j < $firstDayOfMonth) {
                            echo "<td></td>";
                        } elseif (++$dayCounter <= $daysInMonth) {
                            $currentLoopDate = date('Y-m-d', strtotime("$year-$month-$dayCounter"));

                            echo "<td><strong class='h5'>$dayCounter</strong><br>";

                            // Tampilkan ikon edit dan lokasi hanya jika tanggal saat ini atau sebelumnya
                            if ($currentLoopDate <= $currentDate) {
                                echo "<a href='#' class='float-right' data-target='#editAttendanceModal' data-toggle='modal' data-day='$dayCounter'><i class='fas fa-edit'></i></a><br>";

                                if (isset($attendanceData[$dayCounter]) && $attendanceData[$dayCounter]['presence_status'] == 1) {

                                    echo "<div class='d-flex justify-content-start align-items-center gap-2'>";

                                    // Tampilkan ikon lokasi presensi masuk jika tersedia
                                    $checkInLocationEmpty = empty($attendanceData[$dayCounter]['check_in_latitude']) || empty($attendanceData[$dayCounter]['check_in_longitude']);
                                    if (!$checkInLocationEmpty) {
                                        echo "<a href='#' data-target='#mapModal' title='Lihat Lokasi Presensi Masuk' class='mx-1' onclick=\"showMap(" . ($attendanceData[$dayCounter]['check_in_latitude'] ?? 'null') . ", " . ($attendanceData[$dayCounter]['check_in_longitude'] ?? 'null') . ", '{$employee['employee_name']} - Check In')\">
                                                <i class='fas fa-map-marker-alt text-success' style='font-size: 1.1rem;'></i>
                                            </a>";
                                    }

                                    // Tampilkan ikon lokasi presensi keluar jika tersedia
                                    $checkOutLocationEmpty = empty($attendanceData[$dayCounter]['check_out_latitude']) || empty($attendanceData[$dayCounter]['check_out_longitude']);
                                    if (!$checkOutLocationEmpty) {
                                        echo "<a href='#' data-target='#mapModal' title='Lihat Lokasi Presensi Keluar' class='mx-1' onclick=\"showMap(" . ($attendanceData[$dayCounter]['check_out_latitude'] ?? 'null') . ", " . ($attendanceData[$dayCounter]['check_out_longitude'] ?? 'null') . ", '{$employee['employee_name']} - Check Out')\">
                                                <i class='fas fa-map-marker-alt text-danger' style='font-size: 1.1rem;'></i>
                                            </a>";
                                    }

                                    echo "</div>";
                                }
                            }

                            if ($currentLoopDate > $currentDate) {
                                echo "<span class='badge badge-secondary'>Tidak Ada Data</span>";
                            } elseif (isset($attendanceData[$dayCounter])) {
                                switch ($attendanceData[$dayCounter]['presence_status']) {
                                    case 1:
                                        echo "<span class='badge badge-success'>Hadir</span>";
                                        break;
                                    case 0:
                                        echo "<span class='badge badge-danger'>Tidak Hadir</span>";
                                        break;
                                    case 2:
                                        echo "<span class='badge badge-warning'>Izin</span>";
                                        break;
                                    case 3:
                                        echo "<span class='badge badge-warning'>Sakit</span>";
                                        break;
                                    case 4:
                                        echo "<span class='badge badge-dark'>Cuti</span>";
                                        break;
                                    case 5:
                                        echo "<span class='badge badge-primary'>Libur</span>";
                                        break;
                                    default:
                                        echo "<span class='badge badge-secondary'>Tidak Ada Data</span>";
                                        break;
                                }
                            } else {
                                echo "<span class='badge badge-danger'>Tidak Hadir</span>";
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
            <li><i class="fas fa-map-marker-alt text-success"></i> <strong>Ikon Lokasi Hijau:</strong> Lokasi Presensi Masuk</li>
            <li><i class="fas fa-map-marker-alt text-danger"></i> <strong>Ikon Lokasi Merah:</strong> Lokasi Presensi Keluar</li>
            <li><i class="fas fa-edit"></i> <strong>Ikon Edit:</strong> Edit Status Presensi</li>
        </ul>
    </div>
</div>