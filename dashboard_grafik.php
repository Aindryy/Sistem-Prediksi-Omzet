<?php
session_start();
if($_SESSION['status'] != "login"){ header("location:admin.php?pesan=belum_login"); exit; }

$conn = mysqli_connect("localhost", "root", "", "dispar");

// Ambil daftar jenis ekraf untuk filter dropdown
$jenis_query = mysqli_query($conn, "SELECT DISTINCT jenis_ekraf FROM hasil_prediksi");
$selected_jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Grafik Prediksi Hybrid</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f4f8; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .filter-section { margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        select { padding: 10px; border-radius: 5px; border: 1px solid #ddd; width: 250px; }
        .btn-back { display: inline-block; margin-top: 20px; color: #666; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <div class="filter-section">
        <h3>📈 Visualisasi Hasil Prediksi</h3>
        <form method="GET">
            <label>Pilih Jenis Ekraf: </label>
            <select name="jenis" onchange="this.form.submit()">
                <option value="">-- Pilih Kategori --</option>
                <?php while($j = mysqli_fetch_assoc($jenis_query)): ?>
                    <option value="<?= $j['jenis_ekraf'] ?>" <?= ($selected_jenis == $j['jenis_ekraf']) ? 'selected' : '' ?>><?= $j['jenis_ekraf'] ?></option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <?php
    if($selected_jenis != ""){
        $data_query = mysqli_query($conn, "SELECT * FROM hasil_prediksi WHERE jenis_ekraf = '$selected_jenis' ORDER BY tanggal ASC");
        $labels = []; $data_asli = []; $data_prediksi = [];
        while($row = mysqli_fetch_assoc($data_query)){
            $labels[] = date('M Y', strtotime($row['tanggal']));
            $data_asli[] = $row['asli'];
            $data_prediksi[] = $row['prediksi'];
        }
    ?>
        <canvas id="myChart"></canvas>
        <script>
            const ctx = document.getElementById('myChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [
                        { label: 'Data Aktual', data: <?= json_encode($data_asli) ?>, borderColor: '#0054a6', tension: 0.3, fill: false },
                        { label: 'Prediksi Hybrid', data: <?= json_encode($data_prediksi) ?>, borderColor: '#6f42c1', borderDash: [5, 5], tension: 0.3, fill: false }
                    ]
                },
                options: { responsive: true, plugins: { legend: { position: 'top' } } }
            });
        </script>
    <?php } else { echo "<p>Silakan pilih jenis ekonomi kreatif untuk melihat grafik.</p>"; } ?>

    <a href="admin_dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
</div>

</body>
</html>