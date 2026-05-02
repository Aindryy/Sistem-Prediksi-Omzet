<?php
// 1. KONEKSI DATABASE
$conn = mysqli_connect('127.0.0.1:3306', 'root', '', 'dispar');

// --- LOGIKA FIX REAL-TIME BULAN INI ---
$currentMonthStr = date('Y-m-01'); 
$prevMonthStr = date('Y-m-d', strtotime('-1 month', strtotime($currentMonthStr)));

// 2. QUERY PERINGKAT DENGAN SKALA MAKSIMAL 100%
$query = "
    SELECT 
        curr.jenis_ekraf, 
        CASE 
            WHEN prev.prediksi = 0 THEN 0 
            ELSE 
                CASE 
                    WHEN ((curr.prediksi - prev.prediksi) / prev.prediksi * 100) > 100 THEN 100
                    WHEN ((curr.prediksi - prev.prediksi) / prev.prediksi * 100) < -100 THEN -100
                    ELSE ((curr.prediksi - prev.prediksi) / prev.prediksi * 100)
                END
        END as persentase
    FROM prediksi_masa_depan curr
    INNER JOIN prediksi_masa_depan prev ON curr.jenis_ekraf = prev.jenis_ekraf 
    WHERE curr.tanggal = '$currentMonthStr' 
    AND prev.tanggal = '$prevMonthStr'
    ORDER BY persentase DESC";

$ranking_query = mysqli_query($conn, $query);
$data_ada = mysqli_num_rows($ranking_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringkat Prediksi Ekraf - Dinas Pariwisata Jeneponto</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh; 
            overflow: hidden;
        }

        /* --- HEADER (IDENTIK DENGAN BERANDA) --- */
        header {
            background-color: #198754;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        header h1 {
            margin: 0;
            font-size: 28px;
            text-align: center;
            flex-grow: 1;
            font-weight: 700;
            line-height: 1.2;
        }

        header img {
            height: 80px;
            width: auto;
            object-fit: contain;
        }

        /* --- NAVIGASI (DENGAN MENU ADMIN) --- */
        nav {
            background-color: #145c32;
            padding: 0;
            display: flex;
            justify-content: center;
            flex-shrink: 0;
        }

        nav a {
            color: white;
            padding: 15px 25px;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 500;
            font-size: 14px;
        }

        nav a:hover { background-color: #28a745; }
        
        /* Highlight Menu Aktif */
        nav a.active {
            background-color: #ffc107;
            color: black;
            font-weight: 700;
        }

        /* --- CONTAINER DASHBOARD --- */
        .main-container {
            flex: 1;
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-sizing: border-box;
        }

        .ranking-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            max-height: 100%;
        }

        .card-title {
            color: #145c32;
            margin-bottom: 10px;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            flex-shrink: 0;
        }

        /* --- TABEL SCROLLABLE --- */
        .table-wrapper {
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-top: 10px;
        }

        .ranking-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ranking-table th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            color: #666;
            font-size: 13px;
            z-index: 10;
            border-bottom: 2px solid #eee;
        }

        .ranking-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        /* --- BADGE PERSENTASE --- */
        .pct-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }
        .up { background: #dcfce7; color: #166534; }
        .down { background: #fee2e2; color: #991b1b; }
        .stable { background: #e9ecef; color: #495057; }

        /* Tombol Telegram */
        .btn-telegram {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #0088cc;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 1000;
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
    <a href="prediksi_omzet.php" class="active">Bidang Ekonomi Kreatif</a>
    <a href="admin.php">Admin</a> </nav>

<div class="main-container">
    <div class="ranking-card">
        <div class="card-title">Prediksi Omzet</div>
        <p style="text-align: center; font-size: 13px; color: #666; margin: 0 0 10px 0;">
            Prediksi Bulan: <b><?= date('F Y') ?></b>
        </p>

        <?php if($data_ada > 0): ?>
        <div class="table-wrapper">
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th width="8%">No</th>
                        <th width="52%">Kategori Ekonomi Kreatif</th>
                        <th width="20%" style="text-align: center;">Persentase (%)</th>
                        <th width="20%" style="text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $n = 1; 
                    while($rk = mysqli_fetch_assoc($ranking_query)): 
                        $pct = $rk['persentase'];
                        $status_class = ($pct > 0) ? 'up' : (($pct < 0) ? 'down' : 'stable');
                        $label = ($pct > 0) ? 'Meningkat' : (($pct < 0) ? 'Menurun' : 'Stabil');
                        $icon = ($pct > 0) ? '▲' : (($pct < 0) ? '▼' : '▬');
                    ?>
                    <tr>
                        <td>#<?= $n++ ?></td>
                        <td><b><?= $rk['jenis_ekraf'] ?></b></td>
                        <td style="text-align: center;">
                            <span class="pct-badge <?= $status_class ?>">
                                <?= $icon ?> <?= number_format(abs($pct), 2) ?>%
                            </span>
                        </td>
                        <td style="text-align: right; font-weight: 600; color: #555;">
                            <?= $label ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 8px; text-align: center; font-size: 14px;">
                ⚠️ Belum ada data perbandingan untuk periode ini.
            </div>
        <?php endif; ?>
    </div>
</div>

<a href="https://t.me/Prediksi_Omzet_bot" target="_blank" class="btn-telegram">
    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM12.191 5.111a.4.4 0 0 0-.447-.078L4.26 8.56a.4.4 0 0 0-.017.713l2.357.971 1.1 3.225a.4.4 0 0 0 .702.12l1.31-1.538 2.728 2.015a.4.4 0 0 0 .623-.209l2.308-9.451a.4.4 0 0 0-.19-.441z"/></svg>
</a>

</body>
</html>