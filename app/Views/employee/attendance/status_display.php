<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0 text-gray-700 font-weight-bold"><?= esc($title); ?></h3>
    </div>

    <div class="row">
        <div class="col">
            <div class="card shadow mb-4" style="min-height: 543px">
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div class="text-center">
                        <i class="fas <?= esc($status_icon); ?> fa-5x <?= esc($status_color); ?> mb-4"></i>
                        <h5 class="font-weight-bold <?= esc($status_color); ?>"><?= esc($status_text); ?></h5>
                        
                        <?php if (isset($status_subtitle)) : ?>
                            <p class="text-gray-800 mt-3"><?= esc($status_subtitle); ?></p>
                        <?php else : ?>
                            <p class="text-gray-800 mt-3">Hari ini kamu <?= esc(strtolower($status_text)); ?>.</p>
                        <?php endif; ?>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>