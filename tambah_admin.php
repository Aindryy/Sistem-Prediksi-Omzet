<?php
session_start();

// 1. KONEKSI (Disesuaikan dengan Port XAMPP 3306)
$host = "127.0.0.1:3306"; // Ganti localhost ke IP:Port
$user = "root";
$pass = "";
$db   = "dispar"; 

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. CEK LOGIN
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:admin.php?pesan=belum_login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Admin - Database Dispar</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f4f8; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h2 { color: #0054a6; margin: 0; }
        .btn-tambah { display: inline-block; text-decoration: none; background-color: #28a745; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; margin-bottom: 20px; transition: 0.3s; }
        .btn-tambah:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #0054a6; color: white; }
        tr:hover { background-color: #f8f9fa; }
        .btn-edit { color: #f39c12; text-decoration: none; font-weight: bold; margin-right: 10px; }
        .btn-hapus { color: #e74c3c; text-decoration: none; font-weight: bold; }
        .btn-back { display:block; margin-top:20px; text-decoration:none; color:#0054a6; font-weight: 600; }
    </style>
</head>
<body>

<div class="container">
    <h2>Data Administrator</h2>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Kelola akun akses sistem Dinas Pariwisata</p>
    
    <a href="form_tambah_admin.php" class="btn-tambah">+ TAMBAH ADMIN</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Username</th>
                <th>Password</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $query = mysqli_query($koneksi, "SELECT * FROM admin");

            if($query && mysqli_num_rows($query) > 0) {
                while($d = mysqli_fetch_array($query)){
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><span style="color: #888;">#<?php echo $d['id']; ?></span></td>
                        <td><strong><?php echo $d['username']; ?></strong></td>
                        <td><code><?php echo $d['password']; ?></code></td> 
                        <td align="center">
                            <a href="edit_admin.php?id=<?php echo $d['id']; ?>" class="btn-edit">EDIT</a>
                            <a href="hapus_admin.php?id=<?php echo $d['id']; ?>" class="btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus admin ini?')">HAPUS</a>
                        </td>
                    </tr>
                    <?php 
                }
            } else {
                echo "<tr><td colspan='5' align='center'>Data tidak ditemukan di tabel admin.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
</div>

</body>
</html>