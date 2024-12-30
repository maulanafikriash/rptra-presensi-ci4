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
                <h5 class="card-title">Tambahkan Department Baru</h5>
                <p class="card-text">Form untuk menambahkan department baru ke sistem</p>
                <div class="form-group">
                    <label for="d_id" class="col-form-label-md">ID Department</label>
                    <input type="text" class="form-control form-control-md" name="d_id" id="d_id" value="<?= set_value('d_id'); ?>">
                    <?php if (isset($validation) && $validation->hasError('d_id')) : ?>
                        <small class="text-danger"><?= esc($validation->getError('d_id')); ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="d_name" class="col-form-label-md">Nama Department</label>
                    <input type="text" class="form-control form-control-md" name="d_name" id="d_name" value="<?= set_value('d_name'); ?>">
                    <?php if (isset($validation) && $validation->hasError('d_name')) : ?>
                        <small class="text-danger"><?= esc($validation->getError('d_name')); ?></small>
                    <?php endif; ?>
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
<!-- /.container-fluid -->