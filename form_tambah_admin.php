<?php
session_start();

// 1. KONEKSI (Diselaraskan ke Port 3307)
$host = "127.0.0.1:3306"; 
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

// 3. PROSES SIMPAN DATA
if (isset($_POST['simpan'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Mencari ID terakhir agar tidak bentrok
    $result = mysqli_query($koneksi, "SELECT max(id) as max_id FROM admin");
    $row = mysqli_fetch_array($result);
    
    // Jika tabel kosong, max_id jadi 0, lalu ditambah 1
    $id_baru = ($row['max_id'] == null) ? 1 : $row['max_id'] + 1; 

    // Query insert
    $query = mysqli_query($koneksi, "INSERT INTO admin (id, username, password) VALUES ('$id_baru', '$username', '$password')");

    if ($query) {
        echo "<script>alert('Data Berhasil Disimpan! ID Baru: $id_baru'); window.location='tambah_admin.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Admin Baru</title>
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
            outline: none;
            transition: 0.3s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #0054a6;
            box-shadow: 0 0 8px rgba(0,84,166,0.1);
        }
        
        .btn-simpan {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-simpan:hover { background-color: #218838; transform: translateY(-2px); }
        
        .btn-batal {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #dc3545;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Tambah Admin Baru</h2>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Buat username baru" required autocomplete="off">
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Buat password baru" required>
        </div>
        
        <button type="submit" name="simpan" class="btn-simpan">SIMPAN DATA</button>
        <a href="tambah_admin.php" class="btn-batal">Batal & Kembali</a>
    </form>
</div>

</body>
</html>