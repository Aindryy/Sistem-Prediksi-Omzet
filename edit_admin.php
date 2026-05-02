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
if(!isset($_GET['id'])){
    header("location:tambah_admin.php");
    exit;
}
$id = $_GET['id'];

// 4. QUERY AMBIL DATA LAMA
$data = mysqli_query($koneksi, "SELECT * FROM admin WHERE id='$id'");
$d = mysqli_fetch_array($data);

// Jika data tidak ditemukan
if(!$d){
    echo "<script>alert('Data tidak ditemukan!'); window.location='tambah_admin.php';</script>";
}

// 5. PROSES UPDATE DATA (Jika tombol simpan diklik)
if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Query Update berdasarkan ID
    $query = mysqli_query($koneksi, "UPDATE admin SET username='$username', password='$password' WHERE id='$id'");

    if ($query) {
        echo "<script>alert('Data Berhasil Diperbarui!'); window.location='tambah_admin.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f4f8; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h2 { color: #0054a6; margin-bottom: 20px; text-align: center; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }
        
        .btn-update {
            width: 100%;
            padding: 12px;
            background-color: #f39c12; /* Kuning Oranye untuk Edit */
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-update:hover { background-color: #d68910; transform: translateY(-2px); }
        
        .btn-batal {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Data Admin</h2>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo $d['username']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Password Baru</label>
            <input type="text" name="password" value="<?php echo $d['password']; ?>" required>
            <small style="color: #666;">*Password ditampilkan dalam teks biasa sesuai database Anda.</small>
        </div>
        
        <button type="submit" name="update" class="btn-update">SIMPAN PERUBAHAN</button>
        <a href="tambah_admin.php" class="btn-batal">Kembali ke Daftar</a>
    </form>
</div>

</body>
</html>