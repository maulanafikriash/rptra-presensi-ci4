<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-lg-5 col-md-8 col-sm-10 col-12">
            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body p-0">
                    <div class="text-center pt-4 pb-2 px-3 bg-primary text-white">
                        <div class="logo-container">
                            <img src="<?= base_url('../img/logo/logo-rptra.png'); ?>" alt="Company Logo">
                        </div>
                        <h4 class="font-weight-bold">Sistem Presensi Pegawai</h4>
                        <p class="font-weight-bold">RPTRA Cibubur Berseri</p>
                    </div>
                    <div class="p-4">
                        <?php if (session()->getFlashdata('success')) : ?>
                            <div id="success-message" class="alert alert-success">
                                <?= session()->getFlashdata('success'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div id="error-message" class="alert alert-danger">
                                <?= session()->getFlashdata('message'); ?>
                            </div>
                        <?php endif; ?>
                        <form class="user" method="POST" action="<?= base_url('auth/login'); ?>">
                            <?= csrf_field(); ?>
                            <div class="form-group">
                                <label for="username">Username<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" value="<?= old('username'); ?>" autocomplete="off">
                                </div>
                                <?php if (isset($validation) && $validation->hasError('username')) : ?>
                                    <small class="text-danger">
                                        <?= $validation->getError('username'); ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="password">Password<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password">
                                </div>
                                <?php if (isset($validation) && $validation->hasError('password')) : ?>
                                    <small class="text-danger">
                                        <?= $validation->getError('password'); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <button class="btn btn-primary btn-user btn-block" type="submit">
                                Login
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">&copy; <?= date('Y'); ?> CodeEagle Team</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(() => {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        if (successMessage) {
            successMessage.style.transition = "opacity 0.5s ease";
            successMessage.style.opacity = "0";
            setTimeout(() => successMessage.remove(), 500);
        }

        if (errorMessage) {
            errorMessage.style.transition = "opacity 0.5s ease";
            errorMessage.style.opacity = "0"; // Sembunyikan pesan dengan opacity
            setTimeout(() => errorMessage.remove(), 500); // Hapus elemen setelah animasi selesai
        }
    }, 5000);
</script>