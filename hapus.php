<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_hapus = $koneksi->real_escape_string($_GET['id']);
    
    // Ambil nama file gambar (opsional jika ingin menghapus file fisik)
    $sql_file = "SELECT gambar_file FROM menu_makanan WHERE id_menu='$id_hapus'";
    $result_file = $koneksi->query($sql_file);
    
    // Query Hapus
    $sql_delete = "DELETE FROM menu_makanan WHERE id_menu='$id_hapus'";
    
    if ($koneksi->query($sql_delete) === TRUE) {
        // Logika opsional untuk menghapus file fisik
        if ($result_file->num_rows > 0) {
            $data_file = $result_file->fetch_assoc();
            $file_to_delete = "gambar/" . $data_file['gambar_file'];
            // if (file_exists($file_to_delete)) {
            //     unlink($file_to_delete); // Hapus komentar ini jika ingin menghapus file fisik
            // }
        }
        $message = "Menu berhasil dihapus.";
    } else {
        $message = "Error menghapus: " . $koneksi->error;
    }
} else {
    $message = "ID menu tidak valid.";
}

$koneksi->close();

// Redirect kembali ke halaman admin
header("Location: admin.php?message=" . urlencode(strip_tags($message)));
exit();
?>