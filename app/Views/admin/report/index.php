<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="row">
        <div class="col-lg">
            <h3 class="mb-4 text-gray-900"><?= $title; ?></h3>
            <a href="<?= base_url('admin/dashboard'); ?>" class="btn btn-secondary btn-icon-split mb-4">
                <span class="icon text-white">
                    <i class="fas fa-chevron-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7 ml-auto mb-3 float-right">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-3 ">
                        <input type="date" name="start" class="form-control" value="<?= isset($_GET['start']) ? $_GET['start'] : '' ?>">
                    </div>
                    <div class="col-3">
                        <input type="date" name="end" class="form-control" value="<?= isset($_GET['end']) ? $_GET['end'] : '' ?>">
                    </div>
                    <div class="col-4">
                        <select class="form-control" name="dept">
                            <option disabled <?= !isset($_GET['dept']) ? 'selected' : '' ?>>Department</option>
                            <?php foreach ($department as $d) : ?>
                                <option value="<?= $d['department_id']; ?>" <?= isset($_GET['dept']) && $_GET['dept'] == $d['department_id'] ? 'selected' : '' ?>>
                                    <?= $d['department_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-2 d-flex justify-content-end">
                        <button type="submit" class="btn btn-success btn-fill shadow-sm" style="width: 100px; padding: 5px 10px;">Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End of row show -->
    <?php if (!$attendance) : ?>
        <h5>Tidak Ada Data, <br> Silakan Pilih Tanggal dan Department</h5>
    <?php else : ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Kehadiran</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Nama</th>
                                <th>Shift</th>
                                <th>Check In</th>
                                <th>Status Masuk</th>
                                <th>Check Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($attendance as $atd) :
                                // Mendapatkan informasi shift
                                $shift_info = array_filter($shift_data, function ($shift) use ($atd) {
                                    return $shift['shift_id'] == $atd['shift_id'];
                                });
                                $shift_info = array_values($shift_info);
                                if (!empty($shift_info)) {
                                    $shift = $shift_info[0];
                                    $checkout_status = get_checkout_status($atd, $shift, $atd['attendance_date']);
                                } else {
                                    $checkout_status = 'Shift Tidak Ditemukan';
                                }
                            ?>
                                <tr>
                                    <th><?= $i++ ?></th>
                                    <td><?= date('d-m-Y', strtotime($atd['attendance_date'])) ?></td>
                                    <td><?= htmlspecialchars($atd['employee_name']) ?></td>
                                    <td class="text-center">
                                        <?php
                                        if (!empty($shift_info)) {
                                            echo htmlspecialchars($shift['shift_id']) . " = " . date('H:i', strtotime($shift['start_time'])) . " - " . date('H:i', strtotime($shift['end_time']));
                                        } else {
                                            echo "Shift Tidak Ditemukan";
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center"><?= htmlspecialchars($atd['in_time']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($atd['in_status']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($checkout_status) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- Tombol Cetak PDF dan Excel tetap di sini -->
                            <a href="<?= base_url('admin/report/print_attendance_all/pdf/') . $start . '/' . $end . '/' . $dept ?>" target="_blank" class="btn btn-danger btn-sm ml-2 shadow-sm d-sm-inline-block float-right mb-2">
                                <i class="fas fa-file-pdf"></i> Cetak PDF
                            </a>
                            <a href="<?= base_url('admin/report/print_attendance_all/excel/') . $start . '/' . $end . '/' . $dept ?>" class="btn btn-success btn-sm ml-2 shadow-sm d-sm-inline-block float-right mb-2">
                                <i class="fas fa-file-excel"></i> Cetak Excel
                            </a>
                        </tbody>

                    </table>
                </div>
                <div class="mt-4">
                    <h5>Keterangan Kolom Status Masuk:</h5>
                    <ul>
                        <li><strong>Tepat Waktu</strong>: Pegawai melakukan presensi dalam rentang waktu 5 menit setelah waktu mulai shift.</li>
                        <li><strong>Terlambat</strong>: Pegawai melakukan presensi setelah 5 menit dari waktu mulai shift.</li>
                    </ul>
                </div>
                <div class="mt-4">
                    <h5>Keterangan Kolom Check Out:</h5>
                    <ul>
                        <li><strong>Shift Belum Selesai</strong>: Pegawai belum dapat melakukan presensi keluar karena shift masih berlangsung.</li>
                        <li><strong>Belum Presensi Keluar</strong>: Shift sudah selesai, namun pegawai belum melakukan presensi keluar.</li>
                        <li><strong>waktu check out</strong>: Pegawai telah melakukan presensi keluar sesuai jadwal shift.</li>
                        <li><strong>-</strong>: Shift telah selesai pada hari sebelumnya dan pegawai belum melakukan presensi keluar.</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->