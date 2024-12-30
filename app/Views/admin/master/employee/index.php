<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>

    <div class="row">
        <div class="col-lg-3">
            <a href="<?= base_url('admin/master/employee/add'); ?>" class="btn btn-primary btn-icon-split mb-4">
                <span class="icon text-white-600">
                    <i class="fas fa-plus-circle"></i>
                </span>
                <span class="text">Tambah Pegawai</span>
            </a>
        </div>
        <div class="col-lg-5 offset-lg-4" id="flashdataMessage">
            <?= session()->getFlashdata('message'); ?>
        </div>
    </div>

    <!-- Data Table employee -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Tables Pegawai</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Shift</th>
                            <th>Jenis Kelamin</th>
                            <th>Foto</th>
                            <th>Tgl Lahir</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($employee as $emp) : ?>
                            <?php if ($emp['shift_id'] == 0) {
                                continue;
                            } ?>
                            <tr>
                                <td class="align-middle"><?= esc($i++); ?></td>
                                <td class="align-middle"><?= esc($emp['employee_id']); ?></td>
                                <td class="align-middle"><?= esc($emp['employee_name']); ?></td>
                                <td class="align-middle"><?= esc($emp['shift_id']); ?></td>
                                <td class="align-middle">
                                    <?= esc($emp['gender'] === 'Laki-Laki' ? 'Laki-Laki' : 'Perempuan'); ?>
                                </td>
                                <td class="text-center">
                                    <img src="<?= base_url('img/pp/' . $emp['image']); ?>" style="width: 55px; height:55px" class="img-rounded">
                                </td>
                                <td class="align-middle"><?= esc(date('d-m-Y', strtotime($emp['birth_date']))); ?></td>
                                <td class="text-center align-middle">
                                    <a href="<?= base_url('admin/master/employee/detail/' . $emp['employee_id']); ?>" class="btn btn-success btn-circle">
                                        <span class="icon" title="Details">
                                            <i class="fas fa-info"></i>
                                        </span>
                                    </a> |
                                    <a href="<?= base_url('admin/master/employee/edit/' . $emp['employee_id']); ?>" class="btn btn-primary btn-circle">
                                        <span class="icon text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                    </a> |
                                    <button type="button"
                                        class="btn btn-danger btn-circle delete-button"
                                        data-url="<?= base_url('admin/master/employee/delete/' . $emp['employee_id']); ?>"
                                        data-entity="pegawai"
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