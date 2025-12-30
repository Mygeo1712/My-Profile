import pandas as pd

def cari_rekomendasi(lokasi, iklim, musim):
    try:
        df = pd.read_csv("data_bisnis.csv")
        df = df.apply(lambda x: x.astype(str).str.lower())

        lokasi = lokasi.lower().strip()
        iklim = iklim.lower().strip()
        musim = musim.lower().strip()

        hasil = df[df['lokasi'] == lokasi]

        if hasil.empty:
            return pd.DataFrame({"pesan": ["Maaf, tidak ada rekomendasi yang cocok."]})

        if lokasi == "gunung":
            if iklim == "subtropis" or musim in ["hujan", "kemarau"]:
                hasil = hasil[hasil['bisnis'].isin(["jagung bakar", "minuman jahe", "bakso"])]
        elif lokasi == "pantai":
            if musim == "kemarau":
                hasil = hasil[hasil['bisnis'].isin(["es kelapa muda", "rujak buah"])]
            else:
                hasil = hasil[hasil['bisnis'].isin(["bakso", "kopi susu"])]
        elif lokasi == "kota":
            if musim in ["hujan", "kemarau"]:
                hasil = hasil[hasil['bisnis'].isin(["gorengan", "bakso", "kopi susu"])]
            else:
                hasil = hasil[hasil['bisnis'].isin(["rujak buah", "es kelapa muda", "sate ayam"])]

        if hasil.empty:
            hasil = df[
                (df['lokasi'] == lokasi) & ((df['musim'] == musim) | (df['musim'] == 'semua'))
            ]

        return hasil if not hasil.empty else pd.DataFrame({
            "pesan": ["Maaf, tidak ada rekomendasi yang cocok untuk kondisi tersebut."]
        })

    except Exception as e:
        print(f"Terjadi kesalahan: {e}")
        return pd.DataFrame()
