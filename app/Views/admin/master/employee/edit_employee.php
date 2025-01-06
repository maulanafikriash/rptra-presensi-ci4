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
        <div class="col-lg-5 offset-lg-4" id="flashdataMessage">
            <?= session()->getFlashdata('message'); ?>
        </div>
    </div>

    <form action="<?= base_url('admin/master/employee/edit/' . $employee['employee_id']); ?>" method="POST" enctype="multipart/form-data">
        <div class="col-lg p-0">
            <div class="row">
                <div class="col-lg-10">
                    <div class="card">
                        <h5 class="card-header">Pegawai Master Data</h5>
                        <div class="card-body">
                            <h5 class="card-title">Edit Pegawai</h5>
                            <p class="card-text">Form edit Pegawai di sistem</p>
                            <div class="row">
                                <div class="col-lg-4 py-3">
                                    <div class="card" style="width: 100%; height: 100%">
                                        <img src="<?= base_url('img/pp/' . $employee['image']); ?>" class="card-img-top w-75 mx-auto pt-3" alt="Foto Pegawai">
                                        <div class="card-body mt-3">
                                            <label for="image">Ubah Foto Pegawai</label>
                                            <input type="file" name="image" id="image" class="form-control-image mt-2">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3">
                                    <div class="form-group">
                                        <label for="employee_id" class="col-form-label">ID Pegawai</label>
                                        <input type="text" readonly class="form-control-plaintext" name="employee_id" value="<?= esc($employee['employee_id']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="form-group">
                                        <label for="employee_name" class="col-form-label">Nama Pegawai</label>
                                        <input type="text" class="form-control" name="employee_name" id="employee_name" value="<?= esc($employee['employee_name']); ?>">
                                        <?php if (isset($validation) && $validation->hasError('employee_name')) : ?>
                                            <small class="text-danger"><?= esc($validation->getError('employee_name')); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="gender" class="col-form-label">Jenis Kelamin</label>
                                        <div class="d-flex">
                                            <div class="form-check mr-3">
                                                <input class="form-check-input" type="radio" name="gender" id="l" value="Laki-Laki" <?= $employee['gender'] === 'Laki-Laki' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="l">Laki-Laki</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" id="p" value="Perempuan" <?= $employee['gender'] === 'Perempuan' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="p">Perempuan</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="birth_date" class="col-form-label">Tanggal Lahir</label>
                                        <input type="date" class="form-control" name="birth_date" id="birth_date" value="<?= esc($employee['birth_date']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="hire_date" class="col-form-label">Tanggal Bergabung</label>
                                        <input type="date" class="form-control" name="hire_date" id="hire_date" value="<?= esc($employee['hire_date']); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="marital_status" class="col-form-label">Status Perkawinan</label>
                                        <select class="form-control" name="marital_status" id="marital_status">
                                            <option value="Belum Kawin" <?= $employee['marital_status'] === 'Belum Kawin' ? 'selected' : ''; ?>>Belum Kawin</option>
                                            <option value="Kawin" <?= $employee['marital_status'] === 'Kawin' ? 'selected' : ''; ?>>Kawin</option>
                                            <option value="Janda/Duda" <?= $employee['marital_status'] === 'Janda/Duda' ? 'selected' : ''; ?>>Janda/Duda</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="num_children" class="col-form-label">Jumlah Anak</label>
                                        <input type="number" class="form-control" name="num_children" id="num_children" value="<?= esc($employee['num_children']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="contraceptive_use" class="col-form-label">Kontrasepsi yang dipakai</label>
                                        <input type="text" class="form-control" name="contraceptive_use" id="contraceptive_use" value="<?= esc($employee['contraceptive_use']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="education" class="col-form-label">Pendidikan</label>
                                        <input type="text" class="form-control" name="education" id="education" value="<?= esc($employee['education']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="education" class="col-form-label">No.Telepon</label>
                                        <input type="number" class="form-control" name="telephone" id="telephone" value="<?= esc($employee['telephone']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="education" class="col-form-label">Email</label>
                                        <input type="text" class="form-control" name="email" id="email" value="<?= esc($employee['email']); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="department_id" class="col-form-label">Department</label>
                                        <select class="form-control" name="department_id" id="department_id">
                                            <?php
                                            $departmentSelected = array_column($department, 'department_id');
                                            $selectedDepartment = in_array($employee['department_id'], $departmentSelected) ? $employee['department_id'] : 'PLA';
                                            ?>
                                            <?php foreach ($department as $dpt) : ?>
                                                <option value="<?= esc($dpt['department_id']); ?>" <?= $dpt['department_id'] == $selectedDepartment ? 'selected' : ''; ?>>
                                                    <?= esc($dpt['department_id'] . ' - ' . $dpt['department_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="employee_address" class="col-form-label">Alamat Pegawai</label>
                                        <textarea class="form-control" rows="4" name="employee_address" id="employee_address"><?= esc($employee['employee_address']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="rptra_name" class="col-form-label">Nama RPTRA</label>
                                        <input type="text" class="form-control" name="rptra_name" id="rptra_name" value="<?= esc($employee['rptra_name']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="rptra_address" class="col-form-label">Alamat RPTRA</label>
                                        <textarea class="form-control" rows="4" name="rptra_address" id="rptra_address"><?= esc($employee['rptra_address']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-icon-split mt-4 float-right">
                                <span class="icon text-white">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="text">Simpan Perubahan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- /.container-fluid -->