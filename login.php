<?php
session_start();

/* Jika sudah login, langsung masuk ke guestbook */
if (!empty($_SESSION['logged_in'])) {
    header('Location: guestbook.php');
    exit;
}

/* Daftar akun yang boleh login */
$akunValid = [
    'admin' => 'admin123',
    'pengguna' => 'password1'
];

/* Pesan error default */
$pesanError = '';

/* Proses login saat form dikirim */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $usernameBenar = isset($akunValid[$username]);
    $passwordBenar = $usernameBenar && $akunValid[$username] === $password;

    if ($passwordBenar) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;

        header('Location: guestbook.php');
        exit;
    }

    $pesanError = 'Username atau password salah. Silakan coba lagi.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Buku Tamu Digital</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <div class="login-box">
        <h2>Login</h2>
        <p style="text-align:center; color:#666; margin-bottom:20px;">Buku Tamu Digital</p>

        <?php if ($pesanError !== ''): ?>
            <div class="error"><?= htmlspecialchars($pesanError) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Masukkan username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>

            <button type="submit">Masuk</button>
        </form>

        <div class="hint">
            Akun tersedia:<br>
            <strong>admin</strong> / admin123<br>
            <strong>pengguna</strong> / password1
        </div>
    </div>
</body>
</html>