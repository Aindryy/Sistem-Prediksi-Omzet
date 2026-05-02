<?php
session_start();

// 1. Pastikan file koneksi dipanggil dengan benar
// Jika file koneksi.php ada di folder yang sama, gunakan ini:
include 'koneksi.php'; 

// 2. CEK KONEKSI (Sangat Penting)
// Sesuaikan nama variabel $conn di bawah dengan variabel yang ada di file koneksi.php kamu
if (!isset($conn)) {
    // Jika di file koneksi.php kamu menggunakan nama $koneksi, maka aktifkan baris di bawah ini:
    // $conn = $koneksi; 
    
    // Jika tetap tidak ada, kita buat koneksi darurat untuk testing:
    $conn = mysqli_connect("localhost", "root", "", "dispar");
}

// 3. Jalankan Query dengan variabel $conn yang sudah dipastikan ada
$sql = "SELECT * FROM hasil_prediksi WHERE status = 'forecast' ORDER BY tanggal ASC";
$result = mysqli_query($conn, $sql);

// 4. Cek apakah query berhasil
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Proyeksi 3 Tahun</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f4f8; padding: 40px; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #d35400; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #ff9800; color: white; padding: 12px; }
        td { padding: 10px; border: 1px solid #eee; text-align: center; }
        .back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background: #0054a6; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<div class="card">
    <a href="dashboard.php" class="back-btn">⬅ Kembali ke Dashboard</a>
    <h2>🚀 Hasil Proyeksi Omzet 3 Tahun (2025-2027)</h2>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan / Tahun</th>
                <th>Sektor Ekraf</th>
                <th>Prediksi Omzet</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('F Y', strtotime($row['tanggal'])); ?></td>
                    <td><?= $row['jenis_ekraf']; ?></td>
                    <td><strong>Rp <?= number_format($row['prediksi'], 0, ',', '.'); ?></strong></td>
                </tr>
            <?php } 
            } else {
                echo "<tr><td colspan='4'>Data belum tersedia. Silakan jalankan proses AI.</td></tr>";
            } ?>
        </tbody>
    </table>
</div>

</body>
</html>