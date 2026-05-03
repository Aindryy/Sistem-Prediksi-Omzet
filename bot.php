<?php
// --- 1. PENGATURAN API & DATABASE ---
$token_telegram = ""; 
$api_key_groq   = ""; 
$nama_database  = "dispar"; 

// --- 2. TANGKAP PESAN DARI TELEGRAM ---
$content = file_get_contents("php://input");
$update  = json_decode($content, true);

if (isset($update["message"])) {
    $chat_id   = $update["message"]["chat"]["id"];
    $text_user = $update["message"]["text"];

    // --- 3. KONEKSI KE DATABASE (PORT 3307) ---
    $conn = mysqli_connect("127.0.0.1:3306", "root", "", $nama_database);
    
    if (!$conn) {
        $balasan = "❌ Koneksi Database Gagal. Pastikan MySQL di XAMPP port 3307 sudah aktif.";
    } else {
        // Atau ambil 10 data terbaru agar AI punya pilihan konteks yang luas
        $bulan_sekarang = date('Y-m'); 
        $query = "SELECT jenis_ekraf, prediksi, tanggal FROM prediksi_masa_depan WHERE tanggal LIKE '$bulan_sekarang%' ORDER BY prediksi DESC";
        $result = mysqli_query($conn, $query);
        
        $daftar_prediksi = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $format_uang = "Rp " . number_format($row['prediksi'], 0, ',', '.');
                $daftar_prediksi[] = "- Sektor {$row['jenis_ekraf']}: $format_uang (Periode: " . date('F Y', strtotime($row['tanggal'])) . ")";
            }
            $info_data = "Berikut data prediksi omzet bulan ini:\n" . implode("\n", $daftar_prediksi);
        } else {
            // Jika data bulan ini tidak ada, ambil 5 data terakhir secara umum agar AI tetap punya bahan
            $query_backup = "SELECT jenis_ekraf, prediksi, tanggal FROM prediksi_masa_depan ORDER BY id DESC LIMIT 5";
            $res_backup = mysqli_query($conn, $query_backup);
            while ($r = mysqli_fetch_assoc($res_backup)) {
                $daftar_prediksi[] = "- {$r['jenis_ekraf']}: Rp" . number_format($r['prediksi'], 0, ',', '.') . " (" . date('F Y', strtotime($r['tanggal'])) . ")";
            }
            $info_data = "Data bulan ini belum tersedia. Berikut referensi data terbaru:\n" . implode("\n", $daftar_prediksi);
        }

        // --- 4. LOGIKA RESPONS BOT ---
        if ($text_user == "/start") {
        $balasan = "<b>Halo! Asisten AI Dinas Pariwisata Jeneponto siap membantu.</b>\n\nSaya bisa memberikan analisis omzet berbagai sektor ekonomi kreatif berdasarkan data prediksi terbaru.";
        } else {
            // Berikan instruksi agar AI menganalisis SEMUA data yang diberikan, bukan cuma satu
            $prompt = "Kamu adalah asisten ahli. Ini adalah daftar data prediksi omzet beberapa sektor ekraf di Jeneponto:\n$info_data\n\n" .
                    "Pertanyaan User: '$text_user'\n\n" .
                    "Tugasmu: Jawab secara spesifik berdasarkan data di atas. Jika user bertanya tentang sektor tertentu, cari di daftar tersebut. Gunakan format tebal (<b>) untuk angka dan list (-) untuk poin-poin.";
            
            $balasan = tanyaGroq($prompt, $api_key_groq);
        }
    }

    // --- 5. KIRIM BALIK KE TELEGRAM ---
    $url = "https://api.telegram.org/bot$token_telegram/sendMessage?chat_id=$chat_id&text=" . urlencode($balasan) . "&parse_mode=HTML";
    file_get_contents($url);
}

// --- FUNGSI UNTUK BERTANYA KE GROQ AI ---
function tanyaGroq($pesan, $key) {
    $data = [
        "model" => "llama-3.1-8b-instant", 
        "messages" => [
            [
                "role" => "system", 
                "content" => "Kamu adalah asisten cerdas untuk Dinas Pariwisata Jeneponto. Gunakan data yang diberikan untuk memberikan saran strategis yang mendukung UMKM dan ekonomi kreatif."
            ],
            [
                "role" => "user", 
                "content" => $pesan
            ]
        ]
    ];

    $ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json", 
        "Authorization: Bearer " . trim($key)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    // Penting untuk XAMPP: Bypass SSL agar tidak error saat panggil API Groq
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $res_arr = json_decode($response, true);

    if (isset($res_arr['choices'][0]['message']['content'])) {
        return $res_arr['choices'][0]['message']['content'];
    } else {
        return "❌ Maaf, layanan AI sedang mengalami gangguan (HTTP $httpCode). Silakan coba lagi nanti.";
    }
}
?>
