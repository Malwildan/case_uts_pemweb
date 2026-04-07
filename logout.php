<?php
// Mulai sesi agar kita bisa mengaksesnya
session_start();

// Hapus semua data sesi (termasuk info login)
session_destroy();

// Arahkan pengguna kembali ke halaman login
header("Location: login.php");
exit;
