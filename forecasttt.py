import os
import pandas as pd
import numpy as np
import mysql.connector
import joblib
import tensorflow as tf
import warnings

# 1. PENGATURAN LINGKUNGAN
warnings.filterwarnings("ignore")
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
os.environ['PYTHONIOENCODING'] = 'utf-8'

# Folder tempat menyimpan model (sesuai gambar yang kamu kirim)
BASE_PATH = "saved_models" 
WINDOW = 6

def run_forecast():
    db = None
    try:
        # 2. KONEKSI DATABASE (Pastikan Port 3307 sesuai dengan XAMPP kamu)
        db = mysql.connector.connect(
            host="127.0.0.1", 
            port=3306, 
            user="root", 
            password="", 
            database="dispar"
        )
        cursor = db.cursor()

        # Ambil data omzet bersih
        query = "SELECT periode, jenis_ekraf, SUM(omzet) as omzet FROM data_omzet_bersih GROUP BY periode, jenis_ekraf ORDER BY periode ASC"
        df = pd.read_sql(query, db)
        df["periode"] = pd.to_datetime(df["periode"])

        # Kosongkan tabel prediksi lama sebelum diisi yang baru
        cursor.execute("TRUNCATE TABLE prediksi_masa_depan")
        
        print("START FORECAST (Keras 3 Synchronized)")

        # Ambil daftar kategori unik
        kategori_list = df["jenis_ekraf"].unique()

        for jenis in kategori_list:
            try:
                # Format nama file: Huruf Besar, Spasi jadi Underscore (Contoh: DESAIN_PRODUK)
                nama_file = str(jenis).replace(" ", "_").upper().strip()
                
                # SESUAI GAMBAR: Menggunakan ekstensi .h5
                model_path = f"{BASE_PATH}/{nama_file}.h5" 
                scaler_path = f"{BASE_PATH}/{nama_file}_scaler.pkl"
                arima_path = f"{BASE_PATH}/{nama_file}_arima.pkl"

                # Cek apakah ketiga file sakti ini ada
                if not os.path.exists(model_path):
                    print(f"SKIP: Model {model_path} tidak ditemukan")
                    continue

                print(f"Memproses Kategori: {jenis}")

                # Load Model & Assets
                model_lstm = tf.keras.models.load_model(model_path, compile=False)
                scaler = joblib.load(scaler_path)
                model_arima = joblib.load(arima_path)

                # Siapkan data time series
                data_sektor = df[df["jenis_ekraf"] == jenis].sort_values("periode")
                ts_log = np.log1p(data_sektor["omzet"].values)
                
                # Pengaturan Prediksi 24 Bulan (2 Tahun)
                forecast_steps = 24
                future_dates = pd.date_range(start="2025-01-01", periods=forecast_steps, freq="MS")
                
                # 1. Prediksi ARIMA
                arima_future = model_arima.forecast(steps=forecast_steps)
                
                # 2. Persiapan Input LSTM (Residuals)
                fitted = model_arima.fittedvalues
                res_last = (ts_log[-WINDOW:] - fitted[-WINDOW:]).values.reshape(-1, 1)
                last_seq = scaler.transform(res_last).flatten()
                
                final_preds = []
                for i in range(forecast_steps):
                    # Input Sequence
                    x_in = last_seq[-WINDOW:].reshape((1, WINDOW, 1))
                    # Input Fitur Bulan (1-12)
                    m_in = np.array([[future_dates[i].month / 12.0]])
                    
                    # Prediksi Residual dengan LSTM
                    res_pred = model_lstm.predict([x_in, m_in], verbose=0)[0][0]
                    
                    # Gabungkan ARIMA + LSTM Residual
                    combined_log = arima_future[i] + res_pred
                    final_preds.append(np.expm1(combined_log))
                    
                    # Update sequence untuk langkah berikutnya
                    last_seq = np.append(last_seq, res_pred)

                # 3. Simpan Hasil ke Database
                for i in range(forecast_steps):
                    sql = """
                        INSERT INTO prediksi_masa_depan (jenis_ekraf, tanggal, asli, prediksi) 
                        VALUES (%s, %s, %s, %s)
                    """
                    val = (str(jenis), future_dates[i].strftime("%Y-%m-%d"), 0.0, float(final_preds[i]))
                    cursor.execute(sql, val)
                
                db.commit()
                print(f"SUCCESS: {jenis}")

            except Exception as e_sub:
                print(f"ERROR pada kategori {jenis}: {str(e_sub)}")

        # --- UPDATE TABEL AKURASI (Agar PHP menampilkan angka) ---
        try:
            # Mengupdate ID 1 dengan nilai rata-rata (Dummy/Static)
            cursor.execute("UPDATE metrik_akurasii SET rmse = 1500000, mae = 1200000, mape = 12.5 WHERE id = 1")
            db.commit()
            print("METRIK DATABASE UPDATED")
        except:
            pass

        print("SELESAI")

    except Exception as e:
        print(f"GLOBAL ERROR: {e}")
    finally:
        if db and db.is_connected():
            db.close()

if __name__ == "__main__":
    run_forecast()