<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:admin.php?pesan=belum_login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Data Aktual</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f4f8; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: center; }
        h2 { color: #0054a6; margin-bottom: 10px; }
        p { color: #666; font-size: 14px; margin-bottom: 30px; }
        
        .upload-area { border: 2px dashed #0054a6; padding: 30px; border-radius: 10px; background: #f9f9f9; margin-bottom: 20px; }
        input[type="file"] { margin-bottom: 20px; }
        
        .btn-proses { width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; font-family: 'Poppins'; }
        .btn-proses:hover { background-color: #218838; transform: translateY(-2px); }
        
        .btn-back { display: inline-block; margin-top: 20px; color: #0054a6; text-decoration: none; font-size: 14px; font-weight: 600; }
    </style>
</head>
<body>

<div class="container">
    <h2>Upload Data Aktual</h2>
    <p>Masukkan file CSV mentah untuk diproses & dibersihkan secara otomatis.</p>
    
    <div class="upload-area">
        <form action="proses_bersihkan_data.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file_csv" accept=".csv" required>
            <button type="submit" name="upload" class="btn-proses">✨ PROSES & BERSIHKAN DATA</button>
        </form>
    </div>
    
    <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
</div>

</body>
</html>