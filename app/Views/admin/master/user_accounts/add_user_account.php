<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>

    <a href="<?= base_url('admin/master/user_account'); ?>" class="btn btn-secondary btn-icon-split mb-4">
        <span class="icon text-white">
            <i class="fas fa-chevron-left"></i>
        </span>
        <span class="text">Kembali</span>
    </a>
    <div class="col-lg-5 p-0">
        <form action="<?= base_url('admin/master/user_account/add/' . $e_id); ?>" method="POST">
            <div class="card">
                <h5 class="card-header">Users Master Data</h5>
                <div class="card-body">
                    <h5 class="card-title">Tambah akun user</h5>
                    <p class="card-text">Form untuk menambahkan akun user baru ke sistem</p>
                    <input type="hidden" name="e_id" value="<?= esc($e_id); ?>">
                    <div class="form-group row">
                        <label for="u_username" class="col-form-label col-md-3">Username :</label>
                        <div class="col pr-3">
                            <input type="text" readonly class="form-control-plaintext col-md" name="u_username" id="u_username" value="<?= esc($username); ?>">
                            <small class="text-danger"><?= esc($validation->getError('u_username') ?? ''); ?></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="u_password" class="col-form-label col-md-3">Password :</label>
                        <div class="col pr-3">
                            <input type="password" class="form-control col-md" name="u_password" id="u_password">
                            <small class="text-danger"><?= esc($validation->getError('u_password') ?? ''); ?></small>
                            <small class="form-text text-muted">Gunakan <b> rptra<?= date('Y'); ?> </b> sebagai password default</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-icon-split mt-4 float-right">
                        <span class="icon text-white">
                            <i class="fas fa-plus-circle"></i>
                        </span>
                        <span class="text">Tambahkan ke sistem</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
