<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Dinas Pariwisata Jeneponto</title>
    
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

        /* --- HEADER HIJAU & BESAR --- */
        header {
            background-color: #198754; /* Hijau Utama */
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        header h1 {
            margin: 0;
            font-size: 28px; /* FONT BESAR */
            text-align: center;
            flex-grow: 1;
            font-weight: 700;
            line-height: 1.2;
        }

        header img {
            height: 80px; /* LOGO BESAR */
            width: auto;
            object-fit: contain;
        }

        /* --- NAVIGASI HIJAU TUA --- */
        nav {
            background-color: #145c32; /* Hijau Tua */
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
            background-color: #28a745;
        }

        /* Menu Aktif (Kuning) */
        nav a.active {
            background-color: #ffc107;
            color: black;
            font-weight: 700;
        }

        /* --- KONTEN --- */
        section {
            flex: 1;
            padding: 40px 20px;
            max-width: 800px;
            margin: 20px auto;
            text-align: center;
        }

        .content-box {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        h2 {
            color: #145c32;
            margin-top: 0;
            text-transform: uppercase;
        }

        p {
            line-height: 1.6;
            color: #444;
            font-size: 16px;
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
    <a href="index.php" class="active">Beranda</a>
    <a href="prediksi_omzet.php">Bidang Ekonomi Kreatif</a>
    </nav>

<section>
    <div class="content-box">
        <h2>Selamat Datang</h2>
        <p>
            Website Sistem Informasi Dinas Pariwisata Kabupaten Jeneponto
            hadir sebagai media penyampaian informasi dan data pariwisata daerah.
        </p>
        <p>
            Silakan pilih menu di atas untuk melihat informasi.
        </p>
    </div>
</section>

</body>
</html>