<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>

    <a href="<?= base_url('admin/master/shift'); ?>" class="btn btn-secondary btn-icon-split mb-4">
        <span class="icon text-white">
            <i class="fas fa-chevron-left"></i>
        </span>
        <span class="text">Kembali</span>
    </a>
    <div class="col-lg-5" id="flashdataMessage">
        <?= session()->getFlashdata('message'); ?>
    </div>

    <form action="" method="POST" class="col-lg-7 p-0">
        <div class="card">
            <h5 class="card-header">Shift Master Data</h5>
            <div class="card-body">
                <h5 class="card-title">Edit Shift</h5>
                <p class="card-text">Form edit shift di sistem</p>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-md">Shift ke</label>
                    <div class="col-sm-4">
                        <input type="text" readonly class="form-control-plaintext form-control-md" value="<?= esc($s_id); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="s_start_h" class="col-form-label-md">Waktu Mulai Shift</label>
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="number" class="form-control form-control-md" name="s_start_h" id="s_start_h" max="23" min="0" value="<?= set_value('s_start_h', $s_sh); ?>">
                            <small class="text-danger"><?= esc($validation->getError('s_start_h') ?? ''); ?></small>
                        </div>
                        <div class="col-sm-4">
                            <input type="number" class="form-control form-control-md" name="s_start_m" id="s_start_m" max="59" min="0" value="<?= set_value('s_start_m', $s_sm); ?>">
                            <small class="text-danger"><?= esc($validation->getError('s_start_m') ?? ''); ?></small>
                        </div>
                        <div class="col-sm-4">
                            <input type="number" class="form-control form-control-md" name="s_start_s" id="s_start_s" max="59" min="0" value="<?= set_value('s_start_s', $s_ss); ?>">
                            <small class="text-danger"><?= esc($validation->getError('s_start_s') ?? ''); ?></small>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="s_end_h" class="col-form-label-md">Waktu Berakhir Shift</label>
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="number" class="form-control form-control-md" name="s_end_h" id="s_end_h" max="23" min="0" value="<?= set_value('s_end_h', $s_eh); ?>">
                            <small class="text-danger"><?= esc($validation->getError('s_end_h') ?? ''); ?></small>
                        </div>
                        <div class="col-sm-4">
                            <input type="number" class="form-control form-control-md" name="s_end_m" id="s_end_m" max="59" min="0" value="<?= set_value('s_end_m', $s_em); ?>">
                            <small class="text-danger"><?= esc($validation->getError('s_end_m') ?? ''); ?></small>
                        </div>
                        <div class="col-sm-4">
                            <input type="number" class="form-control form-control-md" name="s_end_s" id="s_end_s" max="59" min="0" value="<?= set_value('s_end_s', $s_es); ?>">
                            <small class="text-danger"><?= esc($validation->getError('s_end_s') ?? ''); ?></small>
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
    </form>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->