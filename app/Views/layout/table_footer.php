<?= $this->include('components/footer'); ?>

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<?= $this->include('components/logout_modal'); ?>


<!-- Bootstrap core JavaScript-->
<script src="<?= base_url('../assets/jquery/jquery.min.js'); ?>"></script>
<script src="<?= base_url('../assets/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<!-- Core plugin JavaScript-->
<script src="<?= base_url('../assets/jquery-easing/jquery.easing.min.js'); ?>"></script>
<!-- Custom scripts for all pages-->
<script src="<?= base_url('../js/sb-admin-2.js'); ?>"></script>
<script src="<?= base_url('../assets/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?= base_url('../assets/datatables/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="<?= base_url('../js/demo/datatables-demo.js'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-button');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                const deleteUrl = button.getAttribute('data-url');
                const entity = button.getAttribute('data-entity');

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Yakin ingin menghapus ${entity} ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.action = deleteUrl;
                        form.method = 'POST';
                        form.innerHTML = `<?= csrf_field(); ?>`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    });

    // Seleksi elemen flashdata
    const flashdataMessage = document.getElementById('flashdataMessage');

    // Cek jika elemen ada
    if (flashdataMessage) {
        // Atur timer untuk menghilangkan elemen setelah 3 detik
        setTimeout(() => {
            flashdataMessage.style.transition = 'opacity 0.5s';
            flashdataMessage.style.opacity = '0';
            setTimeout(() => {
                flashdataMessage.remove();
            }, 500);
        }, 3000);
    }
</script>

</body>

</html>