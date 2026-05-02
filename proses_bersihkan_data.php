<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:admin.php?pesan=belum_login");
    exit;
}

// PERBAIKAN: Gunakan include agar settingan port 3306 di koneksi.php ikut terbawa
include 'koneksi.php'; 

// Jika kamu bersikeras menulis manual, gunakan ini (baris 9):
// $conn = mysqli_connect("127.0.0.1:3306", "root", "", "dispar");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Pemrosesan Data</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f4f8; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h3 { color: #0054a6; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-top: 20px; }
        pre { background: #272822; color: #f8f8f2; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 13px; }
        .table-container { margin-top: 20px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #0054a6; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn-selesai { display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: 600; }
    </style>
</head>
<body>

<div class="container">
    <h3>Log Pembersihan Data</h3>
    
    <?php
    if(isset($_POST['upload'])){
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $target_file = $target_dir . "data_mentah.csv";

        if (move_uploaded_file($_FILES["file_csv"]["tmp_name"], $target_file)) {
            // Jalankan Python
            $output = shell_exec("python clean_data.py 2>&1");
            echo "<pre>$output</pre>";

            echo "<h3>Data Hasil Pembersihan (Database)</h3>";
            echo "<div class='table-container'>";
            
            // PERBAIKAN: Pastikan variabel koneksi adalah $conn (sesuai isi koneksi.php)
            $query = mysqli_query($conn, "SELECT * FROM data_omzet_bersih ORDER BY periode DESC, jenis_ekraf ASC LIMIT 5");
            
            if ($query && mysqli_num_rows($query) > 0) {
                echo "<table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Ekraf</th>
                                <th>Periode</th>
                                <th>Omzet (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>";
                $no = 1;
                while($row = mysqli_fetch_assoc($query)) {
                    $periode = !empty($row['periode']) ? date('F Y', strtotime($row['periode'])) : '-';
                    echo "<tr>
                            <td>".$no++."</td>
                            <td>".$row['jenis_ekraf']."</td>
                            <td>".$periode."</td>
                            <td>".number_format($row['omzet'], 0, ',', '.')."</td>
                          </tr>";
                }
                echo "</tbody></table>";
                echo "<p>*Menampilkan 5 data teratas.</p>";
            } else {
                echo "<p>Tidak ada data di tabel atau terjadi kesalahan: ".mysqli_error($conn)."</p>";
            }
            echo "</div>";

        } else {
            echo "<p style='color:red;'>Gagal mengunggah file.</p>";
        }
    }
    ?>
    
    <a href="tambah_data_aktual.php" class="btn-selesai">KEMBALI KE HALAMAN UTAMA</a>
</div>

</body>
</html>