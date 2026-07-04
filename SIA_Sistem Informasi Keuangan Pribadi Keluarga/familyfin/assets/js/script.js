/* File: assets/js/script.js */

// Fungsi Konfirmasi Hapus Data
// Digunakan pada tombol "Sampah/Delete"
function konfirmasiHapus(event) {
  // Tampilkan popup konfirmasi bawaan browser
  let yakin = confirm("Apakah Anda yakin ingin menghapus data ini secara permanen?");

  // Jika user klik 'Cancel' (Batal), cegah link bekerja
  if (!yakin) {
    event.preventDefault();
  }
}

// Log untuk memastikan script terpanggil (bisa dilihat di Console browser)
console.log("FamilyFin Script Loaded Successfully.");
