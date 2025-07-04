<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>

    <!-- Flash Messages -->
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

    <div class="row">

        <!-- Left Column: Profile Picture -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    <img src="<?= base_url('img/pp/') . $account['image']; ?>"
                         class="rounded-circle img-fluid mb-3"
                         alt="Profile Picture"
                         style="width: 150px; height: 150px; object-fit: cover;">
                    
                    <h5 class="card-title"><?= esc($account['employee_name']); ?></h5>
                    <p class="card-text text-muted"><?= esc($account['department_name']); ?></p>

                    <hr>

                    <!-- Form Upload Foto -->
                    <form action="<?= base_url('employee/profile/edit'); ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field(); ?>
                        <label for="image">Ganti Foto Profil</label>
                        <div class="custom-file">
                           <input type="file" class="custom-file-input" id="image" name="image">
                           <label class="custom-file-label text-left" for="image">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted mt-2">Max. 3MB. Format: JPG, JPEG, PNG.</small>
                        <button type="submit" class="btn btn-primary btn-block mt-3">Upload Foto</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Employee Details -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    Detail Data Pegawai
                </div>
                <div class="card-body">
                    <!-- Data Pribadi -->
                    <h6 class="font-weight-bold text-primary">Data Pribadi</h6>
                    <hr class="mt-0">
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Nama Lengkap</strong></div>
                        <div class="col-sm-8">: <?= esc($account['employee_name']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Jenis Kelamin</strong></div>
                        <div class="col-sm-8">: <?= esc($account['gender']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Tempat/Tgl Lahir</strong></div>
                        <div class="col-sm-8">: <?= esc($account['birth_place'] ?? '-') . ', ' . date('d F Y', strtotime($account['birth_date'])); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Status Perkawinan</strong></div>
                        <div class="col-sm-8">: <?= esc($account['marital_status']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Jumlah Anak</strong></div>
                        <div class="col-sm-8">: <?= esc($account['num_children']); ?></div>
                    </div>
                     <div class="row mb-2">
                        <div class="col-sm-4"><strong>Penggunaan Kontrasepsi</strong></div>
                        <div class="col-sm-8">: <?= esc($account['contraceptive_use']) ?: 'Tidak'; ?></div>
                    </div>

                    <!-- Kontak & Alamat -->
                    <h6 class="font-weight-bold text-primary mt-4">Kontak & Pendidikan</h6>
                    <hr class="mt-0">
                     <div class="row mb-2">
                        <div class="col-sm-4"><strong>Pendidikan Terakhir</strong></div>
                        <div class="col-sm-8">: <?= esc($account['education']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Email</strong></div>
                        <div class="col-sm-8">: <?= esc($account['email']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Telepon</strong></div>
                        <div class="col-sm-8">: <?= esc($account['telephone']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Alamat</strong></div>
                        <div class="col-sm-8">: <?= esc($account['employee_address']); ?></div>
                    </div>

                    <!-- Data Pekerjaan -->
                    <h6 class="font-weight-bold text-primary mt-4">Data Pekerjaan</h6>
                    <hr class="mt-0">
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Bergabung Sejak</strong></div>
                        <div class="col-sm-8">: <?= date('d F Y', strtotime($account['hire_date'])); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Department</strong></div>
                        <div class="col-sm-8">: <?= esc($account['department_id']); ?> - <?= esc($account['department_name']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>RPTRA Saat Ini</strong></div>
                        <div class="col-sm-8">: <?= esc($account['rptra_name']); ?></div>
                    </div>
                     <div class="row mb-2">
                        <div class="col-sm-4"><strong>Alamat RPTRA</strong></div>
                        <div class="col-sm-8">: <?= esc($account['rptra_address']); ?></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<!-- /.container-fluid -->

<!-- Script untuk menampilkan nama file pada custom file input -->
<script>
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
  var fileName = document.getElementById("image").files[0].name;
  var nextSibling = e.target.nextElementSibling
  nextSibling.innerText = fileName
})
</script>