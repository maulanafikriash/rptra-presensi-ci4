<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title); ?></h1>
    </div>

    <!-- Content Row (4 Cards) -->
    <div class="row">

        <!-- Departments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Departments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($display['c_department']); ?> Departments</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shift Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Shift Kerja</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($display['c_shift']); ?> Shifts</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pegawai Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pegawai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($display['c_employee']); ?> Pegawai</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-badge fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($display['c_users']); ?> Active Users</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Content Row: Department Pegawai and Pegawai per Shift -->
    <div class="row">
        <!-- Department Table -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Department Pegawai</h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>ID Department</th>
                                <th>Nama Department</th>
                                <th>Pegawai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($d_list as $dept) : ?>
                                <tr>
                                    <td><?= esc($dept['d_id']); ?></td>
                                    <td><?= esc($dept['d_name']); ?></td>
                                    <td><?= esc($dept['qty']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Shift Table -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Shift</h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>ID Shift</th>
                                <th>Waktu Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($s_list as $shift) : ?>
                                <tr>
                                    <td><?= esc($shift['shift_id']); ?></td>
                                    <td>
                                        <?= date('H:i', strtotime(esc($shift['start_time'])))
                                            . ' - ' .
                                            date('H:i', strtotime(esc($shift['end_time']))) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
<!-- /.container-fluid -->