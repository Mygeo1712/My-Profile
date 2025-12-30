from flask import Flask, render_template, request
from logic.rekomendasi import cari_rekomendasi

app = Flask(__name__, template_folder='.')  # <-- Penting: titik berarti root folder

@app.route("/", methods=["GET", "POST"])
def index():
    hasil = None
    pesan = None
    if request.method == "POST":
        lokasi = request.form.get("lokasi")
        iklim = request.form.get("iklim")
        musim = request.form.get("musim")
        hasil_df = cari_rekomendasi(lokasi, iklim, musim)

        if 'pesan' in hasil_df.columns:
            pesan = hasil_df.iloc[0]['pesan']
        else:
            hasil = hasil_df

    return render_template("index.html", hasil=hasil, pesan=pesan)

if __name__ == "__main__":
    app.run(debug=True)
