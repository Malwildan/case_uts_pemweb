<?php
// Mulai sesi
session_start();

// Proteksi halaman: jika belum login, arahkan ke halaman login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Lokasi file tempat menyimpan data buku tamu
// Kita pakai file teks biasa, tiap baris = satu entri (dipisah dengan tanda |)
$data_file = "data/guestbook.txt";

// Buat folder 'data' jika belum ada
if (!is_dir("data")) {
    mkdir("data", 0755, true);
}

$success_message = "";
$error_message   = "";

// Jika ada pesan sukses dari redirect sebelumnya, ambil lalu hapus dari session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Proses form ketika dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    // Validasi: semua field harus diisi
    if ($nama === '' || $email === '' || $pesan === '') {
        $error_message = "Semua field wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Validasi format email
        $error_message = "Format email tidak valid!";
    } else {
        // Buat satu baris data: waktu|nama|email|pesan
        // kita ganti karakter | dan baris baru agar tidak merusak format file
        $tanggal = date("d-m-Y H:i:s");
        $nama    = str_replace(["|", "\n", "\r"], " ", $nama);
        $email   = str_replace(["|", "\n", "\r"], " ", $email);
        $pesan   = str_replace(["|", "\n", "\r"], " ", $pesan);

        $baris = "$tanggal|$nama|$email|$pesan" . PHP_EOL;

        // Tulis ke file (tambahkan di bawah, bukan timpa)
        file_put_contents($data_file, $baris, FILE_APPEND | LOCK_EX);

        // Simpan pesan sukses ke session lalu redirect (Post/Redirect/Get)
        $_SESSION['success_message'] = "Terima kasih, $nama! Pesan kamu berhasil disimpan.";
        header('Location: guestbook.php');
        exit;
    }
}

// Baca semua entri dari file untuk ditampilkan
$semua_entri = [];

if (file_exists($data_file)) {
    $baris_baris = file($data_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Balik urutan agar yang terbaru muncul paling atas
    $baris_baris = array_reverse($baris_baris);

    foreach ($baris_baris as $baris) {
        $bagian = explode("|", $baris, 4); // maks 4 bagian: tanggal, nama, email, pesan
        if (count($bagian) === 4) {
            $semua_entri[] = [
                'tanggal' => $bagian[0],
                'nama'    => $bagian[1],
                'email'   => $bagian[2],
                'pesan'   => $bagian[3],
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu Digital</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

    <!-- Header dengan nama pengguna dan tombol logout -->
    <header>
        <h1>📖 Buku Tamu Digital</h1>
        <span>
            Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> &nbsp;|&nbsp;
            <a href="logout.php">Logout</a>
        </span>
    </header>

    <!-- Form isian buku tamu -->
    <div class="card">
        <h2>Isi Buku Tamu</h2>

        <!-- Tampilkan pesan sukses atau error -->
        <?php if ($success_message !== ""): ?>
            <div class="success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if ($error_message !== ""): ?>
            <div class="error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="POST" action="guestbook.php">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan nama kamu" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email kamu" required>

            <label for="pesan">Pesan</label>
            <textarea id="pesan" name="pesan" placeholder="Tulis pesanmu di sini..." required></textarea>

            <button type="submit">Kirim Pesan</button>
        </form>
    </div>

    <!-- Daftar entri buku tamu -->
    <div class="card">
        <h2>Daftar Tamu (<?= count($semua_entri) ?> pesan)</h2>

        <?php if (empty($semua_entri)): ?>
            <p class="empty-text">Belum ada pesan. Jadilah yang pertama!</p>
        <?php else: ?>
            <?php foreach ($semua_entri as $entri): ?>
                <div class="entry">
                    <div class="entry-name"><?= htmlspecialchars($entri['nama']) ?></div>
                    <div class="entry-email"><?= htmlspecialchars($entri['email']) ?></div>
                    <div class="entry-pesan"><?= nl2br(htmlspecialchars($entri['pesan'])) ?></div>
                    <div class="entry-tanggal">🕐 <?= htmlspecialchars($entri['tanggal']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
