<!-- Begin Page Content -->
<div class="container-fluid">
    <?php $role_id = session()->get('user_role_id'); ?>
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>

    <div class="row">
        <div class="col-lg-5" id="flashdataMessage">
            <?= session()->getFlashdata('message'); ?>
        </div>
    </div>

    <!-- Data Table Users -->
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
                            <th>ID <?= esc($role_name); ?></th>
                            <th>Nama <?= esc($role_name); ?></th>
                            <th>ID Department</th>
                            <th>Username</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($data as $dt) : ?>
                            <tr>
                                <td class="align-middle"><?= $i++; ?></td>
                                <td class="align-middle"><?= esc($dt['e_id']); ?></td>
                                <td class="align-middle"><?= esc($dt['e_name']); ?></td>
                                <td class="align-middle"><?= esc($dt['d_id']); ?></td>
                                <?php if ($dt['u_username']) : ?>
                                    <td class="align-middle text-center">
                                        <?= esc($dt['u_username']); ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <!-- EDIT BUTTON -->
                                        <?php if ($role_id == 1): ?>
                                            <a href="<?= base_url('superadmin/master/admin_account/edit/' . $dt['u_username']); ?>"
                                                class="btn btn-primary btn-circle" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('admin/master/user_account/edit/' . $dt['u_username']); ?>"
                                                class="btn btn-primary btn-circle" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($role_id == 1 || $dt['d_id'] !== 'ADM'): ?>
                                            |
                                            <!-- Delete Button -->
                                            <?php if ($role_id == 1): ?>
                                                <button type="button"
                                                    class="btn btn-danger btn-circle delete-button"
                                                    data-url="<?= base_url('superadmin/master/admin_account/delete/' . $dt['u_username']); ?>"
                                                    data-entity="admin account"
                                                    title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button"
                                                    class="btn btn-danger btn-circle delete-button"
                                                    data-url="<?= base_url('admin/master/user_account/delete/' . $dt['u_username']); ?>"
                                                    data-entity="akun pegawai"
                                                    title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                <?php else : ?>
                                    <td class="align-middle text-center">
                                        <!-- ADD BUTTON -->
                                        <?php if ($role_id == 1): ?>
                                            <a href="<?= base_url('superadmin/master/admin_account/add/' . $dt['e_id']); ?>"
                                                class="btn btn-primary">Buat Akun</a>
                                        <?php else: ?>
                                            <a href="<?= base_url('admin/master/user_account/add/' . $dt['e_id']); ?>"
                                                class="btn btn-primary">Buat Akun</a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-primary btn-circle" disabled>
                                            <i class="fas fa-edit"></i>
                                        </button> |
                                        <!-- DELETE DISABLED karena belum ada akun -->
                                        <button class="btn btn-danger btn-circle" disabled>
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                <?php endif; ?>
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