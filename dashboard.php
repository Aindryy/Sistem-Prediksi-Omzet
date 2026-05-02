<?php 
session_start();

// Cek apakah user sudah login atau belum
if($_SESSION['status'] != "login"){
    header("location:admin.php?pesan=belum_login");
    exit; 
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN SISTEM PREDIKSI OMZET</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        /* --- HEADER & NAV --- */
        header {
            background-color: #76b0eb;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        header h1 {
            margin: 0;
            font-size: 20px; 
            text-align: center;
            flex-grow: 1;
            text-transform: uppercase;
        }

        header img {
            height: 60px;
            width: auto;
        }

        /* --- KONTEN DASHBOARD --- */
        .container {
            padding: 40px;
            max-width: 1100px;
            margin: 0 auto;
            text-align: center;
        }

        .welcome-box {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 { color: #0054a6; margin-bottom: 10px; }
        hr { border: 0; border-top: 1px solid #eee; margin: 20px 0; }

        /* --- GROUP TOMBOL --- */
        .button-group {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap; 
            gap: 15px;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        /* Variasi Warna Tombol */
        .btn-add { background-color: #28a745; color: white; }
        .btn-add:hover { background-color: #218838; }

        .btn-kelola { background-color: #0054a6; color: white; }
        .btn-kelola:hover { background-color: #003d7a; }

        .btn-logout { background-color: #dc3545; color: white; }
        .btn-logout:hover { background-color: #b02a37; }

        /* --- TAMBAHAN STYLE UNTUK TEKS LINK (KIRI & KANAN) --- */
        .link-bantuan {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            border-top: 1px solid #f1f1f1;
            padding-top: 15px;
        }

        .link-bantuan a {
            text-decoration: none;
            color: #555;
            font-size: 13px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .link-bantuan a:hover {
            color: #0054a6;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <img src="p.png" alt="Logo Kiri">
    <h1>SISTEM PREDIKSI OMZET EKONOMI KREATIF JENEPONTO</h1>
    <img src="j.png" alt="Logo Kanan">
</header>

<div class="container">
    <div class="welcome-box">
        <h2>Selamat Datang, <?php echo $_SESSION['username']; ?>!</h2>
        <p>Anda telah berhasil login ke Halaman Administrator.</p>
        <hr>
        <p>Silakan pilih button di bawah untuk mengelola data admin dan prediksi omzet.</p>
        
        <div class="button-group">
            <a href="tambah_admin.php" class="btn btn-add">➕ TAMBAH DATA ADMIN</a>
            <a href="tambah_data_aktual.php" class="btn btn-kelola">📊 KELOLA DATA AKTUAL</a>
            <a href="hybrid_arimalstm2.php" class="btn btn-kelola"> ⚙️ PREDIKSI OMZET </a>
            <a href="logout.php" class="btn btn-logout">🚪 LOGOUT</a>
        </div>

        <div class="link-bantuan">
            <a href="https://drive.google.com/file/d/1YT_Nu53o8fCiRpxnocfJ631VZOrG-MkP/view?usp=sharing" target="_blank">
                📄 Pedoman Pengelolaan Website
            </a>
            <a href="https://www.youtube.com/playlist?list=PLJ90c9vHEWa6SaHj61phtamB4bvo4C2RS" target="_blank">
                🎬 Tutorial Penggunaan (YouTube)
            </a>
        </div>
    </div>
</div>

</body>
</html>