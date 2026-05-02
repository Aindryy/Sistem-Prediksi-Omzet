<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visi & Misi - Dinas Pariwisata Jeneponto</title>
    
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
            background-color: #198754; /* HIJAU UTAMA */
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
            background-color: transparent;
            border-radius: 5px;
        }

        /* --- NAVIGASI HIJAU TUA --- */
        nav {
            background-color: #145c32; /* HIJAU TUA */
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
            background-color: #28a745; /* Hijau Terang saat di-hover */
        }

        /* Menu Aktif (KUNING) */
        nav a.active {
            background-color: #ffc107; /* KUNING */
            color: black; /* Teks Hitam biar terbaca */
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
            color: #145c32; /* Judul ikut warna Hijau Tua */
            margin-top: 30px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        h2:first-of-type {
            margin-top: 0;
        }

        p {
            line-height: 1.8;
            color: #444;
            font-size: 16px;
            margin-bottom: 30px;
        }

        ul {
            text-align: left;
            display: inline-block;
            padding-left: 20px;
            color: #444;
        }

        li {
            margin-bottom: 10px;
            line-height: 1.6;
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
    <a href="visi_misi.php" class="active">Visi & Misi</a>
    <a href="prediksi_omzet.php">Bidang Ekonomi Kreatif</a>
</nav>

<section>
    <div class="content-box">
        <h2>Visi</h2>
        <p>
            "Terwujudnya Kabupaten Jeneponto sebagai daerah tujuan wisata
            yang berdaya saing dan berkelanjutan."
        </p>

        <hr style="border: 0; border-top: 2px solid #ffc107; margin: 20px 0;">

        <h2>Misi</h2>
        <ul>
            <li>Mengembangkan potensi wisata daerah secara optimal.</li>
            <li>Meningkatkan kualitas pelayanan pariwisata yang profesional.</li>
            <li>Mendorong pertumbuhan ekonomi kreatif berbasis kearifan lokal.</li>
        </ul>
    </div>
</section>

</body>
</html>