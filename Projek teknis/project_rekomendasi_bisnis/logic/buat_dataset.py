import pandas as pd

data = {
    "bisnis": [
        "Es Kelapa Muda", "Jagung Bakar", "Sate Ayam", "Kopi Susu", "Bakso", "Minuman Jahe", "Gorengan", "Rujak Buah",
        "Es Teh Manis", "Soto Ayam", "Wedang Jahe", "Smoothie Buah"
    ],
    "lokasi": [
        "pantai", "gunung", "kota", "kota", "pantai", "gunung", "gunung", "kota",
        "gunung", "kota", "gunung", "gunung"
    ],
    "iklim": [
        "tropis", "subtropis", "tropis", "tropis", "tropis", "subtropis", "subtropis", "tropis",
        "tropis", "tropis", "subtropis", "tropis"
    ],
    "musim": [
        "kemarau", "kemarau", "semua", "semua", "kemarau", "kemarau", "hujan", "hujan",
        "kemarau", "hujan", "kemarau", "kemarau"
    ],
    "deskripsi": [
        "Minuman yang menyegarkan untuk dinikmati di cuaca panas di daerah pantai.",
        "Makanan yang hangat dan cocok untuk udara subtropis pegunungan.",
        "Makanan yang nikmat disantap dengan nasi hangat di lingkungan kota.",
        "Minuman yang laris di tempat wisata tropis sepanjang tahun.",
        "Makanan yang populer jadi santapan di tempat yang panas dan lembap.",
        "Minuman hangat yang sangat pas diminum saat cuaca subtropis di gunung.",
        "Cemilan yang digemari saat sore hari terutama di daerah gunung.",
        "Cemilan segar yang cocok dinikmati di suasana tropis kota.",
        "Minuman segar yang cocok dikonsumsi di gunung saat cuaca panas.",
        "Makanan hangat berkuah yang cocok saat musim hujan di kota.",
        "Minuman tradisional hangat untuk cuaca kemarau di pegunungan.",
        "Minuman buah yang menyegarkan untuk kondisi tropis panas."
    ]
}

df = pd.DataFrame(data)
df.to_csv("data_bisnis.csv", index=False)
print("Dataset berhasil dibuat.")
