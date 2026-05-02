<?php 
session_start();
session_destroy(); // Hapus sesi login
header("location:admin.php"); // Kembali ke halaman login
?>