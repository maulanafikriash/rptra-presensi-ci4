<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <?php $role_id = session()->get('user_role_id'); ?>
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>
    <div class="row">
        <div class="col-lg-3">
            <?php if ($role_id == 1):
            ?>
                <a href="<?= base_url('superadmin/master/admin/add'); ?>"
                    class="btn btn-primary btn-icon-split mb-4">
                    <span class="icon text-white-600">
                        <i class="fas fa-user-shield"></i>
                    </span>
                    <span class="text">Tambah Admin</span>
                </a>
            <?php else:
            ?>
                <a href="<?= base_url('admin/master/employee/add'); ?>"
                    class="btn btn-primary btn-icon-split mb-4">
                    <span class="icon text-white-600">
                        <i class="fas fa-plus-circle"></i>
                    </span>
                    <span class="text">Tambah Pegawai</span>
                </a>
            <?php endif; ?>
        </div>
        <div class="col-lg-5 offset-lg-4" id="flashdataMessage">
            <?= session()->getFlashdata('message'); ?>
        </div>
    </div>


    <!-- Data Table employee -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Table <?= esc($title); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Foto</th>
                            <th>Tgl Lahir</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($employee as $emp) : ?>
                            <tr>
                                <td class="align-middle"><?= esc($i++); ?></td>
                                <td class="align-middle"><?= esc($emp['employee_id']); ?></td>
                                <td class="align-middle"><?= esc($emp['employee_name']); ?></td>
                                <td class="align-middle"><?= esc($emp['gender'] === 'Laki-Laki' ? 'Laki-Laki' : 'Perempuan'); ?></td>
                                <td class="text-center">
                                    <img src="<?= base_url('img/pp/' . $emp['image']); ?>" style="width: 55px; height:55px" class="img-rounded" alt="Foto">
                                </td>
                                <td class="align-middle"><?= esc(date('d-m-Y', strtotime($emp['birth_date']))); ?></td>
                                <td class="text-center align-middle">
                                    <!-- Detail Button -->
                                    <?php if ($role_id == 1): ?>
                                        <a href="<?= base_url('superadmin/master/admin/detail/' . $emp['employee_id']); ?>" class="btn btn-success btn-circle" title="Details">
                                            <i class="fas fa-info"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('admin/master/employee/detail/' . $emp['employee_id']); ?>" class="btn btn-success btn-circle" title="Details">
                                            <i class="fas fa-info"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($role_id == 1 || $emp['department_id'] !== 'ADM'): ?>
                                        |
                                        <!-- Delete Button -->
                                        <?php if ($role_id == 1): ?>
                                            <button type="button"
                                                class="btn btn-danger btn-circle delete-button"
                                                data-url="<?= base_url('superadmin/master/employee/delete/' . $emp['employee_id']); ?>"
                                                data-entity="admin"
                                                title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button"
                                                class="btn btn-danger btn-circle delete-button"
                                                data-url="<?= base_url('admin/master/employee/delete/' . $emp['employee_id']); ?>"
                                                data-entity="pegawai"
                                                title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->