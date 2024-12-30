<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>

    <div class="row">
        <div class="col-lg-3">
            <a href="<?= base_url('admin/master/employee'); ?>" class="btn btn-secondary btn-icon-split mb-4">
                <span class="icon text-white">
                    <i class="fas fa-chevron-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
    </div>

    <div class="col-lg p-0">
        <div class="row">
            <div class="col-lg-3">
                <div class="card" style="width: 100%; height: 100%">
                    <img src="<?= base_url('img/pp/') . esc($employee['image']); ?>" class="card-img-top w-75 mx-auto pt-3">
                    <div class="card-body mt-3">
                        <h5 class="card-title text-center"><?= esc($employee['employee_name']); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <h5 class="card-header">Biodata Pegawai</h5>
                    <div class="card-body">
                        <?php
                        $details = [
                            'ID Pegawai' => $employee['employee_id'],
                            'Nama' => $employee['employee_name'],
                            'Jenis Kelamin' => $employee['gender'] == 'Laki-Laki' ? 'Laki-Laki' : 'Perempuan',
                            'Status Perkawinan' => $employee['marital_status'],
                            'Jumlah Anak' => $employee['num_children'],
                            'Tanggal Lahir' => date('d-m-Y', strtotime($employee['birth_date'])),
                            'Tanggal Bergabung' => date('d-m-Y', strtotime($employee['hire_date'])),
                            'Pendidikan' => $employee['education'],
                            'Email' => $employee['email'],
                            'No.Telepon' => $employee['telephone'],
                            'Alamat' => $employee['employee_address'],
                            'Penggunaan Kontrasepsi' => $employee['contraceptive_use'] ?? 'Tidak',
                            'Shift' => $shift_current['shift_id'] . ' = ' . date('H:i', strtotime($shift_current['start'])) . ' - ' . date('H:i', strtotime($shift_current['end'])),
                            'Department' => $department_current['department_id'] . ' - ' . $department_current['department_name'],
                        ];

                        $rptraDetails = [
                            'Nama RPTRA' => $employee['rptra_name'],
                            'Alamat RPTRA' => $employee['rptra_address'],
                        ];
                        ?>

                        <?php foreach ($details as $label => $value): ?>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <p class="mb-0"><strong><?= esc($label); ?> :</strong></p>
                                </div>
                                <div class="col-lg-8">
                                    <p class="mb-0"><?= esc($value); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="row mt-5 pb-3">
                            <div class="col-12">
                                <h5 class="mb-0"><strong>Saat ini Mengelola :</strong></h5>
                            </div>
                        </div>

                        <?php foreach ($rptraDetails as $label => $value): ?>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <p class="mb-0"><strong><?= esc($label); ?> :</strong></p>
                                </div>
                                <div class="col-lg-8">
                                    <p class="mb-0"><?= esc($value); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <a href="<?= base_url('admin/master/employee/attendance/') . esc($employee['employee_id']); ?>" class="btn btn-success btn-icon-split mt-4 float-right">
                            <span class="icon text-white" title="Riwayat Kehadiran">
                                <i class="fas fa-calendar-check"></i>
                            </span>
                            <span class="text">Riwayat Kehadiran</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
</div>