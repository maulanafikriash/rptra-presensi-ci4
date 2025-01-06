// Seleksi elemen flashdata
const flashdataMessage = document.getElementById("flashdataMessage");

// Cek jika elemen ada
if (flashdataMessage) {
  // Atur timer untuk menghilangkan elemen setelah 5 detik
  setTimeout(() => {
    flashdataMessage.style.transition = "opacity 0.5s";
    flashdataMessage.style.opacity = "0";
    setTimeout(() => {
      flashdataMessage.remove();
    }, 500);
  }, 5000);
}
