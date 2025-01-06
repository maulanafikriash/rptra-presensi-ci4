<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= esc($title); ?></title>
    <link rel="icon" href="<?= base_url('../img/favicon.png'); ?>" type="image/png">

    <!-- Custom fonts for this template -->
    <link href="<?= base_url('../assets/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?= base_url('../css/sb-admin-2.css'); ?>" rel="stylesheet">
</head>

<body id="page-top">


    <!-- Begin Page Content -->
    <div class="container-fluid pt-5">

        <!-- 403 Error Text -->
        <div class="text-center pt-5">
            <div class="error mx-auto pt-12" data-text="403">403</div>
            <p class="lead text-gray-800 mb-5">Akses Ditolak</p>
            <p class="text-gray-700 mb-0">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
            <a href="<?= base_url('auth/login'); ?>" class="btn btn-primary mt-3">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->

    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url('../assets/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('../assets/boostrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <!-- Core plugin JavaScript-->
    <script src="<?= base_url('../assets/jquery-easing/jquery.easing.min.js'); ?>"></script>
    <!-- Custom scripts for all pages-->
    <script src="<?= base_url('../js/sb-admin-2.js'); ?>"></script>
</body>

</html>