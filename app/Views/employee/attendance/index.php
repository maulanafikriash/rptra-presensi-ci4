<div class="container-fluid">
  <div class="row">
    <div class="col">
      <div class="card shadow mb-4" style="min-height: 543px">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary"><?= esc($title); ?></h6>
        </div>
        <div class="card-body">

          <?php if (session()->getFlashdata('message')) : ?>
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
            <div class="row">
              <div class="col-lg-5">
                <label for="work_shift_dropdown" class="col-form-label">Shift Kerja</label>

                <?php if ($can_check_in) : ?>
                  <select class="form-control" id="work_shift_dropdown" name="work_shift" required>
                    <option value="" disabled <?= !$has_shift ? 'selected' : ''; ?>>-- Pilih Shift Kerja --</option>

                    <?php foreach ($all_shifts as $shift) : ?>
                      <?php
                      $shift_text = '';
                      if ($shift['start_time'] == '05:00:00' && $shift['end_time'] == '22:00:00') {
                        $shift_text = 'Shift Fleksibel (Tugas Luar)';
                      } else {
                        $shift_text = 'Shift ' . esc($shift['shift_id']) . ' (' . date('H:i', strtotime($shift['start_time'])) . ' - ' . date('H:i', strtotime($shift['end_time'])) . ')';
                      }

                      // menentukan shift yang terpilih secara default
                      $isSelected = ($has_shift && isset($schedule_shift['shift_id']) && $schedule_shift['shift_id'] == $shift['shift_id']) ? 'selected' : '';
                      ?>
                      <option value="<?= esc($shift['shift_id']); ?>"
                        data-start="<?= esc($shift['start_time']); ?>"
                        data-end="<?= esc($shift['end_time']); ?>"
                        <?= $isSelected; ?>>
                        <?= $shift_text; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <?php if (!$has_shift && $presence_status === null) : ?>
                    <small class="form-text text-muted">Status jadwal awal: <span class="font-weight-bold">Tidak Ada Jadwal</span></small>
                  <?php endif; ?>
                <?php else : ?>
                  <?php if ($has_shift && $shift_details) : ?>
                    <input class="form-control" type="text" value="<?= $is_flexible_shift ? 'Shift Fleksibel (05:00 - 22:00)' : 'Shift ' . esc($shift_details['shift_id']) . ' = ' . esc($shift_start_time) . ' - ' . esc($shift_end_time) . ''; ?>" readonly>
                  <?php else : ?>
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
                <?php endif; ?>
              </div>

              <div class="col-lg-5 offset-lg-1 location-container">

                <?php
                if (isset($attendance_message) && $attendance_message) :
                ?>
                  <div class="card bg-light text-center p-3 h-100 shadow-sm">
                    <div class="d-flex flex-column justify-content-center align-items-center">

                      <i class="fas <?= esc($attendance_message['icon']) ?> <?= esc($attendance_message['color']) ?> fa-2x mb-3"></i>

                      <p class="mb-0 text-gray-800" style="font-size: 15px;"><?= esc($attendance_message['text']); ?></p>

                    </div>
                  </div>

                <?php elseif ($can_check_in || $can_check_out) : ?>
                  <label for="location" class="col-form-label">Aktifkan Lokasi Saat Ini</label>
                  <button type="button" class="btn btn-primary btn-lg btn-block shadow-sm" id="activate-location-btn" style="display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fas fa-map-marker-alt mr-2"></i> Aktifkan Lokasi
                  </button>
                  <p id="location-status" class="mt-2 text-muted text-center" style="font-size: 14px;">Lokasi belum diaktifkan</p>
                <?php endif; ?>
              </div>
            </div>

            <hr class="mb-5">
            <div class="row justify-content-center mb-3">
              <div class="col-lg-8 text-center">
                <div id="status-flex-container" class="d-flex justify-content-center align-items-center">

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
                    <button id="main-status-btn" class="btn <?= esc($status['class']); ?> btn-circle" style="width: 100px; height: 100px;" disabled>
                      <i id="main-status-icon" class="fas <?= esc($status['icon']); ?> fa-2x"></i>
                    </button>
                    <p id="main-status-text" class="font-weight-bold <?= esc($status['text_class']); ?> pt-2"><?= esc($status['label']); ?></p>
                  </div>

                  <?php if ($can_check_in || $can_check_out || $already_checked_in && !$already_checked_out || $shift_status === 'belum mulai') : ?>
                    <div class="mx-4"></div>
                  <?php endif; ?>

                  <div class="text-center" id="attendance-action-container">
                    <?php if ($can_check_in) : ?>
                      <div id="check-in-wrapper">
                        <button type="button" name="check_in" id="check-in-btn" class="btn btn-primary btn-circle" style="width: 100px; height: 100px;" disabled>
                          <i class="fas fa-fw fa-sign-in-alt fa-2x"></i>
                        </button>
                        <p id="check-in-text" class="font-weight-bold text-primary pt-2">Masuk!</p>
                      </div>
                    <?php elseif ($can_check_out) : ?>
                      <button type="button" name="check_out" id="check-out-btn" class="btn btn-danger btn-circle" style="width: 100px; height: 100px;" disabled>
                        <i class="fas fa-fw fa-sign-out-alt fa-2x"></i>
                      </button>
                      <p class="font-weight-bold text-danger pt-2">Keluar</p>
                    <?php elseif ($already_checked_in && !$already_checked_out) : ?>
                      <button type="button" class="btn btn-dark btn-circle" style="width: 100px; height: 100px;" disabled>
                        <i class="fas fa-fw fa-clock fa-2x"></i>
                      </button>
                      <p class="text-dark pt-2 font-weight-bold" style="font-size: small;">Belum Waktu Pulang</p>
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
  document.addEventListener("DOMContentLoaded", function() {
    const isFlexibleShift = <?= json_encode($is_flexible_shift); ?>;
    const canCheckIn = <?= json_encode($can_check_in); ?>;
    const canCheckOut = <?= json_encode($can_check_out); ?>;
    const alreadyCheckedIn = <?= json_encode($already_checked_in); ?>;

    // --- Elemen DOM ---
    const activateLocationBtn = document.getElementById("activate-location-btn");
    const checkInBtn = document.getElementById("check-in-btn");
    const checkInWrapper = document.getElementById('check-in-wrapper');
    const checkOutBtn = document.getElementById("check-out-btn");
    const locationStatusEl = document.getElementById("location-status");
    const latitudeInput = document.getElementById("latitude");
    const longitudeInput = document.getElementById("longitude");
    const locationRemarkInput = document.getElementById("location_remark");
    const attendanceForm = document.getElementById('attendance-form');
    const workShiftDropdown = document.getElementById('work_shift_dropdown');
    // Elemen Status Utama
    const mainStatusBtn = document.getElementById('main-status-btn');
    const mainStatusIcon = document.getElementById('main-status-icon');
    const mainStatusText = document.getElementById('main-status-text');
    const statusFlexContainer = document.getElementById('status-flex-container');
    const originalFlexClass = statusFlexContainer ? statusFlexContainer.className : '';

    let isLocationActive = false;
    let originalStatusState = {};

    // Menyimpan status awal dari elemen status utama jika ada
    if (mainStatusBtn) {
      originalStatusState = {
        btnClass: mainStatusBtn.className,
        iconClass: mainStatusIcon.className,
        text: mainStatusText.textContent,
        textClass: mainStatusText.className
      };
    }

    /**
     * Mengembalikan status utama ke kondisi semula (misal: "Tidak Hadir").
     */
    function restoreMainStatus() {
      if (!mainStatusBtn) return;
      mainStatusBtn.className = originalStatusState.btnClass;
      mainStatusIcon.className = originalStatusState.iconClass;
      mainStatusText.textContent = originalStatusState.text;
      mainStatusText.className = originalStatusState.textClass;
      if (statusFlexContainer) statusFlexContainer.className = originalFlexClass;
    }

    /**
     * Mengubah status utama untuk menampilkan pesan kustom.
     */
    function setCustomMainStatus(text, icon, colorClass = 'secondary') {
      if (!mainStatusBtn) return;
      mainStatusBtn.className = `btn btn-${colorClass} btn-circle`;
      mainStatusIcon.className = `fas ${icon} fa-2x`;
      mainStatusText.textContent = text;
      mainStatusText.className = `font-weight-bold text-${colorClass} pt-2`;
    }

    /**
     * Memeriksa shift, lalu mengubah tampilan status utama dan tombol aksi.
     */
    function validateSelectedShift() {
      // Hanya jalankan jika elemen utama ada dan pegawai bisa check-in
      if (!workShiftDropdown || !canCheckIn) return;

      const selectedOption = workShiftDropdown.options[workShiftDropdown.selectedIndex];
      const shiftValue = selectedOption.value;

      // Jika tidak ada shift dipilih, kembali ke state awal
      if (!shiftValue) {
        restoreMainStatus();
        if (checkInWrapper) checkInWrapper.style.display = 'block';
        if (checkInBtn) checkInBtn.disabled = true;
        return;
      }

      const shiftStartTimeString = selectedOption.getAttribute('data-start');
      const shiftEndTimeString = selectedOption.getAttribute('data-end');

      const now = new Date();
      const startShiftTime = new Date();
      const [startHours, startMinutes] = shiftStartTimeString.split(':');
      startShiftTime.setHours(startHours, startMinutes, 0, 0);

      // Waktu paling awal bisa presensi (1 jam sebelum shift mulai)
      const earlyCheckInTime = new Date(startShiftTime.getTime() - 60 * 60 * 1000);

      // case 1: Waktu shift belum mulai
      if (now < earlyCheckInTime) {
        setCustomMainStatus('Shift Belum Mulai', 'fa-clock');
        if (checkInWrapper) checkInWrapper.style.display = 'none';
        if (statusFlexContainer) statusFlexContainer.className = 'd-flex flex-column justify-content-center align-items-center';
        return;
      }

      const endShiftTime = new Date();
      const [endHours, endMinutes] = shiftEndTimeString.split(':');
      endShiftTime.setHours(endHours, endMinutes, 0, 0);

      // case 2: Waktu shift sudah berakhir
      if (now > endShiftTime) {
        setCustomMainStatus('Shift Telah Berakhir', 'fa-calendar-times');

        if (checkInWrapper) checkInWrapper.style.display = 'none';
        if (statusFlexContainer) statusFlexContainer.className = 'd-flex flex-column justify-content-center align-items-center';
      } else {
        // case 3: Waktu presensi valid
        restoreMainStatus();
        if (checkInWrapper) checkInWrapper.style.display = 'block';
        if (checkInBtn) checkInBtn.disabled = !isLocationActive; // Atur disabled berdasarkan lokasi
      }
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
          const errorData = await response.json();
          throw new Error(errorData.message || 'Network response was not ok');
        }
        const data = await response.json();
        if (data.status === 'success') {
          return data.address;
        } else {
          throw new Error(data.message || 'Gagal mengambil alamat dari server.');
        }
      } catch (error) {
        console.error('Error fetching address:', error);
        return `Gagal mengambil nama alamat: ${error.message}`;
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
      activateLocationBtn.disabled = true;

      navigator.geolocation.getCurrentPosition(
        async (position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            latitudeInput.value = lat;
            longitudeInput.value = lon;
            locationStatusEl.textContent = "Lokasi didapat. Mengambil nama alamat...";

            const address = await getAddressFromCoordinates(lat, lon);
            locationRemarkInput.value = address;
            locationStatusEl.innerHTML = `<span class="font-weight-bold text-success">${address}</span>`;

            isLocationActive = true;

            if (canCheckOut && checkOutBtn) checkOutBtn.disabled = false;

            // Cek ulang validasi shift untuk mengaktifkan tombol masuk
            validateSelectedShift();

            activateLocationBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Lokasi Diaktifkan';
            activateLocationBtn.classList.replace('btn-primary', 'btn-success');
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
            isLocationActive = false;
          }, {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
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

      // Tambahan validasi untuk check-in
      if (action === 'check_in' && (!workShiftDropdown || !workShiftDropdown.value)) {
        Swal.fire('Peringatan', 'Harap pilih shift kerja terlebih dahulu.', 'warning');
        return;
      }

      const formData = new FormData(attendanceForm);
      formData.append(action, '1');

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
            Swal.fire('Berhasil!', `Berhasil presensi ${actionText}.`, 'success').then(() => location.reload());
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

    if (workShiftDropdown) {
      workShiftDropdown.addEventListener('change', validateSelectedShift);
    }

    if (checkInBtn) {
      checkInBtn.addEventListener("click", () => sendAttendanceRequest('check_in'));
    }

    if (checkOutBtn) {
      checkOutBtn.addEventListener("click", function() {
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
    validateSelectedShift();
  });
</script>