import pandas as pd
import mysql.connector
import sys
import os

db = None
cursor = None

try:
    path_file = "uploads/data_mentah.csv"
    if not os.path.exists(path_file):
        print(f"ERROR: File {path_file} tidak ditemukan!")
        sys.exit()

    # =========================
    # 1. LOAD DATA
    # =========================
    df = pd.read_csv(path_file)

    # =========================
    # 2. BERSIHKAN KOLOM
    # =========================
    df.columns = df.columns.str.strip().str.upper()
    print("Kolom dataset:", df.columns.tolist())

    # =========================
    # 3. FILTER TAHUN
    # =========================
    df = df[df["TAHUN"].between(2021, 2024)]

    # =========================
    # 4. NORMALISASI JENIS EKRAF
    # =========================
    df['JENIS EKRAF'] = df['JENIS EKRAF'].astype(str)
    df['JENIS EKRAF'] = df['JENIS EKRAF'].replace('nan', pd.NA)
    df = df.dropna(subset=['JENIS EKRAF'])

    df['JENIS EKRAF'] = (
        df['JENIS EKRAF']
        .str.strip()
        .str.upper()
        .str.replace(r'\s+', ' ', regex=True)
    )

    mapping_ekraf = {
        'PERCETKAN': 'PERCETAKAN',
        'FASHON': 'FASHION'
    }
    df['JENIS EKRAF'] = df['JENIS EKRAF'].replace(mapping_ekraf)

    # =========================
    # 5. FIX TIPE DATA
    # =========================
    df['TAHUN'] = df['TAHUN'].astype(float).astype(int)

    # =========================
    # 6. MAP BULAN → ANGKA
    # =========================
    bulan_map = {
        'JANUARI':1,'FEBRUARI':2,'MARET':3,'APRIL':4,
        'MEI':5,'JUNI':6,'JULI':7,'AGUSTUS':8,
        'SEPTEMBER':9,'OKTOBER':10,'NOVEMBER':11,'DESEMBER':12
    }

    def map_bulan(x):
        x = str(x).upper()
        return bulan_map.get(x, None)

    df['bulan_angka'] = df['BULAN'].apply(map_bulan)

    # hapus bulan tidak valid
    df = df.dropna(subset=['bulan_angka'])

    # =========================
    # 7. BUAT PERIODE
    # =========================
    df['periode'] = pd.to_datetime(
        df['TAHUN'].astype(str) + '-' +
        df['bulan_angka'].astype(int).astype(str).str.zfill(2) + '-01',
        format='%Y-%m-%d'
    )

    # =========================
    # 8. AGREGASI
    # =========================
    df_agregat = df.groupby(
        ['JENIS EKRAF','periode']
    )['OMZET'].sum().reset_index()

    # =========================
    # 9. REINDEX (LENGKAPI BULAN)
    # =========================
    all_periods = pd.date_range(
        start=df_agregat['periode'].min(),
        end=df_agregat['periode'].max(),
        freq='MS'
    )

    full_data = []

    for jenis in df_agregat['JENIS EKRAF'].unique():
        temp = df_agregat[df_agregat['JENIS EKRAF'] == jenis].set_index('periode')
        temp = temp.reindex(all_periods)
        temp['OMZET'] = temp['OMZET'].fillna(0)
        temp['JENIS EKRAF'] = jenis
        full_data.append(temp.reset_index().rename(columns={'index': 'periode'}))

    df_agregat = pd.concat(full_data)

    print("Total data setelah reindex:", len(df_agregat))

    # =========================
    # 10. SIMPAN KE DATABASE
    # =========================
    db = mysql.connector.connect(
        host="127.0.0.1",
        user="root",
        password="",
        database="dispar",
        port=3306
    )
    cursor = db.cursor()

    cursor.execute("TRUNCATE TABLE data_omzet_bersih")

    sql = "INSERT INTO data_omzet_bersih (jenis_ekraf, periode, omzet) VALUES (%s, %s, %s)"

    data_to_insert = [
        (
            row['JENIS EKRAF'],
            row['periode'].strftime('%Y-%m-%d'),
            float(row['OMZET'])
        )
        for _, row in df_agregat.iterrows()
    ]

    cursor.executemany(sql, data_to_insert)
    db.commit()

    print(f"SUKSES: {len(df_agregat)} baris masuk database!")

except Exception as e:
    print(f"ERROR: {str(e)}")

finally:
    if cursor: cursor.close()
    if db: db.close()