<?= $this->include('components/footer'); ?>
<?= $this->include('components/logout_modal'); ?>

<div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="scheduleForm" method="POST">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="scheduleModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="schedule_id" id="schedule_id">
                    <input type="hidden" name="employee_id" id="modal_employee_id">
                    <input type="hidden" name="schedule_date" id="modal_schedule_date">

                    <div class="form-group">
                        <label for="modal_schedule_status">Status Jadwal</label>
                        <select name="schedule_status" id="modal_schedule_status" class="form-control" required>
                            <option value="">Pilih Status</option>
                            <option value="shift">Shift Kerja</option>
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

<script src="<?= base_url('../assets/jquery/jquery.min.js'); ?>"></script>
<script src="<?= base_url('../assets/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?= base_url('../assets/jquery-easing/jquery.easing.min.js'); ?>"></script>
<script src="<?= base_url('../js/sb-admin-2.min.js'); ?>"></script>
<script src="<?= base_url('../js/flash-data-message.js'); ?>"></script>

<script>
    $(document).ready(function() {
        const scheduleModal = $('#scheduleModal');
        const scheduleForm = $('#scheduleForm');
        const shiftField = $('#modal_shift_field');
        const shiftSelect = $('#modal_shift_id');

        // Fungsi untuk format tanggal
        const bulanIndonesia = {
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

        function formatTanggal(tanggal) {
            const parts = tanggal.split('-');
            return `${parseInt(parts[2], 10)} ${bulanIndonesia[parseInt(parts[1], 10)]} ${parts[0]}`;
        }

        $('#modal_schedule_status').on('change', function() {
            if ($(this).val() === 'shift') {
                shiftField.slideDown();
                shiftSelect.prop('required', true);
            } else {
                shiftField.slideUp();
                shiftSelect.prop('required', false).val('');
            }
        });

        // Handler untuk tombol TAMBAH jadwal
        $('.add-schedule').on('click', function(e) {
            e.preventDefault();
            const button = $(this);
            const date = button.data('date');
            const employeeId = button.data('employee-id');

            scheduleForm[0].reset();
            scheduleForm.attr('action', '<?= base_url('admin/master/employee/work_schedule/add') ?>');

            $('#scheduleModalTitle').text(`Tambah Jadwal: ${formatTanggal(date)}`);
            $('#schedule_id').val('');
            $('#modal_schedule_date').val(date);
            $('#modal_employee_id').val(employeeId);

            $('#modal_schedule_status').val('shift').trigger('change');
            scheduleModal.modal('show');
        });

        // Handler untuk tombol EDIT jadwal
        $('.edit-schedule').on('click', function(e) {
            e.preventDefault();
            const data = $(this).data();

            scheduleForm[0].reset();
            scheduleForm.attr('action', `<?= base_url('admin/master/employee/work_schedule/edit') ?>/${data.scheduleId}`);

            $('#scheduleModalTitle').text(`Edit Jadwal: ${formatTanggal(data.date)}`);
            $('#schedule_id').val(data.scheduleId);
            $('#modal_employee_id').val('<?= esc($employee['employee_id']) ?>'); // Set ID pegawai saat edit
            $('#modal_schedule_date').val(data.date);

            if (data.status == 4 || data.status == 5) {
                $('#modal_schedule_status').val(data.status).trigger('change');
            } else {
                $('#modal_schedule_status').val('shift').trigger('change');
                shiftSelect.val(data.shiftId);
            }

            scheduleModal.modal('show');
        });

        scheduleForm.on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitButton = form.find('button[type="submit"]');
            submitButton.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        scheduleModal.modal('hide');
                        window.location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan pada server. Lihat console untuk detail.');
                    console.error('AJAX Error:', xhr.responseText);
                },
                complete: function() {
                    submitButton.prop('disabled', false).text('Simpan');
                }
            });
        });
    });
</script>