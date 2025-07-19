<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>

    <div class="row">
        <div class="col-lg-3">
            <a href="<?= base_url('admin/master/department/add'); ?>" class="btn btn-primary btn-icon-split mb-4">
                <span class="icon text-white-600">
                    <i class="fas fa-plus-circle"></i>
                </span>
                <span class="text">Tambah Department</span>
            </a>
        </div>
        <div class="col-lg-5 offset-lg-4" id="flashdataMessage">
            <?= session()->getFlashdata('message'); ?>
        </div>
    </div>

    <!-- Data Table Department-->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Tables Department</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Department</th>
                            <th>Nama Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($department as $dpt) : ?>
                            <tr>
                                <td class="align-middle"><?= esc($i++); ?></td>
                                <td class="align-middle"><?= esc($dpt['department_id']); ?></td>
                                <td class="align-middle"><?= esc($dpt['department_name']); ?></td>
                                <td class="align-middle text-center">
                                    <a href="<?= base_url('admin/master/department/edit/' . $dpt['department_id']); ?>" class="btn btn-primary btn-circle">
                                        <span class="icon text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                    </a>
                                    <?php if ($dpt['department_id'] != 'ADM') : ?>
                                        |
                                        <button type="button" class="btn btn-danger btn-circle delete-button" data-url="<?= base_url('admin/master/department/delete/' . $dpt['department_id']); ?>" data-entity="department" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php endif; ?>
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