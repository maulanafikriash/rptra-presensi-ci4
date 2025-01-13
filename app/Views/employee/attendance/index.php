<div class="container-fluid">

  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h3 class="mb-0 text-gray-700 font-weight-bold"><?= esc($title); ?></h3>
  </div>

  <!-- Content Row -->
  <div class="row">
    <div class="col">
      <div class="row">
        <!-- Attendance Card -->
        <div class="col-xl col-lg">
          <div class="card shadow mb-4" style="min-height: 543px">
            <!-- Card Header -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Isi Kehadiran Anda!</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">

              <!-- Menampilkan Pesan Flashdata -->
              <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-dismissible fade show" role="alert">
                  <?= session()->getFlashdata('message'); ?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              <?php endif; ?>

              <form id="attendance-form" action="<?= base_url('employee/attendance'); ?>" method="POST">

                <!-- CSRF Field -->
                <?= csrf_field() ?>

                <!-- Bagian Shift dan Lokasi -->
                <div class="row">
                  <div class="col-lg-5">
                    <label for="work_shift" class="col-form-label">Shift Kerja</label>
                    <?php if ($has_shift && $shift_details): ?>
                      <input class="form-control" type="text" placeholder="Shift <?= esc($shift_details['shift_id']); ?> = <?= esc($shift_start_time); ?> - <?= esc($shift_end_time); ?>" value="Shift <?= esc($shift_details['shift_id']); ?> = <?= esc($shift_start_time); ?> - <?= esc($shift_end_time); ?>" name="work_shift_display" readonly>
                      <input type="hidden" name="work_shift" value="<?= esc($shift_details['shift_id']); ?>">
                    <?php else: ?>
                      <?php if ($presence_status == 5): // Libur 
                      ?>
                        <div class="d-flex align-items-center">
                          <i class="fas fa-calendar-day fa-2x text-primary mr-2"></i>
                          <span class="text-primary font-weight-bold">Hari ini Libur</span>
                        </div>
                      <?php elseif ($presence_status == 4): // Cuti 
                      ?>
                        <div class="d-flex align-items-center">
                          <i class="fas fa-calendar-check fa-2x text-dark mr-2"></i>
                          <span class="text-dark font-weight-bold">Hari ini Cuti</span>
                        </div>
                      <?php elseif ($presence_status == 2): // Izin 
                      ?>
                        <div class="d-flex align-items-center">
                          <i class="fas fa-user-clock fa-2x text-warning mr-2"></i>
                          <span class="text-warning font-weight-bold">Hari ini Izin</span>
                        </div>
                      <?php elseif ($presence_status == 3): // Sakit 
                      ?>
                        <div class="d-flex align-items-center">
                          <i class="fas fa-medkit fa-2x text-warning mr-2"></i>
                          <span class="text-warning font-weight-bold">Hari ini Sakit</span>
                        </div>
                      <?php else: // Tidak Ada Jadwal 
                      ?>
                        <div class="d-flex align-items-center">
                          <i class="fas fa-calendar-alt fa-2x text-secondary mr-2"></i>
                          <span class="text-secondary font-weight-bold">Tidak Ada Jadwal</span>
                        </div>
                      <?php endif; ?>
                      <input type="hidden" name="work_shift" value="">
                    <?php endif; ?>
                  </div>

                  <!-- Tombol untuk mengaktifkan lokasi -->
                  <div class="col-lg-5 offset-lg-1 location-container">
                    <?php if ($has_shift && in_array($presence_status, [null, 1])): ?>
                      <label for="location" class="col-form-label">Aktifkan Lokasi Saat Ini</label>
                      <button type="button" class="btn btn-primary btn-lg btn-block shadow-sm" id="activate-location-btn" style="display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s;">
                        <i class="fas fa-map-marker-alt mr-2"></i> Aktifkan Lokasi
                      </button>

                      <!-- Menampilkan status lokasi dan menyimpan latitude/longitude -->
                      <p id="location-status" class="mt-2 text-muted text-center" style="font-size: 14px;">Lokasi belum diaktifkan</p>
                      <input type="hidden" name="latitude" id="latitude">
                      <input type="hidden" name="longitude" id="longitude">
                    <?php endif; ?>
                  </div>
                </div>

                <div class="row justify-content-center mb-3">
                  <div class="col-lg-6 text-center">
                    <hr>
                    <?php
                    // Definisikan array mapping untuk presence_status
                    $presenceStatuses = [
                      1 => [
                        'label' => 'Hadir',
                        'class' => 'btn-success',
                        'icon' => 'fa-check',
                        'text_class' => 'text-success'
                      ],
                      0 => [
                        'label' => 'Tidak Hadir',
                        'class' => 'btn-danger',
                        'icon' => 'fa-times',
                        'text_class' => 'text-danger'
                      ],
                      2 => [
                        'label' => 'Izin',
                        'class' => 'btn-warning',
                        'icon' => 'fa-calendar-day',
                        'text_class' => 'text-warning'
                      ],
                      3 => [
                        'label' => 'Sakit',
                        'class' => 'btn-warning',
                        'icon' => 'fa-medkit',
                        'text_class' => 'text-warning'
                      ],
                      4 => [
                        'label' => 'Cuti',
                        'class' => 'btn-dark',
                        'icon' => 'fa-calendar-check',
                        'text_class' => 'text-dark'
                      ],
                      5 => [
                        'label' => 'Libur',
                        'class' => 'btn-primary',
                        'icon' => 'fa-calendar-times',
                        'text_class' => 'text-primary'
                      ],
                      'default' => [
                        'label' => 'Tidak Hadir',
                        'class' => 'btn-danger',
                        'icon' => 'fa-times',
                        'text_class' => 'text-danger'
                      ]
                    ];

                    // Ambil status berdasarkan presence_status
                    $status = $presenceStatuses[$presence_status] ?? $presenceStatuses['default'];
                    ?>

                    <div class="d-flex justify-content-center align-items-center">
                      <!-- Tombol Status Presensi -->
                      <div class="text-center mt-3">
                        <button class="btn <?= esc($status['class']); ?> btn-circle" style="font-size: 20px; width: 100px; height: 100px;" disabled>
                          <i class="fas <?= esc($status['icon']); ?> fa-2x"></i>
                        </button>

                        <p class="font-weight-bold <?= esc($status['text_class']); ?> pt-2">
                          <?= esc($status['label']); ?>
                        </p>
                      </div>

                      <?php
                      // Tentukan apakah Tombol Presensi Masuk/Keluar akan ditampilkan
                      $show_presensi_button = false;

                      if ($has_shift && !in_array($presence_status, [2, 3])) {
                        if ($shift_status === 'presensi masuk' && !$already_checked_in) {
                          $show_presensi_button = true;
                        } elseif ($shift_status === 'presensi masuk' && $already_checked_in && !$already_checked_out) {
                          $show_presensi_button = true;
                        } elseif ($shift_status === 'sudah selesai' && !$already_checked_in) {
                          $show_presensi_button = false;
                        } elseif ($shift_status === 'sudah selesai' && $already_checked_in && !$already_checked_out) {
                          $show_presensi_button = true;
                        } elseif ($shift_status === 'sudah selesai' && $already_checked_in && $already_checked_out) {
                          $show_presensi_button = true;
                        } elseif ($shift_status === 'belum mulai') {
                          $show_presensi_button = true;
                        }
                      }
                      ?>

                      <?php if ($show_presensi_button): ?>
                        <!-- Spacer -->
                        <div style="width: 4rem;"></div>
                      <?php endif; ?>

                      <!-- Tombol Presensi Masuk atau Keluar -->
                      <?php if ($show_presensi_button): ?>
                        <div class="text-center mt-3">
                          <?php
                          if (!$has_shift) {
                            // Tidak Ada Jadwal Shift atau status lainnya sudah ditangani di Bagian Shift dan Lokasi
                            // Tidak perlu menampilkan apa-apa di sini jika tidak ada shift
                          } else {
                            // Ada Jadwal Shift, lanjutkan dengan logika presensi
                            if ($shift_status === 'belum mulai') {
                              // Shift Belum Mulai
                          ?>
                              <button type="button" class="btn btn-dark btn-circle" style="font-size: 20px; width: 100px; height: 100px;" disabled>
                                <i class="fas fa-fw fa-sign-in-alt fa-2x"></i>
                              </button>
                              <p class="font-weight-bold text-dark pt-2">Belum Mulai</p>
                              <?php
                            } elseif ($shift_status === 'presensi masuk') {
                              if (!$already_checked_in) {
                                // Shift Mulai dan Belum Check-In
                              ?>
                                <button type="button" name="check_in" value="1" class="btn btn-primary btn-circle" id="check-in-btn" style="font-size: 20px; width: 100px; height: 100px;" <?= !$can_check_in ? 'disabled' : ''; ?>>
                                  <i class="fas fa-fw fa-sign-in-alt fa-2x"></i>
                                </button>
                                <p class="font-weight-bold text-primary pt-2"><?= $can_check_in ? 'Masuk!' : 'Masuk!' ?></p>
                              <?php
                              } else {
                                // Sudah Check-In dan Belum Check-Out
                              ?>
                                <button type="button" class="btn btn-dark btn-circle" style="font-size: 20px; width: 100px; height: 100px;" disabled>
                                  <i class="fas fa-fw fa-sign-out-alt fa-2x"></i>
                                </button>
                                <p class="text-dark pt-2" style="font-size: small;">Presensi keluar terbuka <br> setelah shift selesai.</p>
                              <?php
                              }
                            } elseif ($shift_status === 'sudah selesai') {
                              if (!$already_checked_in) {
                                // Shift Sudah Selesai dan Belum Check-In
                              ?>
                                <button type="button" class="btn btn-dark btn-circle" style="font-size: 20px; width: 100px; height: 100px;" disabled>
                                  <i class="fas fa-fw fa-sign-in-alt fa-2x"></i>
                                </button>
                                <p class="text-dark pt-2" style="font-size: small;">Anda terlambat <br> shift sudah selesai</p>
                              <?php
                              } elseif ($already_checked_in && !$already_checked_out) {
                                // Shift Sudah Selesai dan Sudah Check-In tapi Belum Check-Out
                              ?>
                                <button type="button" name="check_out" value="1" class="btn btn-danger btn-circle" id="check-out-btn" style="font-size: 20px; width: 100px; height: 100px;" <?= !$can_check_out ? 'disabled' : ''; ?>>
                                  <i class="fas fa-fw fa-sign-out-alt fa-2x"></i>
                                </button>
                                <p class="font-weight-bold text-danger pt-2"><?= $can_check_out ? 'Keluar' : 'Keluar' ?></p>
                              <?php
                              } elseif ($already_checked_out) {
                                // Sudah Check-Out
                              ?>
                                <button type="button" class="btn btn-dark btn-circle" style="font-size: 20px; width: 100px; height: 100px;" disabled>
                                  <i class="fas fa-fw fa-sign-out-alt fa-2x"></i>
                                </button>
                                <p class="font-weight-bold text-dark pt-2">Sudah Keluar</p>
                              <?php
                              }
                            } else {
                              // Kondisi Default (jika ada)
                              ?>
                              <button type="button" class="btn btn-dark btn-circle" style="font-size: 20px; width: 100px; height: 100px;" disabled>
                                <i class="fas fa-fw fa-sign-in-alt fa-2x"></i>
                              </button>
                              <p class="font-weight-bold text-dark pt-2">Tidak Ada Status</p>
                          <?php
                            }
                          }
                          ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End of Main Content -->

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const shiftStatus = "<?= esc($shift_status); ?>";
    const alreadyCheckedIn = <?= json_encode($already_checked_in); ?>;
    const alreadyCheckedOut = <?= json_encode($already_checked_out ?? false); ?>;
    const presenceStatus = <?= json_encode($presence_status); ?>;
    const hasShift = <?= json_encode($has_shift); ?>;

    const activateLocationBtn = document.getElementById("activate-location-btn");
    const checkInBtn = document.getElementById("check-in-btn");
    const checkOutBtn = document.getElementById("check-out-btn");
    const locationStatus = document.getElementById("location-status");
    const attendanceForm = document.getElementById("attendance-form");

    // CSRF Token
    const csrfName = "<?= csrf_token() ?>";
    const csrfHash = "<?= csrf_hash() ?>";

    // Fungsi untuk mengaktifkan lokasi
    function activateLocation(callback) {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          document.getElementById("latitude").value = position.coords.latitude;
          document.getElementById("longitude").value = position.coords.longitude;
          locationStatus.textContent = "Lokasi berhasil diaktifkan!";
          locationStatus.classList.remove("text-muted");
          locationStatus.classList.add("text-success", "font-weight-bold");

          Swal.fire({
            icon: 'success',
            title: 'Sukses',
            text: 'Lokasi Anda Berhasil Diaktifkan',
            confirmButtonText: 'Oke'
          });

          if (callback) callback(true);
        }, function(error) {
          Swal.fire({
            icon: 'error',
            title: 'Gagal Mengaktifkan Lokasi',
            text: 'Harap izinkan akses lokasi di browser Anda.',
            confirmButtonText: 'Oke'
          });
          if (callback) callback(false);
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Geolocation tidak didukung oleh browser Anda.',
          confirmButtonText: 'Oke'
        });
        if (callback) callback(false);
      }
    }

    // Kondisi Tidak Ada Jadwal Shift
    if (!hasShift) {
      // Hilangkan tampilan form shift dan button aktifkan lokasi
      const shiftForm = document.querySelector('input[name="work_shift_display"]');
      if (shiftForm) shiftForm.parentElement.style.display = 'none';

      const locationContainer = document.querySelector('.location-container');
      if (locationContainer) locationContainer.style.display = 'none';

      // Tampilkan ikon kalender kosong
      // Sudah dilakukan di bagian atas dengan PHP
    } else {
      // Ada Jadwal Shift, lanjutkan dengan logika presensi
      if (shiftStatus === "belum mulai") {
        activateLocationBtn.disabled = true;
        if (checkInBtn) {
          checkInBtn.disabled = true;
          checkInBtn.classList.remove("btn-primary");
          checkInBtn.classList.add("btn-dark");
        }
        if (checkOutBtn) {
          checkOutBtn.disabled = true;
          checkOutBtn.classList.remove("btn-danger");
          checkOutBtn.classList.add("btn-dark");
        }
      }

      // Kondisi Shift Sudah Mulai dan Belum Check-In
      else if (shiftStatus === "presensi masuk" && !alreadyCheckedIn) {
        activateLocationBtn.disabled = false;
        if (checkInBtn) {
          checkInBtn.disabled = true;
        }

        activateLocationBtn.addEventListener("click", function() {
          activateLocation(function(success) {
            if (success && checkInBtn) {
              checkInBtn.disabled = false;
            }
          });
        });

        // Handle Check-In via AJAX
        if (checkInBtn) {
          checkInBtn.addEventListener("click", function() {
            const latitude = document.getElementById("latitude").value;
            const longitude = document.getElementById("longitude").value;

            if (!latitude || !longitude) {
              Swal.fire({
                icon: 'warning',
                title: 'Lokasi Belum Aktif',
                text: 'Harap aktifkan lokasi sebelum melakukan presensi.',
                confirmButtonText: 'Oke'
              });
              return;
            }

            // Disable button to prevent multiple submissions
            checkInBtn.disabled = true;
            checkInBtn.classList.remove("btn-primary");
            checkInBtn.classList.add("btn-dark");

            // Send AJAX request
            fetch("<?= base_url('employee/attendance'); ?>", {
                method: "POST",
                headers: {
                  "Content-Type": "application/x-www-form-urlencoded",
                  "X-Requested-With": "XMLHttpRequest",
                },
                body: new URLSearchParams({
                  check_in: 1,
                  work_shift: "<?= esc($shift_details['shift_id'] ?? ''); ?>",

                  latitude: latitude,
                  longitude: longitude,
                  [csrfName]: csrfHash // Sertakan CSRF token
                })
              })
              .then(response => response.json())
              .then(data => {
                if (data.status === 'success') {
                  Swal.fire({
                    icon: 'success',
                    title: 'Sukses',
                    text: `Berhasil presensi masuk pada pukul ${new Date().toLocaleTimeString()}`,
                    confirmButtonText: 'Oke'
                  }).then(() => {
                    // Reload halaman setelah sukses
                    location.reload();
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message,
                    confirmButtonText: 'Oke'
                  });
                  // Re-enable button
                  checkInBtn.disabled = false;
                  checkInBtn.classList.remove("btn-dark");
                  checkInBtn.classList.add("btn-primary");
                }
              })
              .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Terjadi kesalahan saat melakukan presensi.',
                  confirmButtonText: 'Oke'
                });
                // Re-enable button
                checkInBtn.disabled = false;
                checkInBtn.classList.remove("btn-dark");
                checkInBtn.classList.add("btn-primary");
              });
          });
        }
      }

      // Kondisi Shift Sudah Selesai namun Belum Check-In
      else if (shiftStatus === "sudah selesai" && !alreadyCheckedIn) {
        activateLocationBtn.disabled = true;
        if (checkInBtn) {
          checkInBtn.disabled = true;
          checkInBtn.classList.remove("btn-primary");
          checkInBtn.classList.add("btn-dark");
        }
        if (checkOutBtn) {
          checkOutBtn.disabled = true;
          checkOutBtn.classList.remove("btn-danger");
          checkOutBtn.classList.add("btn-dark");
        }
        locationStatus.textContent = "Waktu Shift sudah selesai.";
        locationStatus.classList.remove("text-muted");
        locationStatus.classList.add("text-danger");
        Swal.fire({
          icon: 'warning',
          title: 'Waktu Shift Sudah Selesai',
          text: 'Anda terlambat melakukan presensi.',
          confirmButtonText: 'Oke'
        });
      }

      // Kondisi Sudah Check-In
      if (alreadyCheckedIn) {
        if (!alreadyCheckedOut) {
          // Presensi Masuk sudah dilakukan, tampilkan tombol Presensi Keluar jika shift sudah selesai
          if (shiftStatus === "sudah selesai" && checkOutBtn) {
            activateLocationBtn.disabled = false;
            checkOutBtn.disabled = true;
            checkOutBtn.classList.remove("btn-danger");
            checkOutBtn.classList.add("btn-danger"); // Tetap merah

            activateLocationBtn.addEventListener("click", function() {
              activateLocation(function(success) {
                if (success && checkOutBtn) {
                  checkOutBtn.disabled = false;
                }
              });
            });

            // Handle Check-Out via AJAX
            checkOutBtn.addEventListener("click", function() {
              const latitude = document.getElementById("latitude").value;
              const longitude = document.getElementById("longitude").value;

              if (!latitude || !longitude) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Lokasi Belum Aktif',
                  text: 'Harap aktifkan lokasi sebelum melakukan presensi keluar.',
                  confirmButtonText: 'Oke'
                });
                return;
              }

              // Disable button to prevent multiple submissions
              checkOutBtn.disabled = true;
              checkOutBtn.classList.remove("btn-danger");
              checkOutBtn.classList.add("btn-dark");

              // Send AJAX request
              fetch("<?= base_url('employee/attendance'); ?>", {
                  method: "POST",
                  headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest",
                  },
                  body: new URLSearchParams({
                    check_out: 1,
                    work_shift: "<?= esc($shift_details['shift_id'] ?? ''); ?>",
                    latitude: latitude,
                    longitude: longitude,
                    [csrfName]: csrfHash // Sertakan CSRF token
                  })
                })
                .then(response => response.json())
                .then(data => {
                  if (data.status === 'success') {
                    Swal.fire({
                      icon: 'success',
                      title: 'Sukses',
                      text: `Berhasil presensi keluar pada pukul ${new Date().toLocaleTimeString()}`,
                      confirmButtonText: 'Oke'
                    }).then(() => {
                      // Reload halaman untuk memperbarui status
                      location.reload();
                    });
                  } else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Gagal',
                      text: data.message,
                      confirmButtonText: 'Oke'
                    });
                    // Re-enable button
                    checkOutBtn.disabled = false;
                    checkOutBtn.classList.remove("btn-dark");
                    checkOutBtn.classList.add("btn-danger");
                  }
                })
                .catch(error => {
                  console.error('Error:', error);
                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat melakukan presensi keluar.',
                    confirmButtonText: 'Oke'
                  });
                  // Re-enable button
                  checkOutBtn.disabled = false;
                  checkOutBtn.classList.remove("btn-dark");
                  checkOutBtn.classList.add("btn-danger");
                });
            });
          }
        } else {
          // Sudah melakukan check-out, sesuaikan UI
          if (checkOutBtn) {
            checkOutBtn.disabled = true;
            checkOutBtn.classList.remove("btn-danger");
            checkOutBtn.classList.add("btn-dark");
          }
          if (checkInBtn) {
            checkInBtn.disabled = true;
            checkInBtn.classList.remove("btn-primary");
            checkInBtn.classList.add("btn-dark");
          }
          activateLocationBtn.disabled = true;
          locationStatus.textContent = "Sudah keluar.";
          locationStatus.classList.remove("text-muted");
          locationStatus.classList.add("text-secondary");
        }
      }
    }
  });
</script>