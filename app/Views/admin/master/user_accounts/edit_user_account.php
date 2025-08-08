<!-- Begin Page Content -->
<div class="container-fluid">
    <?php $role_id = session()->get('user_role_id'); ?>
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>

    <a href="<?= base_url(
                    $role_id == 1
                        ? 'superadmin/master/admin_account'
                        : 'admin/master/user_account'
                ); ?>" class="btn btn-secondary btn-icon-split mb-3">
        <span class="icon text-white">
            <i class="fas fa-chevron-left"></i>
        </span>
        <span class="text">Kembali</span>
    </a>
    <form action="" method="POST" class="col-lg-5 p-0">
        <div class="card">
            <h5 class="card-header">Users Master Data</h5>
            <div class="card-body">
                <h5 class="card-title">Edit Akun User</h5>
                <p class="card-text">Form edit akun user di sistem</p>
                <div class="form-group">
                    <label for="u_username" class="col-form-label-md">Username</label>
                    <?php if ($role_id == 1): ?>
                        <input type="text"
                            class="form-control"
                            name="u_username"
                            id="u_username"
                            value="<?= set_value('u_username', esc($users['username'])); ?>">
                        <small class="text-danger"><?= esc($validation->getError('u_username') ?? ''); ?></small>
                    <?php else: ?>
                        <input type="text"
                            readonly
                            class="form-control-plaintext"
                            name="u_username"
                            value="<?= esc($users['username']); ?>">
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="password" class="col-form-label-md">Reset Password</label>
                    <input type="password" class="form-control form-control-md" name="password" id="password">
                    <small class="text-danger"><?= esc($validation->getError('password') ?? ''); ?></small>
                    <small class="form-text text-muted">Gunakan <b>rptra<?= date('Y'); ?></b> sebagai password default</small>
                </div>
                <button type="submit" class="btn btn-success btn-icon-split mt-4 float-right">
                    <span class="icon text-white">
                        <i class="fas fa-check"></i>
                    </span>
                    <span class="text">Simpan</span>
                </button>
            </div>
        </div>
    </form>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->