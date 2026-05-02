<?php
session_start();

// 1. KONEKSI
$host = "127.0.0.1:3306"; 
$user = "root";
$pass = "";
$db   = "dispar"; 

$koneksi = mysqli_connect($host, $user, $pass, $db);

// 2. CEK LOGIN
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:admin.php?pesan=belum_login");
    exit;
}

// 3. AMBIL ID DARI URL
if(isset($_GET['id'])){
    $id = $_GET['id'];

    // 4. QUERY HAPUS DATA
    $query = mysqli_query($koneksi, "DELETE FROM admin WHERE id='$id'");

    if($query){
        // Jika berhasil, kembali ke halaman utama dengan pesan sukses
        echo "<script>alert('Data admin berhasil dihapus!'); window.location='tambah_admin.php';</script>";
    } else {
        // Jika gagal, tampilkan error
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "'); window.location='tambah_admin.php';</script>";
    }
} else {
    // Jika tidak ada ID di URL, lempar balik ke halaman utama
    header("location:tambah_admin.php");
}
?>