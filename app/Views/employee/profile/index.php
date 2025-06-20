<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">
        <!-- Left Section -->
        <div class="col-sm-10 col-md-5 col-lg-4 col-xl-3 offset-sm-1 offset-md-0 offset-lg-0 offset-xl-0">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('message'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <img src="<?= base_url('img/pp/') . $account['image']; ?>" class="rounded-circle img-thumbnail account-image" alt="Profile Picture">
            <form action="<?= base_url('employee/profile/edit'); ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="image">Ganti Foto Profil</label>
                    <input type="file" class="form-control" name="image" id="image">
                    <button type="submit" class="btn btn-primary mt-2">Upload Foto</button>
                </div>
            </form>
        </div>

        <!-- Right Section -->
        <div class="col-sm-10 col-md-6 offset-sm-1">
            <h1 class="h3 text-white bg-primary px-3 py-2 rounded mt-1 mb-3 data-pegawai"><?= esc($title); ?></h1>
            <table class="table">
                <tbody>
                    <tr>
                        <th scope="row">Nama</th>
                        <td>: <?= esc($account['employee_name']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Jenis Kelamin</th>
                        <td>: <?= esc($account['gender']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Status Perkawinan</th>
                        <td>: <?= esc($account['marital_status']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Jumlah Anak</th>
                        <td>: <?= esc($account['num_children']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Tanggal Lahir</th>
                        <td>: <?= date('d-m-Y', strtotime($account['birth_date'])); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Bergabung Sejak</th>
                        <td>: <?= date('d-m-Y', strtotime($account['hire_date'])); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Pendidikan</th>
                        <td>: <?= esc($account['education']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Email</th>
                        <td>: <?= esc($account['email']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Telepon</th>
                        <td>: <?= esc($account['telephone']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Alamat</th>
                        <td>: <?= esc($account['employee_address']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Penggunaan Kontrasepsi</th>
                        <td>: <?= esc($account['contraceptive_use']) ?: 'Tidak'; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Department</th>
                        <td>
                            : <?= esc($account['department_id']); ?> - <?= esc($account['department_name']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="font-weight-bold text-center">Saat ini Mengelola</td>
                    </tr>
                    <tr>
                        <th scope="row">Nama RPTRA</th>
                        <td>: <?= esc($account['rptra_name']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Alamat RPTRA</th>
                        <td>: <?= esc($account['rptra_address']); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->