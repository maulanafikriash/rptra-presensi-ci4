<div class="container mb-5">
    <h3 class="text-center text-gray-700 font-weight-bold"><?= esc($title); ?></h3>

    <!-- Pesan Flash -->
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

    <div class="col-md-6 mx-auto pt-3">
        <!-- Form menggunakan URL helper bawaan CI4 -->
        <form action="<?= base_url('employee/change_password') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <div class="form-group">
                <label for="current_password">Password Aktif</label>
                <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="off" required>
            </div>
            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input type="password" class="form-control" id="new_password" name="new_password" autocomplete="off" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" autocomplete="off" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Ubah Password</button>
        </form>
    </div>
</div>