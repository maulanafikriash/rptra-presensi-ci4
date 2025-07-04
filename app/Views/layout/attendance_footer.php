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

  <?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
    $('html, body').animate({
      scrollTop: 0
    }, 'fast');
  <?php endif; ?>
</script>
</body>

</html>