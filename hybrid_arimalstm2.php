<?php
session_start();
// 1. Koneksi Database - Menggunakan Port 3307 sesuai konfigurasi kamu
$conn = mysqli_connect("127.0.0.1:3306", "root", "", "dispar");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// 2. Ambil Metrik Akurasi
$q_metrik = mysqli_query($conn, "SELECT * FROM metrik_akurasii WHERE id = 1");
$metrik = ($q_metrik && mysqli_num_rows($q_metrik) > 0) ? mysqli_fetch_assoc($q_metrik) : ['rmse' => 0, 'mae' => 0, 'mape' => 0];

// 3. Ambil Data Terakhir untuk Pembanding Tren (Desember 2024)
$q_last = mysqli_query($conn, "SELECT jenis_ekraf, omzet FROM data_omzet_bersih WHERE periode = (SELECT MAX(periode) FROM data_omzet_bersih)");
$last_data = [];
while($l = mysqli_fetch_assoc($q_last)) {
    $last_data[$l['jenis_ekraf']] = (float)$l['omzet'];
}

// 4. Ambil Data Prediksi 2025-2026
$res_query = mysqli_query($conn, "SELECT * FROM prediksi_masa_depan ORDER BY tanggal ASC, jenis_ekraf ASC");
$has_results = ($res_query && mysqli_num_rows($res_query) > 0);

$data_per_jenis = [];
if ($has_results) {
    while($row = mysqli_fetch_assoc($res_query)) {
        $jenis = $row['jenis_ekraf'];
        $data_per_jenis[$jenis]['labels'][] = date('M Y', strtotime($row['tanggal']));
        $data_per_jenis[$jenis]['prediksi'][] = (float)$row['prediksi'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forecast Hybrid ARIMA-LSTM 2025-2026</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7fe; padding: 30px; color: #2d3748; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 40px; border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #edf2f7; padding-bottom: 20px; }
        .title h1 { margin: 0; font-size: 24px; color: #1a202c; }
        
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 500; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #34d399; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; font-family: monospace; font-size: 12px; }

        .btn-group { display: flex; gap: 10px; }
        .btn-run { background: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 12px; font-weight: 600; transition: 0.3s; border: none; cursor: pointer; }
        .btn-run:hover { background: #4338ca; transform: translateY(-2px); }
        .btn-dashboard { background: #ffffff; color: #4a5568; padding: 12px 24px; text-decoration: none; border-radius: 12px; font-weight: 600; border: 1px solid #e2e8f0; }

        .stats-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: #ffffff; padding: 20px; border-radius: 20px; text-align: center; border: 1px solid #e2e8f0; border-top: 4px solid #4f46e5; }
        .stat-value { font-size: 22px; font-weight: 700; color: #2d3748; margin-top: 8px; }
        
        .chart-card { background: #f8fafc; padding: 25px; border-radius: 24px; margin-bottom: 30px; border: 1px solid #e2e8f0; }
        select { padding: 10px 20px; border-radius: 10px; border: 1px solid #cbd5e0; font-family: 'Poppins'; outline: none; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #f7fafc; padding: 15px; text-align: left; font-size: 14px; border-bottom: 2px solid #edf2f7; }
        td { padding: 15px; border-bottom: 1px solid #edf2f7; font-size: 14px; }
        .badge-kategori { background: #e0e7ff; color: #4338ca; padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 12px; }
        .tren-naik { color: #10b981; font-weight: 700; }
        .tren-turun { color: #ef4444; font-weight: 700; }
        .empty-state { text-align: center; padding: 80px; border: 3px dashed #e2e8f0; border-radius: 30px; color: #a0aec0; }
    </style>
</head>
<body>

<div class="container">
    <?php if(isset($_SESSION['sukses_prediksi'])): ?>
        <div class="alert alert-success"><?= $_SESSION['sukses_prediksi']; unset($_SESSION['sukses_prediksi']); ?></div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_prediksi'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error_prediksi']; unset($_SESSION['error_prediksi']); ?></div>
    <?php endif; ?>

    <div class="header">
        <div class="title">
            <h1>📊 Prediksi Omzet Ekraf 2025 - 2026</h1>
            <p>Model Hybrid: ARIMA & LSTM </p>
        </div>
        <div class="btn-group">
            <a href="dashboard.php" class="btn-dashboard">🏠 Dashboard</a>
            <a href="proses_prediksi_2tahun.php" class="btn-run" onclick="return confirm('Mulai proses AI Hybrid? Ini akan memakan waktu beberapa detik.')">⚙️ Update Forecast</a>
        </div>
    </div>

    <?php if ($has_results): ?>
        <div class="stats-container">
            <div class="stat-box"><h4>RMSE (Rata-rata)</h4><div class="stat-value"><?= number_format($metrik['rmse'], 0, ',', '.') ?></div></div>
            <div class="stat-box"><h4>MAE (Rata-rata)</h4><div class="stat-value"><?= number_format($metrik['mae'], 0, ',', '.') ?></div></div>
            <div class="stat-box"><h4>MAPE (Akurasi)</h4><div class="stat-value"><?= number_format($metrik['mape'], 2, ',', '.') ?>%</div></div>
        </div>

        <div class="chart-card">
            <div style="margin-bottom: 20px;">
                <label style="font-weight: 600; margin-right: 10px;">Filter Kategori:</label>
                <select id="jenisSelector" onchange="renderChart(this.value)">
                    <?php foreach(array_keys($data_per_jenis) as $jenis): ?>
                        <option value="<?= $jenis ?>"><?= $jenis ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="height: 400px;">
                <canvas id="forecastChart"></canvas>
            </div>
        </div>

        <h3>📋 Detail Hasil Prediksi Bulanan</h3>
        <div style="max-height: 600px; overflow-y: auto; border-radius: 15px; border: 1px solid #e2e8f0;">
            <table>
                <thead style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th>Periode</th>
                        <th>Kategori</th>
                        <th>Prediksi Omzet</th>
                        <th>Analisis Tren</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($res_query, 0); 
                    while($row = mysqli_fetch_assoc($res_query)): 
                        $jns = $row['jenis_ekraf'];
                        $pred = (float)$row['prediksi'];
                        $last = isset($last_data[$jns]) ? $last_data[$jns] : 0;
                        $is_naik = ($pred >= $last);
                    ?>
                    <tr>
                        <td><strong><?= date('F Y', strtotime($row['tanggal'])) ?></strong></td>
                        <td><span class="badge-kategori"><?= $jns ?></span></td>
                        <td style="color: #4f46e5; font-weight: 700;">Rp <?= number_format($pred, 0, ',', '.') ?></td>
                        <td class="<?= $is_naik ? 'tren-naik' : 'tren-turun' ?>">
                            <?= $is_naik ? '📈 Naik' : '📉 Turun' ?>
                            <small style="color: #a0aec0; font-weight: 400;">(vs Data Terakhir)</small>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="empty-state">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" style="opacity: 0.2; margin-bottom: 20px;">
            <h2>Data Belum Tersedia</h2>
            <p>Silakan klik tombol <strong>Update Forecast</strong> untuk menjalankan mesin AI Hybrid.</p>
        </div>
    <?php endif; ?>
</div>

<script>
<?php if ($has_results): ?>
const chartData = <?= json_encode($data_per_jenis) ?>;
let activeChart;

function renderChart(jenis) {
    const ctx = document.getElementById('forecastChart').getContext('2d');
    if (activeChart) activeChart.destroy();

    activeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData[jenis].labels,
            datasets: [{
                label: 'Prediksi Omzet ' + jenis,
                data: chartData[jenis].prediksi,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointBackgroundColor: '#4f46e5'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    padding: 15,
                    callbacks: {
                        label: (ctx) => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    ticks: { callback: (val) => 'Rp ' + val.toLocaleString('id-ID') }
                }
            }
        }
    });
}
// Jalankan grafik pertama kali
renderChart(document.getElementById('jenisSelector').value);
<?php endif; ?>
</script>
</body>
</html>