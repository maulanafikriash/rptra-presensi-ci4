<?= $this->include('components/footer'); ?>
<?= $this->include('components/logout_modal'); ?>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url('../assets/jquery/jquery.min.js'); ?>"></script>
<script src="<?= base_url('../assets/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<!-- Core plugin JavaScript-->
<script src="<?= base_url('../assets/jquery-easing/jquery.easing.min.js'); ?>"></script>
<!-- Custom scripts for all pages-->
<script src="<?= base_url('../js/sb-admin-2.min.js'); ?>"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
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