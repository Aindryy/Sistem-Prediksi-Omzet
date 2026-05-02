<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Dinas Pariwisata Jeneponto</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- HEADER BIRU (KHUSUS AREA ADMIN) --- */
        header {
            background-color: #76b0eb; /* Biru Utama */
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        header h1 {
            margin: 0;
            font-size: 28px; /* Ukuran Besar */
            text-align: center;
            flex-grow: 1;
            font-weight: 700;
            line-height: 1.2;
        }

        header img {
            height: 80px; /* Logo Besar */
            width: auto;
            object-fit: contain;
        }

        /* --- NAVIGASI --- */
        nav {
            background-color: #0071c2;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        nav a {
            color: white;
            padding: 15px 25px;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 500;
            font-size: 14px;
        }

        nav a:hover {
            background-color: #0054a6;
        }

        /* --- AREA LOGIN (Tengah) --- */
        section {
            flex: 1;
            display: flex;
            flex-direction: column; /* Judul di atas, Kotak di bawah */
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Judul Halaman di Luar Kotak */
        .page-title {
            color: #0054a6; /* Teks Biru */
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Kotak Login Biru Tua */
        .login-box {
            background-color: #004080; /* Biru Agak Tua (Elegan) */
            padding: 40px;
            border-radius: 15px;
            color: white;
            width: 100%;
            max-width: 350px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }

        .login-box h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 400;
            font-size: 18px;
        }

        /* Input Styles */
        .login-box input[type="text"],
        .login-box input[type="password"] {
            font-family: 'Poppins', sans-serif;
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 25px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .login-box input[type="text"]:focus,
        .login-box input[type="password"]:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
        }

        /* Tombol Login (Putih agar kontras dengan biru) */
        .login-box input[type="submit"] {
            font-family: 'Poppins', sans-serif;
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 25px;
            background-color: #ffffff;
            color: #0054a6;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-box input[type="submit"]:hover {
            background-color: #e6e6e6;
        }

        /* Style Pesan Error */
        .alert {
            background-color: #ffcccc;
            color: #cc0000;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 13px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <img src="p.png" alt="Logo Kiri">
    <h1>Dinas Pariwisata Kabupaten Jeneponto</h1>
    <img src="j.png" alt="Logo Kanan">
</header>

<nav>
    <a href="index.php">Beranda</a>
    <a href="prediksi_omzet.php">Bidang Ekonomi Kreatif</a>
</nav>

<section>
    <div class="page-title">
        ADMIN SISTEM PREDIKSI OMZET
    </div>

    <div class="login-box">
        <h2>Silakan Login</h2>

        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "gagal"){
                echo "<div class='alert'>Login Gagal! Username atau Password salah.</div>";
            } else if($_GET['pesan'] == "logout"){
                echo "<div class='alert' style='background-color: #d4edda; color: #155724;'>Anda berhasil logout.</div>";
            } else if($_GET['pesan'] == "belum_login"){
                echo "<div class='alert'>Anda harus login untuk mengakses admin.</div>";
            }
        }
        ?>

        <form action="proses_login.php" method="post">
            <input type="text" name="username" placeholder="Username" required autocomplete="off">
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="LOGIN">
        </form>
    </div>
</section>

</body>
</html>