<?php 
// Sertakan header dan koneksi
include 'header.php'; 

// Jika mode admin sedang dalam aksi edit atau tambah, jangan tampilkan container utama dulu
$show_form = isset($_GET['action']) && ($_GET['action'] == 'tambah' || $_GET['action'] == 'edit');
?>
<div class="container">
    <h2>Manajemen Menu Makanan (CRUD)</h2>
    <a href="admin.php?action=tambah" class="btn btn-primary">➕ Tambah Menu Baru</a>
    <hr>

    <?php
    // --- Bagian 1: Logika CRUD (Tambah/Edit) ---
    $id_edit = $nama = $deskripsi = $harga = $gambar_lama = '';
    $mode = 'tambah';
    $message = '';

    // Ambil data jika mode EDIT
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id_edit = $koneksi->real_escape_string($_GET['id']);
        $sql_edit = "SELECT * FROM menu_makanan WHERE id_menu='$id_edit'";
        $result_edit = $koneksi->query($sql_edit);
        if ($result_edit->num_rows == 1) {
            $data = $result_edit->fetch_assoc();
            $nama = $data['nama_menu'];
            $deskripsi = $data['deskripsi'];
            $harga = $data['harga'];
            $gambar_lama = $data['gambar_file'];
            $mode = 'edit';
        } 
    }

    // Tangani Form Submission (Tambah/Edit)
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_menu'])) {
        $mode_post = $_POST['mode'];
        $id_post = ($mode_post == 'edit' && isset($_POST['id_menu'])) ? $koneksi->real_escape_string($_POST['id_menu']) : null;
        $nama_post = $koneksi->real_escape_string($_POST['nama_menu']);
        $deskripsi_post = $koneksi->real_escape_string($_POST['deskripsi']);
        $harga_post = $koneksi->real_escape_string($_POST['harga']);
        $gambar_lama_post = $koneksi->real_escape_string($_POST['gambar_lama'] ?? ''); // Handle jika tidak ada gambar lama

        $gambar_baru = $gambar_lama_post; // Default menggunakan gambar lama

        // Penanganan Upload File
        if (isset($_FILES['gambar_baru']) && $_FILES['gambar_baru']['error'] == 0) {
            $target_dir = "gambar/";
            $file_name = basename($_FILES["gambar_baru"]["name"]);
            $target_file = $target_dir . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Pindahkan file dan update nama gambar baru
            if (move_uploaded_file($_FILES["gambar_baru"]["tmp_name"], $target_file)) {
                $gambar_baru = $file_name;
            } else {
                $message = "Gagal mengupload gambar.";
            }
        }
        
        // Query Insert/Update
        if (empty($message)) {
            if ($mode_post == 'tambah') {
                $sql = "INSERT INTO menu_makanan (nama_menu, deskripsi, harga, gambar_file) 
                        VALUES ('$nama_post', '$deskripsi_post', '$harga_post', '$gambar_baru')";
            } elseif ($mode_post == 'edit') {
                $sql = "UPDATE menu_makanan SET nama_menu='$nama_post', deskripsi='$deskripsi_post', 
                        harga='$harga_post', gambar_file='$gambar_baru' WHERE id_menu='$id_post'";
            }
            
            if ($koneksi->query($sql) === TRUE) {
                $message = ($mode_post == 'tambah') ? "Menu berhasil ditambahkan!" : "Menu berhasil diupdate!";
            } else {
                $message = "Error: " . $koneksi->error;
            }
            
            // Redirect setelah submit agar data POST tidak tersimpan
            header("Location: admin.php?message=" . urlencode(strip_tags($message)));
            exit();
        }
    }
    
    // Tampilkan pesan jika ada (dari redirect atau error)
    if (isset($_GET['message'])) {
         echo "<div style='padding: 10px; border: 1px solid #ccc; background-color:#f0fff0; margin-bottom: 15px; color: green;'>Status: " . htmlspecialchars($_GET['message']) . "</div>";
    }

    // --- Bagian 2: Form Tambah/Edit ---
    if ($show_form):
    ?>
        <h3><?php echo ($mode == 'tambah') ? 'Form Tambah Menu' : 'Form Edit Menu'; ?></h3>
        <form method="POST" enctype="multipart/form-data" action="admin.php">
            <input type="hidden" name="mode" value="<?php echo $mode; ?>">
            <?php if ($mode == 'edit'): ?>
                <input type="hidden" name="id_menu" value="<?php echo htmlspecialchars($id_edit); ?>">
                <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($gambar_lama); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="nama_menu">Nama Menu:</label>
                <input type="text" id="nama_menu" name="nama_menu" value="<?php echo htmlspecialchars($nama); ?>" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"><?php echo htmlspecialchars($deskripsi); ?></textarea>
            </div>
            <div class="form-group">
                <label for="harga">Harga (Angka saja, cth: 15000):</label>
                <input type="number" id="harga" name="harga" value="<?php echo htmlspecialchars($harga); ?>" required>
            </div>
            <div class="form-group">
                <label for="gambar_baru">Gambar Menu Baru:</label>
                <input type="file" id="gambar_baru" name="gambar_baru" accept="image/*">
                <?php if ($mode == 'edit' && $gambar_lama): ?>
                    <small style="display:block; margin-top: 5px;">Gambar saat ini: **<?php echo htmlspecialchars($gambar_lama); ?>**</small>
                <?php endif; ?>
            </div>
            <button type="submit" name="submit_menu" class="btn btn-success">Simpan Data</button>
            <a href="admin.php" class="btn btn-warning">Batal</a>
        </form>

    <?php 
    // --- Bagian 3: Tampilan Daftar Menu ---
    else: 
        $sql_list = "SELECT id_menu, nama_menu, harga, gambar_file FROM menu_makanan ORDER BY id_menu DESC";
        $result_list = $koneksi->query($sql_list);
    ?>
        <h3>Daftar Menu Saat Ini</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Nama Menu</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result_list->num_rows > 0): ?>
                <?php while($row = $result_list->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_menu']; ?></td>
                    <td><img src="gambar/<?php echo htmlspecialchars($row['gambar_file']); ?>" style="width: 50px; height: 50px; object-fit: cover;"></td>
                    <td><?php echo htmlspecialchars($row['nama_menu']); ?></td>
                    <td><?php echo formatRupiah($row['harga']); ?></td>
                    <td>
                        <a href="admin.php?action=edit&id=<?php echo $row['id_menu']; ?>" class="btn btn-warning">Edit</a>
                        <a href="hapus.php?id=<?php echo $row['id_menu']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus menu ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center;">Tidak ada data menu.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

</body>
</html>