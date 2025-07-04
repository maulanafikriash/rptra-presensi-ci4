<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title); ?></h1>
        <a href="<?= base_url('admin/master/employee/detail/' . $employee['employee_id']); ?>" class="btn btn-secondary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-chevron-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    <?php if (session()->getFlashdata('message')) : ?>
        <div class="row">
            <div class="col-lg-12">
                <?= session()->getFlashdata('message'); ?>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/master/employee/edit/' . $employee['employee_id']); ?>" method="POST" enctype="multipart/form-data" id="editEmployeeForm">

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Foto Profil</h6>
                    </div>
                    <div class="card-body text-center">
                        <img src="<?= base_url('img/pp/' . esc($employee['image'])); ?>" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;" alt="Foto Pegawai" id="imagePreview">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="image" name="image" onchange="previewImg()">
                            <label class="custom-file-label" for="image">Pilih gambar...</label>
                            <?php if (isset($validation) && $validation->hasError('image')) : ?>
                                <small class="text-danger mt-1 d-block"><?= esc($validation->getError('image')); ?></small>
                            <?php endif; ?>
                        </div>
                        <small class="form-text text-muted mt-2">Maksimal 3MB, format: JPG, JPEG, PNG.</small>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Info Akun</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="employee_id">ID Pegawai</label>
                            <input type="text" class="form-control" id="employee_id" value="<?= esc($employee['employee_id']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="department_id">Department</label>
                            <!-- Tampilkan dropdown disabled -->
                            <select
                                class="form-control"
                                id="department_id"
                                disabled>
                                <option value="<?= esc($employee['department_id']); ?>" selected>
                                    <?= esc($employee['department_id'] . ' - ' . $employee['department_name']); ?>
                                </option>
                            </select>

                            <input type="hidden"
                                name="department_id"
                                value="<?= esc($employee['department_id']); ?>">

                            <?php if (isset($validation) && $validation->hasError('department_id')) : ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('department_id')); ?></div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi RPTRA</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="rptra_name">Nama RPTRA</label>
                            <input type="text"
                                id="rptra_name_display"
                                class="form-control"
                                value="<?= esc($rptra_name); ?>"
                                disabled>
                            <!-- tetap kirim nilainya -->
                            <input type="hidden" name="rptra_name" value="<?= esc($rptra_name); ?>">
                        </div>
                        <div class="form-group">
                            <label for="rptra_address">Alamat RPTRA</label>
                            <textarea id="rptra_address_display"
                                class="form-control"
                                rows="3"
                                disabled><?= esc($rptra_address); ?></textarea>
                            <!-- tetap kirim nilainya -->
                            <input type="hidden" name="rptra_address" value="<?= esc($rptra_address); ?>">
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Data Diri Pegawai</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="employee_name">Nama Lengkap</label>
                            <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('employee_name')) ? 'is-invalid' : ''; ?>" id="employee_name" name="employee_name" value="<?= old('employee_name', esc($employee['employee_name'])); ?>">
                            <?php if (isset($validation) && $validation->hasError('employee_name')) : ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('employee_name')); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="l" value="Laki-Laki" <?= (old('gender', $employee['gender']) === 'Laki-Laki') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="l">Laki-Laki</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="p" value="Perempuan" <?= (old('gender', $employee['gender']) === 'Perempuan') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="p">Perempuan</label>
                                        </div>
                                    </div>
                                    <?php if (isset($validation) && $validation->hasError('gender')) : ?>
                                        <small class="text-danger mt-1 d-block"><?= esc($validation->getError('gender')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="education">Pendidikan Terakhir</label>
                                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('education')) ? 'is-invalid' : ''; ?>" id="education" name="education" value="<?= old('education', esc($employee['education'])); ?>">
                                    <?php if (isset($validation) && $validation->hasError('education')) : ?>
                                        <div class="invalid-feedback"><?= esc($validation->getError('education')); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birth_place">Tempat Lahir</label>
                                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('birth_place')) ? 'is-invalid' : ''; ?>" id="birth_place" name="birth_place" value="<?= old('birth_place', esc($employee['birth_place'])); ?>">
                                    <?php if (isset($validation) && $validation->hasError('birth_place')) : ?>
                                        <div class="invalid-feedback"><?= esc($validation->getError('birth_place')); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birth_date">Tanggal Lahir</label>
                                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('birth_date')) ? 'is-invalid' : ''; ?>" id="birth_date" name="birth_date" value="<?= old('birth_date', esc($employee['birth_date'])); ?>">
                                    <?php if (isset($validation) && $validation->hasError('birth_date')) : ?>
                                        <div class="invalid-feedback"><?= esc($validation->getError('birth_date')); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="employee_address">Alamat Lengkap</label>
                            <textarea class="form-control <?= (isset($validation) && $validation->hasError('employee_address')) ? 'is-invalid' : ''; ?>" id="employee_address" name="employee_address" rows="3"><?= old('employee_address', esc($employee['employee_address'])); ?></textarea>
                            <?php if (isset($validation) && $validation->hasError('employee_address')) : ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('employee_address')); ?></div>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold">Informasi Kontak & Status</h6>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?= old('email', esc($employee['email'])); ?>">
                                    <?php if (isset($validation) && $validation->hasError('email')) : ?>
                                        <div class="invalid-feedback"><?= esc($validation->getError('email')); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone">No. Telepon</label>
                                    <input type="tel" class="form-control <?= (isset($validation) && $validation->hasError('telephone')) ? 'is-invalid' : ''; ?>" id="telephone" name="telephone" value="<?= old('telephone', esc($employee['telephone'])); ?>">
                                    <?php if (isset($validation) && $validation->hasError('telephone')) : ?>
                                        <div class="invalid-feedback"><?= esc($validation->getError('telephone')); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="marital_status">Status Perkawinan</label>
                                    <select class="form-control <?= (isset($validation) && $validation->hasError('marital_status')) ? 'is-invalid' : ''; ?>" name="marital_status" id="marital_status">
                                        <option value="Belum Kawin" <?= (old('marital_status', $employee['marital_status']) === 'Belum Kawin') ? 'selected' : ''; ?>>Belum Kawin</option>
                                        <option value="Kawin" <?= (old('marital_status', $employee['marital_status']) === 'Kawin') ? 'selected' : ''; ?>>Kawin</option>
                                        <option value="Janda/Duda" <?= (old('marital_status', $employee['marital_status']) === 'Janda/Duda') ? 'selected' : ''; ?>>Janda/Duda</option>
                                    </select>
                                    <?php if (isset($validation) && $validation->hasError('marital_status')) : ?>
                                        <div class="invalid-feedback"><?= esc($validation->getError('marital_status')); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="num_children">Jumlah Anak</label>
                                    <input type="number" class="form-control <?= (isset($validation) && $validation->hasError('num_children')) ? 'is-invalid' : ''; ?>" id="num_children" name="num_children" value="<?= old('num_children', esc($employee['num_children'])); ?>" min="0">
                                    <?php if (isset($validation) && $validation->hasError('num_children')) : ?>
                                        <div class="invalid-feedback"><?= esc($validation->getError('num_children')); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contraceptive_use">Kontrasepsi yang Dipakai</label>
                                    <input type="text" class="form-control" name="contraceptive_use" id="contraceptive_use" value="<?= old('contraceptive_use', esc($employee['contraceptive_use'])); ?>" placeholder="(Opsional)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hire_date">Tanggal Bergabung</label>
                                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('hire_date')) ? 'is-invalid' : ''; ?>" id="hire_date" name="hire_date" value="<?= old('hire_date', esc($employee['hire_date'])); ?>">
                                    <?php if (isset($validation) && $validation->hasError('hire_date')) : ?>
                                        <div class="invalid-feedback"><?= esc($validation->getError('hire_date')); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success btn-icon-split float-right">
                            <span class="icon text-white-50">
                                <i class="fas fa-save"></i>
                            </span>
                            <span class="text">Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    function previewImg() {
        const image = document.querySelector('#image');
        const imagePreview = document.querySelector('#imagePreview');
        const imageLabel = document.querySelector('.custom-file-label');

        imageLabel.textContent = image.files[0].name;

        const fileReader = new FileReader();
        fileReader.readAsDataURL(image.files[0]);

        fileReader.onload = function(e) {
            imagePreview.src = e.target.result;
        }
    }
</script>