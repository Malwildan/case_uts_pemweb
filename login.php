<?php
// Mulai sesi agar kita bisa menyimpan data login
session_start();

// Jika pengguna sudah login, langsung arahkan ke halaman buku tamu
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: guestbook.php");
    exit;
}

// Data akun yang valid (username => password)
// Di aplikasi nyata, ini sebaiknya disimpan di database
$valid_accounts = [
    "admin"    => "admin123",
    "pengguna" => "password1"
];

$error_message = ""; // Pesan error, kosong dulu

// Cek apakah form login sudah dikirim (method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Cek apakah username ada dan passwordnya cocok
    if (isset($valid_accounts[$username]) && $valid_accounts[$username] === $password) {
        // Login berhasil: simpan info ke session
        $_SESSION['logged_in'] = true;
        $_SESSION['username']  = $username;

        // Arahkan ke halaman buku tamu
        header("Location: guestbook.php");
        exit;
    } else {
        // Login gagal
        $error_message = "Username atau password salah. Silakan coba lagi.";
    }
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

        <!-- Tampilkan pesan error jika ada -->
        <?php if ($error_message !== ""): ?>
            <div class="error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <!-- Form login -->
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
