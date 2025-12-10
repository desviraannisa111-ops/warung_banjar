<?php include 'header.php'; ?>

<div class="hero">
    <h1>"warung banjar"</h1>
    <p>Lihat menu Banjar kami di bawah ini!</p>
</div>

<div class="menu-header">
    <h2>Menu Andalan Dapur Banjar</h2>
</div>

<div class="menu-container">
    <?php
    // Query untuk mengambil data menu dari database
    $sql = "SELECT nama_menu, deskripsi, harga, gambar_file FROM menu_makanan ORDER BY id_menu DESC";
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        while($item = $result->fetch_assoc()):
    ?>
        <div class="menu-card">
            <img src="gambar/<?php echo htmlspecialchars($item['gambar_file']); ?>" 
                 alt="<?php echo htmlspecialchars($item['nama_menu']); ?>">
                 
            <div class="menu-info">
                <h3><?php echo htmlspecialchars($item['nama_menu']); ?></h3>
                <p><?php echo htmlspecialchars($item['deskripsi']); ?></p>
                <span class="price"><?php echo formatRupiah($item['harga']); ?></span>
                
                <?php
                    // Ambil nama menu dan encode untuk URL
                    $nama_menu_encoded = urlencode($item['nama_menu']);
                    // **PENTING: GANTI 62895414830410 dengan nomor WhatsApp Anda!**
                    $whatsapp_number = "62895414830410"; 
                    $whatsapp_message = "Halo Warung Banjar, saya mau pesan menu " . $nama_menu_encoded . ". Apakah stoknya tersedia?";
                    $whatsapp_url = "https://wa.me/" . $whatsapp_number . "?text=" . urlencode($whatsapp_message);
                ?>
                <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="btn btn-whatsapp">
                    Pesan Sekarang
                </a>
                </div>
        </div>
    <?php
        endwhile;
    } else {
        echo "<p style='text-align:center;'>Belum ada menu yang tersedia.</p>";
    }
    ?>
</div>

<?php include 'footer.php'; ?>