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
        <div class="col-lg-8 ml-auto mb-3 float-right">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-2">
                        <input type="date" name="start" class="form-control" value="<?= htmlspecialchars($start ?? '', ENT_QUOTES) ?>">
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2">
                        <input type="date" name="end" class="form-control" value="<?= htmlspecialchars($end ?? '', ENT_QUOTES) ?>">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-2">
                        <select class="form-control" name="dept">
                            <option value="" disabled <?= empty($dept) ? 'selected' : '' ?>>Pilih Departemen</option>
                            <?php foreach ($department as $d) : ?>
                                <option value="<?= $d['department_id']; ?>" <?= ($dept ?? '') == $d['department_id'] ? 'selected' : '' ?>>
                                    <?= $d['department_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-2 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-fill shadow-sm w-100">Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hasil Laporan -->
    <?php
    $selectedDeptName = '';
    if (!empty($dept)) {
        foreach ($department as $d) {
            if ($d['department_id'] == $dept) {
                $selectedDeptName = $d['department_name'];
                break;
            }
        }
    }

    if (strtoupper($selectedDeptName) === 'ADM' || $selectedDeptName === 'Administrator') :
    ?>
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <h5 class="text-info mt-3">Informasi</h5>
                <p class="mb-3">Administrator tidak melakukan presensi kehadiran.</p>
            </div>
        </div>

    <?php elseif (isset($start) && $start != '' && isset($end) && $end != '' && empty($dept)) : ?>
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5 class="text-center text-danger">Data kehadiran tidak ditemukan.</h5>
                <p class="text-center">Pastikan departemen sudah dipilih.</p>
            </div>
        </div>

    <?php elseif (empty($attendance) && (isset($start) && isset($end))) : ?>
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5 class="text-center text-danger">Data kehadiran tidak ditemukan.</h5>
                <p class="text-center">Pastikan rentang tanggal dan departemen yang dipilih sudah benar.</p>
            </div>
        </div>
    <?php elseif (!empty($attendance)) : ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Kehadiran</h6>
                <div>
                    <a href="<?= base_url('admin/report/print_attendance_all/pdf/') . $start . '/' . $end . '/' . $dept ?>" target="_blank" class="btn btn-danger btn-sm shadow-sm">
                        <i class="fas fa-file-pdf fa-sm"></i> Cetak PDF
                    </a>
                    <a href="<?= base_url('admin/report/print_attendance_all/excel/') . $start . '/' . $end . '/' . $dept ?>" class="btn btn-success btn-sm shadow-sm">
                        <i class="fas fa-file-excel fa-sm"></i> Cetak Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th>Nama</th>
                                <th>Shift</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status Masuk</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php
                            $i = 1;
                            $currentDate = null;
                            ?>
                            <?php foreach ($attendance as $atd) : ?>
                                <?php
                                // Cek jika tanggal saat ini berbeda dengan tanggal sebelumnya
                                $rowClass = '';
                                if ($atd['attendance_date'] !== $currentDate) {
                                    $rowClass = 'bg-light';
                                    $currentDate = $atd['attendance_date']; // Update tanggal saat ini
                                }
                                ?>
                                <tr class="<?= $rowClass; ?>">
                                    <td><?= $i++; ?></td>
                                    <td><?= date('d-m-Y', strtotime($atd['attendance_date'])); ?></td>

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
                                            <td colspan="4" class="align-middle font-italic">
                                                <?= htmlspecialchars($atd['presence_status_text']); ?>
                                            </td>
                                        <?php
                                            break;

                                        default: // Tidak Ada Data
                                        ?>
                                            <td colspan="5" class="align-middle font-italic text-muted">
                                                <?= htmlspecialchars($atd['presence_status_text']); ?>
                                            </td>
                                    <?php
                                            break;
                                    }
                                    ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5 class="text-center">Tidak Ada Data</h5>
                <p class="text-center text-muted">Silakan pilih rentang tanggal dan departemen untuk menampilkan laporan.</p>
            </div>
        </div>
    <?php endif; ?>

</div>
<!-- /.container-fluid -->