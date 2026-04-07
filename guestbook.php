<?php
session_start();

/* Cek apakah user sudah login */
if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

/* Siapkan folder, file, dan pesan */
$folderData = 'data';
$fileGuestbook = $folderData . '/guestbook.txt';

if (!is_dir($folderData)) {
    mkdir($folderData);
}

$pesanSukses = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

$pesanError = '';
$daftarTamu = [];

/* Fungsi kecil untuk membersihkan input */
function bersihkanInput($text)
{
    $text = trim($text);
    return str_replace(["|", "\n", "\r"], ' ', $text);
}

/* Proses form jika disubmit */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = bersihkanInput($_POST['nama'] ?? '');
    $email = bersihkanInput($_POST['email'] ?? '');
    $pesan = bersihkanInput($_POST['pesan'] ?? '');

    if ($nama === '' || $email === '' || $pesan === '') {
        $pesanError = 'Semua field wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesanError = 'Format email tidak valid!';
    } else {
        $tanggal = date('d-m-Y H:i:s');
        $baris = $tanggal . '|' . $nama . '|' . $email . '|' . $pesan . PHP_EOL;

        file_put_contents($fileGuestbook, $baris, FILE_APPEND | LOCK_EX);

        $_SESSION['success_message'] = "Terima kasih, $nama! Pesan kamu berhasil disimpan.";
        header('Location: guestbook.php');
        exit;
    }
}

/* Ambil semua data guestbook */
if (file_exists($fileGuestbook)) {
    $semuaBaris = file($fileGuestbook, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $semuaBaris = array_reverse($semuaBaris);

    foreach ($semuaBaris as $baris) {
        $data = explode('|', $baris, 4);

        if (count($data) === 4) {
            $daftarTamu[] = [
                'tanggal' => $data[0],
                'nama' => $data[1],
                'email' => $data[2],
                'pesan' => $data[3],
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
    <header>
        <h1>📖 Buku Tamu Digital</h1>
        <span>
            Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> | 
            <a href="logout.php">Logout</a>
        </span>
    </header>

    <div class="card">
        <h2>Isi Buku Tamu</h2>

        <?php if ($pesanSukses !== ''): ?>
            <div class="success"><?= htmlspecialchars($pesanSukses) ?></div>
        <?php endif; ?>

        <?php if ($pesanError !== ''): ?>
            <div class="error"><?= htmlspecialchars($pesanError) ?></div>
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

    <div class="card">
        <h2>Daftar Tamu (<?= count($daftarTamu) ?> pesan)</h2>

        <?php if (empty($daftarTamu)): ?>
            <p class="empty-text">Belum ada pesan. Jadilah yang pertama!</p>
        <?php else: ?>
            <?php foreach ($daftarTamu as $tamu): ?>
                <div class="entry">
                    <div class="entry-name"><?= htmlspecialchars($tamu['nama']) ?></div>
                    <div class="entry-email"><?= htmlspecialchars($tamu['email']) ?></div>
                    <div class="entry-pesan"><?= nl2br(htmlspecialchars($tamu['pesan'])) ?></div>
                    <div class="entry-tanggal">🕐 <?= htmlspecialchars($tamu['tanggal']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>