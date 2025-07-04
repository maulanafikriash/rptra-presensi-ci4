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
    <div class="col-lg-12 p-0">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="card">
                <h5 class="card-header">Pegawai Master Data</h5>
                <div class="card-body">
                    <h5 class="card-title">Tambah Pegawai Baru</h5>
                    <p class="card-text">Form untuk menambahkan pegawai baru di sistem</p>
                    <div class="row pr-3">
                        <div class="col-lg-6" style="padding-right: 25px;">
                            <div class="form-group row">
                                <label for="employee_name" class="col-form-label col-lg-4">Nama Pegawai</label>
                                <div class="col p-0">
                                    <input type="text" class="form-control " name="employee_name" id="employee_name" value="<?= set_value('employee_name'); ?>" autofocus>
                                    <?php if (isset($validation) && $validation->hasError('employee_name')) : ?>
                                        <small class="text-danger"><?= esc($validation->getError('employee_name')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="department_id" class="col-form-label col-lg-4">Department</label>
                                <div class="col p-0">
                                    <select class="form-control" name="department_id" id="department_id">
                                        <?php foreach ($department as $dpt) : ?>
                                            <option value="<?= esc($dpt['department_id']); ?>" <?= set_select('department_id', $dpt['department_id'], $dpt['department_id'] === 'PLA'); ?>>
                                                <?= esc($dpt['department_id'] . ' - ' . $dpt['department_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($validation) && $validation->hasError('department_id')) : ?>
                                        <small class="text-danger"><?= esc($validation->getError('department_id')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-form-label col-lg-4">Email</label>
                                <div class="col p-0">
                                    <input type="email" class="form-control col-lg" name="email" id="email" value="<?= set_value('email'); ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="telephone" class="col-form-label col-lg-4">No.Telepon</label>
                                <div class="col p-0">
                                    <input type="number" class="form-control col-lg" name="telephone" id="telephone" value="<?= set_value('telephone'); ?>">
                                    <?php if (isset($validation) && $validation->hasError('telephone')) : ?>
                                        <small class="text-danger"><?= esc($validation->getError('telephone')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="gender" class="col-form-label col-lg-4">Jenis Kelamin</label>
                                <div class="form-check form-check-inline my-0">
                                    <input class="form-check-input" type="radio" name="gender" id="l" value="Laki-Laki" <?= set_radio('gender', 'Laki-Laki', true); ?>>
                                    <label class="form-check-label" for="l">Laki-Laki</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="p" value="Perempuan" <?= set_radio('gender', 'Perempuan'); ?>>
                                    <label class="form-check-label" for="p">Perempuan</label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="marital_status" class="col-form-label col-lg-4">Status Perkawinan</label>
                                <div class="col p-0">
                                    <select class="form-control" name="marital_status" id="marital_status">
                                        <option value="Belum Kawin" <?= set_select('marital_status', 'Belum Kawin'); ?>>Belum Kawin</option>
                                        <option value="Kawin" <?= set_select('marital_status', 'Kawin'); ?>>Kawin</option>
                                        <option value="Janda/Duda" <?= set_select('marital_status', 'Janda/Duda'); ?>>Janda/Duda</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="num_children" class="col-form-label col-lg-4">Jumlah Anak</label>
                                <div class="col p-0">
                                    <input type="number" class="form-control col-lg" name="num_children" id="num_children" value="<?= set_value('num_children'); ?>">
                                    <?php if (isset($validation) && $validation->hasError('num_children')) : ?>
                                        <small class="text-danger"><?= esc($validation->getError('num_children')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="contraceptive_use" class="col-form-label col-lg-4">Kontrasepsi yang dipakai</label>
                                <div class="col p-0">
                                    <input type="text" class="form-control col-lg" name="contraceptive_use" id="contraceptive_use" value="<?= set_value('contraceptive_use'); ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="education" class="col-form-label col-lg-4">Pendidikan</label>
                                <div class="col p-0">
                                    <input type="text" class="form-control col-lg" name="education" id="education" value="<?= set_value('education'); ?>">
                                    <?php if (isset($validation) && $validation->hasError('education')) : ?>
                                        <small class="text-danger"><?= esc($validation->getError('education')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="employee_address" class="col-form-label col-lg-4">Alamat</label>
                                <div class="col p-0">
                                    <textarea class="form-control col-lg" rows="4" name="employee_address" id="employee_address"><?= set_value('employee_address'); ?></textarea>
                                    <?php if (isset($validation) && $validation->hasError('employee_address')) : ?>
                                        <small class="text-danger"><?= esc($validation->getError('employee_address')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="birth_place" class="col-form-label col-lg-4">Tempat Lahir</label>
                                <div class="col-lg p-0">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="birth_place"
                                        id="birth_place"
                                        value="<?= set_value('birth_place'); ?>">
                                    <?php if (isset($validation) && $validation->hasError('birth_place')) : ?>
                                        <small class="text-danger"><?= esc($validation->getError('birth_place')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="birth_date" class="col-form-label col-lg-4">Tanggal Lahir</label>
                                <div class="col-lg p-0">
                                    <input type="date" class="form-control col-lg" name="birth_date" id="birth_date" value="<?= set_value('birth_date'); ?>">
                                    <?php if (isset($validation) && $validation->hasError('birth_date')) : ?>
                                        <small class="text-danger"><?= esc($validation->getError('birth_date')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="hire_date" class="col-form-label col-lg-4">Tanggal Bergabung</label>
                                <div class="col-lg p-0">
                                    <input type="date" class="form-control col-lg" name="hire_date" id="hire_date" value="<?= set_value('hire_date'); ?>">
                                    <?php if (isset($validation) && $validation->hasError('hire_date')) : ?>
                                        <small class="text-danger"><?= esc($validation->getError('hire_date')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="rptra_name" class="col-form-label col-lg-4">Nama RPTRA</label>
                                <div class="col p-0">
                                    <input type="text" class="form-control col-lg" name="rptra_name" id="rptra_name" value="<?= esc($rptra_name ?? '-'); ?>" readonly>
                                    <?php if (isset($validation) && $validation->hasError('rptra_name')) : ?>
                                        <small class="text-danger"><?= esc($validation->getError('rptra_name')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="rptra_address" class="col-form-label col-lg-4">Alamat RPTRA</label>
                                <div class="col p-0">
                                    <textarea
                                        class="form-control col-lg"
                                        id="rptra_address"
                                        rows="4"
                                        readonly><?= esc($rptra_address); ?></textarea>
                                    <input
                                        type="hidden"
                                        name="rptra_address"
                                        value="<?= esc($rptra_address); ?>">
                                </div>
                            </div>

                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-icon-split mt-4 float-right">
                        <span class="icon text-white">
                            <i class="fas fa-plus-circle"></i>
                        </span>
                        <span class="text">Simpan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /.container-fluid -->

<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>