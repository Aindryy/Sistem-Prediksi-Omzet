<?php
$host = '127.0.0.1';
$user = 'root';
$pass = ''; 
$db   = 'dispar';
$port = '3306'; // Tambahkan variabel port jika XAMPP kamu tidak standar

// Tambahkan parameter port di akhir fungsi mysqli_connect
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
// Hapus echo "Koneksi Berhasil" agar tidak mengganggu proses login/redirect
?>