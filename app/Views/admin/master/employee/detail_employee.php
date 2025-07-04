<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title); ?></h1>
        <a href="<?= base_url('admin/master/employee'); ?>" class="btn btn-secondary btn-md">
            <i class="fas fa-chevron-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
    <?php if (session()->getFlashdata('message')) : ?>
        <div class="row" id="flashdataMessage">
            <div class="col-lg-12">
                <?= session()->getFlashdata('message'); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    <img src="<?= base_url('img/pp/') . esc($employee['image']); ?>" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;" alt="Foto Profil">
                    <h5 class="card-title font-weight-bold text-gray-800 mb-1"><?= esc($employee['employee_name']); ?></h5>
                    <p class="text-muted mb-1">ID: <?= esc($employee['employee_id']); ?></p>
                    <p class="text-primary"><?= esc($department_current['department_name']); ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-sm-between">
                    <h6 class="m-0 font-weight-bold text-primary mb-2 mb-sm-0">Biodata Pegawai</h6>
                    <div class="action-buttons">
                        <a href="<?= base_url('admin/master/employee/edit/' . $employee['employee_id']); ?>" class="btn btn-primary btn-circle btn-md" title="Edit Data">
                            <i class="fas fa-edit"></i>
                        </a>

                        <?php if (esc($employee['department_id']) != 'ADM'): ?>
                            <a href="<?= base_url('admin/master/employee/work_schedule/') . esc($employee['employee_id']); ?>" class="btn btn-info btn-circle btn-md" title="Jadwal Kerja">
                                <i class="fas fa-calendar-alt"></i>
                            </a>
                            <a href="<?= base_url('admin/master/employee/attendance/') . esc($employee['employee_id']); ?>" class="btn btn-success btn-circle btn-md" title="Riwayat Kehadiran">
                                <i class="fas fa-calendar-check"></i>
                            </a>
                        <?php endif; ?>

                        <a href="<?= base_url('admin/report/print_biodata/pdf/') . esc($employee['employee_id']); ?>" class="btn btn-danger btn-circle btn-md" target="_blank" title="Cetak Biodata (PDF)">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $details = [
                        'Nama Lengkap' => $employee['employee_name'],
                        'Jenis Kelamin' => $employee['gender'] == 'Laki-Laki' ? 'Laki-Laki' : 'Perempuan',
                        'Tempat/Tgl Lahir'  => ($employee['birth_place'] ?? '-') . ', ' . date('d F Y', strtotime($employee['birth_date'])),
                        'Email' => $employee['email'],
                        'No. Telepon' => $employee['telephone'],
                        'Alamat' => $employee['employee_address'],
                        'Status Perkawinan' => $employee['marital_status'],
                        'Jumlah Anak' => $employee['num_children'],
                        'Pendidikan Terakhir' => $employee['education'],
                        'Tanggal Bergabung' => date('d F Y', strtotime($employee['hire_date'])),
                        'Penggunaan Kontrasepsi' => $employee['contraceptive_use'] ?? 'Tidak',
                    ];

                    $rptraDetails = [
                        'Nama RPTRA' => $employee['rptra_name'],
                        'Alamat RPTRA' => $employee['rptra_address'],
                    ];
                    ?>

                    <?php foreach ($details as $label => $value): ?>
                        <div class="row mb-2">
                            <div class="col-sm-4">
                                <span class="text-gray-700"><?= esc($label); ?></span>
                            </div>
                            <div class="col-sm-8">
                                <strong class="text-gray-900"><?= esc($value); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <hr>

                    <h6 class="font-weight-bold text-gray-800">Saat ini mengelola</h6>

                    <?php foreach ($rptraDetails as $label => $value): ?>
                        <div class="row mb-2">
                            <div class="col-sm-4">
                                <span class="text-gray-700"><?= esc($label); ?></span>
                            </div>
                            <div class="col-sm-8">
                                <strong class="text-gray-900"><?= esc($value); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </div>
</div>
<style>
    @media (max-width: 510px) {
        .action-buttons {
            align-self: flex-end;
        }

        .action-buttons .btn-circle.btn-md {
            width: 2.5rem !important;
            height: 2.5rem !important;
            font-size: 0.8rem !important;
            padding: 0 !important;
        }
    }
</style>