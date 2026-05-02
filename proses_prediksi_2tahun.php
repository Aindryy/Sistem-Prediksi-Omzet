<?php
session_start();
set_time_limit(0); 

// 1. TENTUKAN PATH LENGKAP PYTHON (WAJIB PATH LENGKAP)
// Ganti alamat di bawah ini dengan hasil 'where python' kamu
$python_path = "C:/Users/ASUS/AppData/Local/Microsoft/WindowsApps/python.exe"; 

// 2. PATH SCRIPT
$script_path = __DIR__ . "/forecasttt.py";

// 3. JALANKAN PERINTAH
// Gunakan escapeshellarg agar path yang ada spasinya tidak error
$command = "$python_path \"$script_path\" 2>&1";
$output = shell_exec($command);

// Simpan log untuk kita intip kalau ada error
file_put_contents("debug_log.txt", "Waktu: " . date("Y-m-d H:i:s") . "\nOutput: " . $output . "\n---\n", FILE_APPEND);

// 4. CEK HASIL
if (strpos($output, 'SELESAI') !== false) {
    $_SESSION['sukses_prediksi'] = "✅ Analisis AI Berhasil Diperbarui!";
} else {
    // Tampilkan error aslinya agar kita tidak tebak-tebakan
    $_SESSION['error_prediksi'] = "❌ Gagal menjalankan script. <br><small>Detail: " . nl2br(htmlspecialchars($output)) . "</small>";
}

header("Location: hybrid_arimalstm2.php");
exit();