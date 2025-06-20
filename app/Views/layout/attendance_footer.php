<?= $this->include('components/footer'); ?>
<?= $this->include('components/logout_modal'); ?>

<!-- Modal edit attendance -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editAttendanceModal">Edit Presensi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= base_url('admin/master/employee/attendance/edit'); ?>" method="POST">
        <div class="modal-body">
          <input type="hidden" name="employee_id" value="<?= esc($employeeId); ?>">
          <input type="hidden" name="date" id="attendance_date" value=" ">

          <!-- Dropdown untuk memilih status presensi -->
          <div class="form-group">
            <label for="presence_status">Status Presensi</label>
            <select class="form-control" id="presence_status" name="presence_status">
              <option value="0">Tidak Hadir</option>
              <option value="1">Hadir</option>
              <option value="2">Izin</option>
              <option value="3">Sakit</option>
              <option value="4">Cuti</option>
              <option value="5">Libur</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal untuk Maps -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mapModalLabel">Lokasi Presensi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Map Container -->
        <div id="map" style="width: 100%; height: 400px;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal untuk Menambah/Edit Jadwal Kerja -->
<div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="scheduleForm" method="POST" action="<?= base_url('admin/master/employee/work_schedule/add'); ?>">
      <?= csrf_field() ?>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold">Tambah/Edit Jadwal Kerja<span id="modal_schedule_date_display" class="ml-2 text-muted"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="schedule_id" id="schedule_id" value="">
          <input type="hidden" name="employee_id" id="modal_employee_id" value="">
          <input type="hidden" name="department_id" id="modal_department_id" value="">
          <input type="hidden" name="schedule_date" id="modal_schedule_date" value="">

          <div class="form-group">
            <label for="modal_schedule_status">Status Jadwal</label>
            <select name="schedule_status" id="modal_schedule_status" class="form-control" required>
              <option value="">Pilih Status</option>
              <option value="NULL">Shift Kerja</option>
              <option value="4">Cuti</option>
              <option value="5">Libur</option>
            </select>
          </div>
          <div class="form-group" id="modal_shift_field" style="display: none;">
            <label for="modal_shift_id">Shift</label>
            <select name="shift_id" id="modal_shift_id" class="form-control">
              <option value="">Pilih Shift</option>
              <?php foreach ($shifts as $shift): ?>
                <option value="<?= esc($shift['shift_id']) ?>">
                  <?= date('H:i', strtotime(esc($shift['start_time']))) ?> - <?= date('H:i', strtotime(esc($shift['end_time']))) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url('../assets/jquery/jquery.min.js'); ?>"></script>
<script src="<?= base_url('../assets/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<!-- Core plugin JavaScript-->
<script src="<?= base_url('../assets/jquery-easing/jquery.easing.min.js'); ?>"></script>
<!-- Custom scripts for all pages-->
<script src="<?= base_url('../js/sb-admin-2.min.js'); ?>"></script>
<script src="<?= base_url('../js/flash-data-message.js'); ?>"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $('#editAttendanceModal').on('show.bs.modal', function(event) {
    let button = $(event.relatedTarget);
    let day = button.data('day');
    let month = '<?= $month; ?>';
    let year = '<?= $year; ?>';
    let modal = $(this);

    console.log('day:', day, 'month:', month, 'year:', year);

    modal.find('.modal-title').text('Edit Presensi - <?= $employee['employee_name']; ?> (' + day + '-' + month + '-' + year + ')');
    modal.find('#attendance_date').val(year + '-' + month + '-' + day);
  });

  // maps start
  let map;

  function showMap(lat, lng, label) {
    document.getElementById("mapModalLabel").textContent = "Lokasi Presensi " + label;

    if (map) {
      map.remove();
    }

    map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([lat, lng]).addTo(map)
      .bindPopup(`<b>${label}</b><br><i>Memuat alamat...</i>`)
      .openPopup();
    fetchAddress(lat, lng, marker, label);

    $('#mapModal').modal('show');

    setTimeout(() => {
      map.invalidateSize();
    }, 200);
  }

  async function fetchAddress(lat, lng, marker, originalLabel) {
    const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;

    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error('Gagal mendapatkan respon dari server.');
      }
      const data = await response.json();

      let popupContent;
      if (data && data.display_name) {
        popupContent = `<b>${originalLabel}</b><br>${data.display_name}`;
      } else {
        popupContent = `<b>${originalLabel}</b><br><small>Alamat tidak ditemukan.</small>`;
      }
      marker.setPopupContent(popupContent);

    } catch (error) {
      console.error("Error fetching address:", error);
      const errorContent = `<b>${originalLabel}</b><br><small class="text-danger">Gagal memuat alamat.</small>`;
      marker.setPopupContent(errorContent);
    }
  }

  function showAlert(message) {
    Swal.fire({
      icon: 'info',
      title: 'Info',
      text: message,
      confirmButtonText: 'OK'
    });
  }

  // schedule
  let bulanIndonesia = {
    1: 'Januari',
    2: 'Februari',
    3: 'Maret',
    4: 'April',
    5: 'Mei',
    6: 'Juni',
    7: 'Juli',
    8: 'Agustus',
    9: 'September',
    10: 'Oktober',
    11: 'November',
    12: 'Desember'
  };

  // memformat tanggal ke "dd Month yyyy"
  function formatTanggal(tanggal) {
    let parts = tanggal.split('-');
    let tahun = parts[0];
    let bulan = parseInt(parts[1], 10);
    let hari = parseInt(parts[2], 10);
    return hari + ' ' + bulanIndonesia[bulan] + ' ' + tahun;
  }

  $(document).ready(function() {
    // Handle tombol tambah jadwal
    $('.add-schedule').on('click', function(e) {
      e.preventDefault();
      let date = $(this).data('date');
      let employeeId = $(this).data('employee-id');
      let departmentId = $(this).data('department-id');

      // Reset form
      $('#scheduleForm')[0].reset();
      $('#schedule_id').val('');
      $('#modal_employee_id').val(employeeId);
      $('#modal_department_id').val(departmentId);
      $('#modal_schedule_date').val(date);
      $('#modal_shift_field').hide();
      $('#modal_shift_id').prop('required', false);

      $('#modal_schedule_date_display').text(formatTanggal(date));

      // Update form action untuk tambah jadwal
      $('#scheduleForm').attr('action', '<?= base_url('admin/master/employee/work_schedule/add'); ?>');
      $('#scheduleModal').modal('show');
    });

    // Handle tombol edit jadwal
    $('.edit-schedule').on('click', function(e) {
      e.preventDefault();
      let scheduleId = $(this).data('schedule-id');
      let date = $(this).data('date');
      let status = $(this).data('status');
      let shiftId = $(this).data('shift-id');
      let employeeId = '<?= esc($employee['employee_id']); ?>';
      let departmentId = '<?= esc($employee['department_id']); ?>';

      // Reset form
      $('#scheduleForm')[0].reset();
      $('#schedule_id').val(scheduleId);
      $('#modal_employee_id').val(employeeId);
      $('#modal_department_id').val(departmentId);
      $('#modal_schedule_date').val(date);

      // tampilan tanggal di modal edit
      $('#modal_schedule_date_display').text(formatTanggal(date));

      if (status === null || status === 'NULL') {
        $('#modal_schedule_status').val('NULL');
        $('#modal_shift_field').show();
        $('#modal_shift_id').prop('required', true);
      } else {
        $('#modal_schedule_status').val(status);
        $('#modal_shift_field').hide();
        $('#modal_shift_id').prop('required', false);
      }

      if (shiftId) {
        $('#modal_shift_id').val(shiftId);
      } else {
        $('#modal_shift_id').val('');
      }

      $('#scheduleForm').attr('action', '<?= base_url('admin/master/employee/work_schedule/edit'); ?>/' + scheduleId);

      $('#scheduleModal').modal('show');
    });

    // Toggle shift field berdasarkan status
    $('#modal_schedule_status').on('change', function() {
      let shiftField = $('#modal_shift_field');
      if ($(this).val() === 'NULL') {
        shiftField.show();
        $('#modal_shift_id').prop('required', true);
      } else {
        shiftField.hide();
        $('#modal_shift_id').prop('required', false);
      }
    });

    $('#scheduleModal').on('shown.bs.modal', function() {
      $('#modal_schedule_status').trigger('change');
    });

    // Submit form via AJAX
    $('#scheduleForm').on('submit', function(e) {
      e.preventDefault();
      let form = $(this);
      let actionUrl = form.attr('action');
      let formData = form.serialize();

      $.ajax({
        url: actionUrl,
        type: 'POST',
        data: formData,
        success: function(response) {
          if (response.status === 'success') {
            location.reload();
          } else {
            $('#flashdataMessage').html(`
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ${response.message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            `);
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', status, error);
          alert('Terjadi kesalahan: ' + xhr.responseText);
        }
      });
    });
    <?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
      $('html, body').animate({
        scrollTop: 0
      }, 'fast');
    <?php endif; ?>
  });
</script>
</body>

</html>