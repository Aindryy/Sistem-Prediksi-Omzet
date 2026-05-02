<?php
session_start();
include 'koneksi.php'; // Memastikan file ini berisi $conn = mysqli_connect(...)

$username = $_POST['username'];

// Password dibaca sebagai teks biasa sesuai permintaanmu
$password = $_POST['password']; 

// PERBAIKAN: Ganti $koneksi menjadi $conn agar sesuai dengan isi file koneksi.php
$login = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' AND password='$password'");

// Cek apakah query berhasil dijalankan
if ($login) {
    $cek = mysqli_num_rows($login);

    if($cek > 0){
        // Jika cocok, masuk ke dashboard
        $_SESSION['username'] = $username;
        $_SESSION['status'] = "login";
        header("location:dashboard.php");
    } else {
        // Jika salah, kembali ke login
        header("location:admin.php?pesan=gagal");
    }
} else {
    // Jika query error (misal tabel 'admin' tidak ada)
    die("Error pada query: " . mysqli_error($conn));
}
?>