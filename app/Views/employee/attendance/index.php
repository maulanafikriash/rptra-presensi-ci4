<div class="container-fluid">

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h3 class="mb-0 text-gray-700 font-weight-bold"><?= esc($title); ?></h3>
  </div>

  <div class="row">
    <div class="col">
      <div class="card shadow mb-4" style="min-height: 543px">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Isi Kehadiran Anda!</h6>
        </div>
        <div class="card-body">

          <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-dismissible fade show" role="alert">
              <?= session()->getFlashdata('message'); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <form id="attendance-form" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="location_remark" id="location_remark">
            <input type="hidden" name="work_shift" value="<?= $has_shift ? esc($shift_details['shift_id']) : ''; ?>">

            <div class="row">
              <div class="col-lg-5">
                <label for="work_shift" class="col-form-label">Shift Kerja</label>
                <?php if ($has_shift && $shift_details): ?>
                  <input class="form-control" type="text"
                    value="<?= $is_flexible_shift ? 'Shift Fleksibel (00:00 - 23:59)' : 'Shift ' . esc($shift_details['shift_id']) . ' = ' . esc($shift_start_time) . ' - ' . esc($shift_end_time) . ''; ?>"
                    readonly>
                <?php else: ?>
                  <?php
                  $statusMap = [
                    5 => ['icon' => 'fa-calendar-day', 'color' => 'text-primary', 'text' => 'Hari ini Libur'],
                    4 => ['icon' => 'fa-calendar-check', 'color' => 'text-dark', 'text' => 'Hari ini Cuti'],
                    2 => ['icon' => 'fa-user-clock', 'color' => 'text-warning', 'text' => 'Hari ini Izin'],
                    3 => ['icon' => 'fa-medkit', 'color' => 'text-warning', 'text' => 'Hari ini Sakit'],
                    'default' => ['icon' => 'fa-calendar-alt', 'color' => 'text-secondary', 'text' => 'Tidak Ada Jadwal']
                  ];
                  $currentStatus = $statusMap[$presence_status] ?? $statusMap['default'];
                  ?>
                  <div class="d-flex align-items-center">
                    <i class="fas <?= $currentStatus['icon'] ?> fa-2x <?= $currentStatus['color'] ?> mr-2"></i>
                    <span class="<?= $currentStatus['color'] ?> font-weight-bold"><?= $currentStatus['text'] ?></span>
                  </div>
                <?php endif; ?>
              </div>

              <div class="col-lg-5 offset-lg-1 location-container">
                <?php if ($can_check_in || $can_check_out): ?>
                  <label for="location" class="col-form-label">Aktifkan Lokasi Saat Ini</label>
                  <button type="button" class="btn btn-primary btn-lg btn-block shadow-sm" id="activate-location-btn"
                    style="display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fas fa-map-marker-alt mr-2"></i> Aktifkan Lokasi
                  </button>
                  <p id="location-status" class="mt-2 text-muted text-center" style="font-size: 14px;">Lokasi belum
                    diaktifkan</p>
                <?php endif; ?>
              </div>
            </div>

            <hr class="mb-5">
            <div class="row justify-content-center mb-3">
              <div class="col-lg-8 text-center">
                <div class="d-flex justify-content-center align-items-center">

                  <div class="text-center">
                    <?php
                    $presenceStatuses = [
                      1 => ['label' => 'Hadir', 'class' => 'btn-success', 'icon' => 'fa-check', 'text_class' => 'text-success'],
                      0 => ['label' => 'Tidak Hadir', 'class' => 'btn-danger', 'icon' => 'fa-times', 'text_class' => 'text-danger'],
                      2 => ['label' => 'Izin', 'class' => 'btn-warning', 'icon' => 'fa-calendar-day', 'text_class' => 'text-warning'],
                      3 => ['label' => 'Sakit', 'class' => 'btn-warning', 'icon' => 'fa-medkit', 'text_class' => 'text-warning'],
                      4 => ['label' => 'Cuti', 'class' => 'btn-dark', 'icon' => 'fa-calendar-check', 'text_class' => 'text-dark'],
                      5 => ['label' => 'Libur', 'class' => 'btn-primary', 'icon' => 'fa-calendar-times', 'text_class' => 'text-primary'],
                      'default' => ['label' => 'Tidak Hadir', 'class' => 'btn-danger', 'icon' => 'fa-times', 'text_class' => 'text-danger']
                    ];

                    $status = $presenceStatuses[$presence_status] ?? $presenceStatuses['default'];
                    if ($already_checked_out) {
                      $status = ['label' => 'Sudah Keluar', 'class' => 'btn-secondary', 'icon' => 'fa-check-circle', 'text_class' => 'text-secondary'];
                    }
                    ?>
                    <button class="btn <?= esc($status['class']); ?> btn-circle" style="width: 100px; height: 100px;"
                      disabled>
                      <i class="fas <?= esc($status['icon']); ?> fa-2x"></i>
                    </button>
                    <p class="font-weight-bold <?= esc($status['text_class']); ?> pt-2"><?= esc($status['label']); ?>
                    </p>
                  </div>

                  <?php if ($can_check_in || $can_check_out || $already_checked_in && !$already_checked_out || $shift_status === 'belum mulai'): ?>
                    <div class="mx-4"></div>
                  <?php endif; ?>

                  <div class="text-center">
                    <?php if ($can_check_in): ?>
                      <button type="button" name="check_in" id="check-in-btn" class="btn btn-primary btn-circle"
                        style="width: 100px; height: 100px;" disabled>
                        <i class="fas fa-fw fa-sign-in-alt fa-2x"></i>
                      </button>
                      <p class="font-weight-bold text-primary pt-2">Masuk!</p>
                    <?php elseif ($can_check_out): ?>
                      <button type="button" name="check_out" id="check-out-btn" class="btn btn-danger btn-circle"
                        style="width: 100px; height: 100px;" disabled>
                        <i class="fas fa-fw fa-sign-out-alt fa-2x"></i>
                      </button>
                      <p class="font-weight-bold text-danger pt-2">Keluar</p>

                    <?php elseif ($shift_status === 'belum mulai'): ?>
                      <button type="button" class="btn btn-dark btn-circle" style="width: 100px; height: 100px;" disabled>
                        <i class="fas fa-fw fa-sign-in-alt fa-2x"></i>
                      </button>
                      <p class="font-weight-bold text-dark pt-2">Belum Mulai</p>

                    <?php elseif ($already_checked_in && !$already_checked_out): ?>
                      <button type="button" class="btn btn-dark btn-circle" style="width: 100px; height: 100px;" disabled>
                        <i class="fas fa-fw fa-clock fa-2x"></i>
                      </button>
                      <p class="text-dark pt-2" style="font-size: small;">Belum Waktu Pulang</p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    // --- Variabel dari PHP ---
    const isFlexibleShift = <?= json_encode($is_flexible_shift); ?>;
    const canCheckIn = <?= json_encode($can_check_in); ?>;
    const canCheckOut = <?= json_encode($can_check_out); ?>;
    const csrfName = "<?= csrf_token() ?>";
    const csrfHash = "<?= csrf_hash() ?>";
    const shiftStatus = "<?= esc($shift_status, 'js'); ?>";
    const alreadyCheckedIn = <?= json_encode($already_checked_in); ?>;

    // --- Elemen DOM ---
    const activateLocationBtn = document.getElementById("activate-location-btn");
    const checkInBtn = document.getElementById("check-in-btn");
    const checkOutBtn = document.getElementById("check-out-btn");
    const locationStatusEl = document.getElementById("location-status");
    const latitudeInput = document.getElementById("latitude");
    const longitudeInput = document.getElementById("longitude");
    const locationRemarkInput = document.getElementById("location_remark");
    const attendanceForm = document.getElementById('attendance-form');

    if (shiftStatus === 'sudah selesai' && !alreadyCheckedIn) {
      Swal.fire({
        icon: 'warning',
        title: 'Waktu Shift Sudah Berakhir',
        text: 'Anda terlambat melakukan presensi.',
        confirmButtonText: 'Oke'
      });
    }

    /**
     * mengambil alamat dari koordinat (Reverse Geocoding).
     */
    async function getAddressFromCoordinates(lat, lon) {
      try {
        const formData = new FormData();
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        formData.append('lat', lat);
        formData.append('lon', lon);

        const response = await fetch("<?= base_url('employee/get_location'); ?>", {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          },
          body: formData,
          cache: 'no-store'
        });

        if (!response.ok) {
          // Jika server merespon dengan status error (spt 400, 500)
          const errorData = await response.json();
          throw new Error(errorData.message || 'Network response was not ok');
        }

        const data = await response.json();

        if (data.status === 'success') {
          return data.address;
        } else {
          // Jika ada error yang dikirim dari backend (status 200 tapi ada 'message' error)
          throw new Error(data.message || 'Gagal mengambil alamat dari server.');
        }

      } catch (error) {
        console.error('Error fetching address from backend:', error);
        // Tampilkan pesan error yang lebih informatif ke console atau UI jika perlu
        return Gagal mengambil nama alamat: ${ error.message };
      }
    }

    /**
     * mengambil lokasi terkini dan alamat, lalu mengaktifkan tombol presensi.
     */
    function activateLocationAndGetAddress() {
      if (!navigator.geolocation) {
        Swal.fire('Error', 'Geolocation tidak didukung oleh browser ini.', 'error');
        return;
      }

      locationStatusEl.textContent = "Mencari lokasi Anda...";
      locationStatusEl.className = "mt-2 text-primary text-center";
      activateLocationBtn.disabled = true; // Nonaktifkan tombol selama proses

      navigator.geolocation.getCurrentPosition(
        async (position) => {
          const lat = position.coords.latitude;
          const lon = position.coords.longitude;

          latitudeInput.value = lat;
          longitudeInput.value = lon;

          locationStatusEl.textContent = "Lokasi didapat. Mengambil nama alamat...";

          // Ambil nama alamat
          const address = await getAddressFromCoordinates(lat, lon);
          locationRemarkInput.value = address;

          // Tampilkan alamat ke pengguna
          locationStatusEl.innerHTML = <span class="font-weight-bold text-success">${address}</span>;

          // Aktifkan tombol yang sesuai
          if (canCheckIn && checkInBtn) checkInBtn.disabled = false;
          if (canCheckOut && checkOutBtn) checkOutBtn.disabled = false;

          // Ganti ikon & teks tombol aktivasi menjadi tanda sukses
          activateLocationBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Lokasi Diaktifkan';
          activateLocationBtn.classList.remove('btn-primary');
          activateLocationBtn.classList.add('btn-success');
        },
        (error) => {
          let errorMessage = 'Gagal mendapatkan lokasi. Harap izinkan akses lokasi dan coba lagi.';
          switch (error.code) {
            case error.PERMISSION_DENIED:
              errorMessage = "Akses lokasi ditolak. Silakan izinkan dari pengaturan browser Anda.";
              break;
            case error.POSITION_UNAVAILABLE:
              errorMessage = "Informasi lokasi tidak tersedia.";
              break;
            case error.TIMEOUT:
              errorMessage = "Waktu permintaan lokasi habis.";
              break;
          }
          Swal.fire('Gagal!', errorMessage, 'error');
          locationStatusEl.textContent = "Gagal mendapatkan lokasi.";
          locationStatusEl.className = "mt-2 text-danger text-center";
          activateLocationBtn.disabled = false;
        }, {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 0 // ambil lokasi baru
      }
      );
    }

    /**
     * mengirim data presensi ke server.
     */
    function sendAttendanceRequest(action) {
      if (!latitudeInput.value || !longitudeInput.value) {
        Swal.fire('Peringatan', 'Harap aktifkan lokasi terlebih dahulu.', 'warning');
        return;
      }

      const formData = new FormData(attendanceForm);
      formData.append(action, '1'); // Menambah 'check_in' atau 'check_out'

      fetch("<?= base_url('employee/attendance'); ?>", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest"
        },
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            const actionText = (action === 'check_in') ? 'masuk' : 'keluar';
            const timeString = new Date().toLocaleTimeString('id-ID', {
              hour: '2-digit',
              minute: '2-digit'
            });
            const successMessage = Berhasil presensi ${ actionText } pada pukul ${ timeString }.;

            Swal.fire('Berhasil!', successMessage, 'success').then(() => location.reload());
          } else {
            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
        });
    }

    // --- Event Listeners ---
    if (activateLocationBtn) {
      activateLocationBtn.addEventListener("click", activateLocationAndGetAddress);
    }

    if (checkInBtn) {
      checkInBtn.addEventListener("click", () => sendAttendanceRequest('check_in'));
    }

    if (checkOutBtn) {
      checkOutBtn.addEventListener("click", function () {
        if (isFlexibleShift) {
          Swal.fire({
            title: 'Konfirmasi Keluar',
            text: "Saat ini kamu sedang tugas diluar dengan waktu shift fleksibel, pastikan waktu pulang sudah tiba.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Presensi Keluar',
            cancelButtonText: 'Batal'
          }).then((result) => {
            if (result.isConfirmed) {
              sendAttendanceRequest('check_out');
            }
          });
        } else {
          sendAttendanceRequest('check_out');
        }
      });
    }
  });
</script>