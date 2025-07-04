<!-- Begin Page Content -->
<div class="container-fluid">

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
            <h6 class="m-0 font-weight-bold text-primary">DataTables Users</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID Pegawai</th>
                            <th>Nama Pegawai</th>
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
                                        <a href="<?= base_url('admin/master/user_account/edit/' . $dt['u_username']); ?>" class="btn btn-primary btn-circle">
                                            <span class="icon text-white" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                        </a> |
                                        <?php if ($dt['d_id'] === 'ADM') : ?>
                                            <button type="button" class="btn btn-danger btn-circle" title="Tidak dapat dihapus" disabled>
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php else : ?>
                                            <button type="button"
                                                class="btn btn-danger btn-circle delete-button"
                                                data-url="<?= base_url('admin/master/user_account/delete/' . $dt['u_username']); ?>"
                                                data-entity="akun pegawai"
                                                title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                <?php else : ?>
                                    <td class="align-middle text-center">
                                        <a href="<?= base_url('admin/master/user_account/add/' . $dt['e_id']); ?>" class="btn btn-primary">Buat Akun</a>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-primary btn-circle" disabled>
                                            <span class="icon text-white" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                        </button> |
                                        <button class="btn btn-danger btn-circle" disabled>
                                            <span class="icon text-white" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </span>
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