<?php
// core/CertaintyFactor.php - Mesin Inferensi Certainty Factor

class CertaintyFactor {
    
    // Solusi data Dihapus

    // Definisi Penyakit (Statik - HANYA NAMA, DESKRIPSI, DAN SOLUSI)
    private $penyakit = [
        "P01" => [
            "nama" => "Hawar Daun (Leaf Blight)",
            "deskripsi" => "Penyakit yang disebabkan oleh jamur Exserohilum turcicum. Ditandai dengan bercak memanjang berwarna coklat kehijauan pada daun. Infeksi parah menyebabkan daun mengering dan mati sebelum waktunya, mengurangi fotosintesis.",
            "solusi" => "Gunakan varietas jagung yang tahan terhadap hawar daun. Lakukan rotasi tanaman dan gunakan fungisida berbahan aktif mankozeb atau klorotalonil jika infeksi sudah parah."
        ],
        "P02" => [
            "nama" => "Busuk Pelepah (Sheath Rot)",
            "deskripsi" => "Disebabkan oleh bakteri atau jamur, ditandai dengan pelepah daun yang busuk, lunak, dan berwarna coklat gelap hingga hitam. Biasanya terjadi di kondisi lembab dan dapat menyebar ke ruas batang.",
            "solusi" => "Lakukan sanitasi lahan dengan membuang sisa tanaman yang terinfeksi. Hindari kelembaban tinggi (drainase baik). Aplikasikan bakterisida/fungisida berbasis tembaga pada awal gejala."
        ],
        "P03" => [
            "nama" => "Bulai (Downy Corn)",
            "deskripsi" => "Penyakit virus yang sangat merusak, ditandai dengan garis-garis klorotik kuning atau putih pada daun termuda. Tanaman terinfeksi biasanya kerdil dan tidak menghasilkan tongkol.",
            "solusi" => "Gunakan benih yang sudah diberi perlakuan fungisida sistemik (misal metalaksil) sebelum tanam. Cabut dan musnahkan tanaman yang menunjukkan gejala bulai sejak dini (eradikasi)."
        ],
        "P04" => [
            "nama" => "Karat Daun (Leaf Rust)",
            "deskripsi" => "Disebabkan jamur Puccinia, ditandai dengan bintik kecil berwarna coklat kemerahan seperti karat yang tersebar di permukaan daun. Jika digosok, akan meninggalkan serbuk jingga kecoklatan.",
            "solusi" => "Pilih varietas jagung yang resisten. Kontrol gulma di sekitar pertanaman. Gunakan fungisida yang mengandung azoxystrobin atau propiconazole pada tingkat infeksi sedang."
        ],
        "P05" => [
            "nama" => "Bercak Daun (Leaf Spot)",
            "deskripsi" => "Ditandai dengan bercak kecil berbentuk bulat hingga lonjong pada daun. Pusat bercak seringkali berwarna putih abu-abu dengan tepi coklat atau ungu. Mengurangi kemampuan fotosintesis tanaman.",
            "solusi" => "Rotasi tanaman dan pengelolaan residu yang baik untuk mengurangi inokulum jamur. Aplikasikan fungisida secara preventif jika kondisi lingkungan mendukung perkembangan penyakit."
        ],
        "P06" => [
            "nama" => "Busuk Batang (Stem Rot)",
            "deskripsi" => "Serangan jamur atau bakteri yang menyebabkan bagian bawah batang membusuk, lunak, dan berwarna coklat atau hitam. Gejala lanjut menyebabkan batang mudah roboh (lodging).",
            "solusi" => "Perbaiki drainase lahan. Hindari kepadatan tanaman yang terlalu rapat. Penuhi nutrisi Kalium (K) yang cukup untuk memperkuat jaringan batang."
        ],
        "P07" => [
            "nama" => "Virus Mosaik Kerdil (Corn Dwarf Mosaic Virus)",
            "deskripsi" => "Penyakit yang disebabkan oleh virus (misalnya SCMV), ditularkan oleh kutu daun. Ditandai dengan daun menguning (klorosis), pola mosaik (belang hijau muda/tua), dan tanaman kerdil.",
            "solusi" => "Gunakan benih bebas virus. Kendalikan vektor (kutu daun) menggunakan insektisida. Lakukan eradikasi (pemusnahan) tanaman yang menunjukkan gejala virus."
        ]
    ];

    // Definisi Gejala, MB, dan MD (Statik)
    private $gejala = [
        "G01" => ["desc" => "Bercak kecil coklat/hijau kelabu pada daun", "mb" => 0.7, "md" => 0.3],
        "G02" => ["desc" => "Daun kering dan gugur lebih awal", "mb" => 0.8, "md" => 0.2],
        "G03" => ["desc" => "Daun berubah warna jadi kekuningan/coklat", "mb" => 0.9, "md" => 0.1],
        "G04" => ["desc" => "Bercak kecil berbentuk oval", "mb" => 0.6, "md" => 0.4],
        "G05" => ["desc" => "Pelepah daun busuk, lunak, coklat gelap/hitam", "mb" => 0.9, "md" => 0.1],
        "G06" => ["desc" => "Bau busuk di sekitar tanaman", "mb" => 0.7, "md" => 0.3],
        "G07" => ["desc" => "Infeksi menyebar ke ruas batang/tangkai", "mb" => 0.6, "md" => 0.4],
        "G08" => ["desc" => "Daun mengering, melipat atau layu", "mb" => 0.7, "md" => 0.3],
        "G09" => ["desc" => "Bercak kuning terang/oranye pada daun", "mb" => 0.8, "md" => 0.2],
        "G10" => ["desc" => "Infeksi pada tangkai daun dan batang", "mb" => 0.9, "md" => 0.1],
        "G11" => ["desc" => "Bintik gelap di sekitar lesi/daun terinfeksi", "mb" => 0.6, "md" => 0.4],
        "G12" => ["desc" => "Permukaan daun berwarna putih seperti tepung", "mb" => 0.8, "md" => 0.2],
        "G13" => ["desc" => "Bercak kuning, oranye atau coklat pada daun", "mb" => 0.4, "md" => 0.6],
        "G14" => ["desc" => "Penyebaran spora seperti serbuk kuning-oranye", "mb" => 0.7, "md" => 0.3],
        "G15" => ["desc" => "Tanaman tampak lebih lemah dan cepat mati", "mb" => 0.6, "md" => 0.4],
        "G16" => ["desc" => "Bintik hitam muncul di kedua sisi daun", "mb" => 0.7, "md" => 0.3],
        "G17" => ["desc" => "Warna bervariasi (hitam, coklat, kuning, merah)", "mb" => 0.8, "md" => 0.2],
        "G18" => ["desc" => "Konidia sedikit melengkung, ujung tumpul", "mb" => 0.9, "md" => 0.1],
        "G19" => ["desc" => "Bercak sedikit memanjang dan lebar", "mb" => 0.6, "md" => 0.4],
        "G20" => ["desc" => "Warna kuning pada tulang daun", "mb" => 0.7, "md" => 0.3],
        "G21" => ["desc" => "Batang lemah, mudah patah atau roboh", "mb" => 0.9, "md" => 0.1],
        "G22" => ["desc" => "Pangkal batang lunak, coklat atau hitam", "mb" => 0.6, "md" => 0.4],
        "G23" => ["desc" => "Pertumbuhan jamur putih/abu/hitam di pangkal batang", "mb" => 0.8, "md" => 0.2],
        "G24" => ["desc" => "Kulit luar batang menipis", "mb" => 0.7, "md" => 0.3],
        "G25" => ["desc" => "Daun belang hijau muda/kuning (mosaik)", "mb" => 0.6, "md" => 0.4],
        "G26" => ["desc" => "Tanaman kerdil", "mb" => 0.9, "md" => 0.1],
        "G27" => ["desc" => "Tanaman terlihat keriting atau mengerucut", "mb" => 0.7, "md" => 0.3],
        "G28" => ["desc" => "Kualitas biji buruk/susah", "mb" => 0.8, "md" => 0.2]
    ];

    // Mapping Rule (Statik - HARAP GANTI DENGAN QUERY DATABASE)
    private $rules = [
        "P01" => ["G01", "G02", "G03", "G04"],
        "P02" => ["G05", "G06", "G07", "G08"],
        "P03" => ["G09", "G10", "G11", "G12"],
        "P04" => ["G13", "G14", "G15", "G16"],
        "P05" => ["G17", "G18", "G19", "G20"],
        "P06" => ["G21", "G22", "G23", "G24"],
        "P07" => ["G25", "G26", "G27", "G28"] 
    ];

    public function getGejala() {
        return $this->gejala;
    }
    
    public function getPenyakit() {
        return $this->penyakit;
    }

    private function hitungCfPakar($kode_gejala) {
        if (isset($this->gejala[$kode_gejala])) {
            $g = $this->gejala[$kode_gejala];
            // CF Pakar = MB - MD
            return round($g['mb'] - $g['md'], 4); // Menggunakan 4 digit untuk presisi
        }
        return 0;
    }

    private function kombinasiCf($cf_old, $cf_new) {
        // CFcombine = CF1 + CF2 * (1 - CF1)
        return $cf_old + $cf_new * (1 - $cf_old);
    }

    public function diagnosa($input_pengguna) {
        $hasil_diagnosa = [];
        $riwayat_perhitungan = [];
        
        foreach ($this->rules as $kode_penyakit => $daftar_gejala_penyakit) {
            $cf_list_for_disease = [];
            $detail_gejala_ditemukan = []; 
            
            // 1. Menghitung CF Gejala
            foreach ($input_pengguna as $input_gejala) {
                list($gejala_user, $bobot_user) = $input_gejala;
                
                if (in_array($gejala_user, $daftar_gejala_penyakit)) {
                    $cf_pakar = $this->hitungCfPakar($gejala_user);
                    $cf_gejala = $cf_pakar * $bobot_user; 
                    
                    if ($cf_gejala > 0) {
                        $cf_list_for_disease[] = $cf_gejala;
                        // Simpan detail gejala dan CF yang digunakan
                        $detail_gejala_ditemukan[] = [
                            'kode' => $gejala_user,
                            'cf_pakar' => $cf_pakar,
                            'cf_user' => $bobot_user,
                            'cf_gejala' => $cf_gejala,
                        ];
                    }
                }
            }
            
            // 2. Mengombinasikan CF dan mencatat riwayat
            if (!empty($cf_list_for_disease)) {
                
                // Urutkan detail gejala berdasarkan CF Gejala tertinggi
                usort($detail_gejala_ditemukan, function($a, $b) {
                    return $b['cf_gejala'] <=> $a['cf_gejala'];
                });

                // Setelah diurutkan, ambil CF Gejala untuk kombinasi
                $cf_list_for_disease = array_column($detail_gejala_ditemukan, 'cf_gejala');

                $cf_combine = array_shift($cf_list_for_disease);
                $history = [];
                
                // Logika inisiasi untuk gejala pertama
                if (!empty($detail_gejala_ditemukan)) {
                    $history[] = [
                        'gabungan' => null, 
                        'hasil' => round($cf_combine, 4), 
                        'gejala' => $detail_gejala_ditemukan[0]['kode'],
                        'langkah' => 1 // Penanda langkah
                    ];
                }
                
                // Kombinasi untuk gejala berikutnya
                for ($i = 1; $i <= count($cf_list_for_disease); $i++) {
                    $cf_next = $cf_list_for_disease[$i - 1];
                    $cf_old = $cf_combine;
                    $cf_combine = $this->kombinasiCf($cf_old, $cf_next);
                    
                    $history[] = [
                        'gabungan' => round($cf_old, 4), 
                        'cf_baru' => round($cf_next, 4),
                        'hasil' => round($cf_combine, 4), 
                        'gejala' => $detail_gejala_ditemukan[$i]['kode'],
                        'langkah' => $i + 1 // Penanda langkah
                    ];
                }
                
                if ($cf_combine > 0) {
                    $hasil_diagnosa[$kode_penyakit] = round($cf_combine, 4);
                    
                    // Simpan riwayat perhitungan untuk penyakit ini
                    $riwayat_perhitungan[$kode_penyakit] = [
                        'gejala_terlibat' => $detail_gejala_ditemukan,
                        'history_cf' => $history
                    ];
                }
            }
        }
        
        arsort($hasil_diagnosa);
        
        // Kembalikan hasil utama DAN riwayat perhitungan
        return [
            'diagnosa_list' => $hasil_diagnosa,
            'riwayat' => $riwayat_perhitungan
        ];
    }
}
?>