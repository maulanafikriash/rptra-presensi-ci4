<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>

    <div class="row">
        <div class="col-lg-3">
            <a href="<?= base_url('admin/master/shift/add'); ?>" class="btn btn-primary btn-icon-split mb-4">
                <span class="icon text-white">
                    <i class="fas fa-plus-circle"></i>
                </span>
                <span class="text">Tambah Shift</span>
            </a>
        </div>
        <div class="col-lg-5 offset-lg-4" id="flashdataMessage">
            <?= session()->getFlashdata('message'); ?>
        </div>
    </div>

    <!-- Data Table Shift-->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Tables Shift</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Waktu Mulai Shift</th>
                            <th>Waktu Berakhir Shift</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($shift as $sft) : ?>
                            <tr>
                                <td class="align-middle"><?= $i++; ?></td>
                                <td class="align-middle"><?= esc($sft['start_time']); ?></td>
                                <td class="align-middle"><?= esc($sft['end_time']); ?></td>
                                <td class="align-middle text-center">
                                    <a href="<?= base_url('admin/master/shift/edit/' . $sft['shift_id']); ?>" class="btn btn-primary btn-circle">
                                        <span class="icon text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                    </a> |
                                    <button type="button"
                                        class="btn btn-danger btn-circle delete-button"
                                        data-url="<?= base_url('admin/master/shift/delete/' . $sft['shift_id']); ?>"
                                        data-entity="shift"
                                        title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->