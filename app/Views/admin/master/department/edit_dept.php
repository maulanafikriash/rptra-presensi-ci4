<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>

    <a href="<?= base_url('admin/master/department'); ?>" class="btn btn-secondary btn-icon-split mb-4">
        <span class="icon text-white">
            <i class="fas fa-chevron-left"></i>
        </span>
        <span class="text">Kembali</span>
    </a>

    <form action="" method="POST" class="col-lg-5 p-0">
        <div class="card">
            <h5 class="card-header">Department Master Data</h5>
            <div class="card-body">
                <h5 class="card-title">Edit Department</h5>
                <p class="card-text">Form untuk edit department ke sistem</p>
                <div class="form-group">
                    <label for="department_id" class="col-form-label-md">ID Department</label>
                    <input type="text" readonly class="form-control-plaintext form-control-md" name="d_id" value="<?= esc($d_old['department_id']); ?>">
                </div>
                <div class="form-group">
                    <label for="d_name" class="col-form-label-md">Nama Department</label>
                    <input type="text" class="form-control form-control-md" name="d_name" id="d_name" value="<?= esc($d_old['department_name']); ?>">
                    <?php if (isset($validation) && $validation->hasError('d_name')) : ?>
                        <small class="text-danger"><?= esc($validation->getError('d_name')); ?></small>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-success btn-icon-split mt-4 float-right">
                    <span class="icon text-white">
                        <i class="fas fa-check"></i>
                    </span>
                    <span class="text">Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </form>
</div>
<!-- /.container-fluid -->