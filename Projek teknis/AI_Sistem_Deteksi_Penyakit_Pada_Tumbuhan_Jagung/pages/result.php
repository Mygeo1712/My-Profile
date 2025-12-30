<?php
    // pages/result.php - Halaman Laporan Hasil Diagnosis
    $is_page_root = false;
    $page_title   = "Laporan Diagnosa";

  
    require_once '../core/CertaintyFactor.php';

    require_once '../template/header.php';
?>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0">
                <div class="result-header">
                    <h3 class="mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Diagnosa Resmi (Metode Certainty Factor)</h3>
                </div>
                <div class="card-body p-4">
                    <div id="result-content">
                        <div class="text-center py-5">
                            <h4 class="text-muted"><i class="bi bi-arrow-clockwise spinner-spinner-border me-2"></i> Memuat Laporan Diagnosa...</h4>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end bg-light">

                    <a href="diagnose.php" class="btn btn-primary"><i class="bi bi-arrow-repeat"></i> Mulai Penilaian Baru</a>
                </div>
            </div>
        </div>
    </div>

<?php
    require_once '../template/footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const resultContent = document.getElementById('result-content');
        const storedResult = sessionStorage.getItem('diagnosis_result');
        const storedSelectedGejala = sessionStorage.getItem('selected_gejala');
        const AMBANG_BATAS_MINIMAL = 25.00; // Ambang batas minimum 25%

        // Pilihan CF untuk deskripsi
        const CF_OPTIONS = {
            1.0: "Pasti YA (100%)", 0.8: "Hampir Pasti YA (80%)", 0.6: "Kemungkinan Besar YA (60%)", 0.4: "Mungkin YA (40%)", 0.2: "Tidak Tahu (20%)", 0.0: "Mungkin TIDAK (0%)"
        };

        // --- FUNGSI GENERATE TABEL DETAIL CF ---
        function generateCfDetailTable(riwayat, namaPenyakit, persenAkhir, gejalaMaster) {
            const gejalaTerlibat = riwayat.gejala_terlibat;
            const historyCf = riwayat.history_cf;
            let html = `
                <hr class="my-4">
                <h4 class="text-dark mt-3 mb-3"><i class="bi bi-calculator me-2 text-warning"></i> Detail Perhitungan (${namaPenyakit})</h4>

                <p class="fw-bold mb-2">1. Nilai CF Gejala</p>

                <div class="alert alert-light p-3 small">
                    CF Gejala = CF Pakar x CF User
                </div>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered table-striped">
                        <thead class="table-warning">
                            <tr>
                                <th>Kode</th>
                                <th>Deskripsi Gejala</th>
                                <th>CF Pakar</th>
                                <th>CF User</th>
                                <th>CF Gejala (CF Pakar x CF User)</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            gejalaTerlibat.forEach(g => {
                const cfDesc = CF_OPTIONS[g.cf_user] || g.cf_user.toFixed(2);
                const descGejala = gejalaMaster[g.kode] ? gejalaMaster[g.kode].desc : 'Deskripsi tidak ditemukan';

                html += `
                            <tr>
                                <td>${g.kode}</td>
                                <td class="small">${descGejala}</td>
                                <td>${g.cf_pakar.toFixed(4)}</td>
                                <td>${cfDesc}</td>
                                <td class="fw-bold">${g.cf_gejala.toFixed(4)}</td>
                            </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>

                <p class="fw-bold mb-2">2. Kombinasi CF</p>
                <div class="alert alert-light p-3 small">
                    CF Gabungan = CF lama + CF baru (1 - CF lama)
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>Langkah</th>
                                <th>Gejala</th>
                                <th>Langkah Substitusi</th> <th>CF Gabungan</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            historyCf.forEach((h, index) => {
                let cfLama = h.gabungan !== null ? h.gabungan.toFixed(4) : '-';
                let cfBaru = h.cf_baru !== undefined ? h.cf_baru.toFixed(4) : h.hasil.toFixed(4); // CF awal

                let substitusiText;
                if (h.gabungan === null) {
                    // Kasus Gejala Awal
                    substitusiText = `${h.gejala} (CF Awal)`;
                } else {
                    // Kasus Kombinasi: CF_lama + CF_baru * (1 - CF_lama)
                    const sisa = (1 - h.gabungan).toFixed(4);
                    // Menghilangkan trailing zeros untuk tampilan yang lebih rapi
                    substitusiText = `${cfLama} + ${cfBaru} x (1 - ${cfLama}) = ${cfLama} + ${cfBaru} x ${sisa}`;
                }


                html += `
                            <tr>
                                <td>${h.langkah}</td>
                                <td>${h.gejala}</td>
                                <td>${substitusiText}</td> <td class="fw-bold">${h.hasil.toFixed(4)}</td>
                            </tr>
                `;
            });

            html += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">CF Akhir (%)</td>
                                <td class="fw-bold text-success fs-5">${persenAkhir}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;

            return html;
        }
        // --- AKHIR FUNGSI GENERATE TABEL DETAIL CF ---


        if (storedResult) {
            try {
                const data = JSON.parse(storedResult);
                // Tambahkan pengecekan untuk storedSelectedGejala di sini
                const selectedGejalaData = storedSelectedGejala ? JSON.parse(storedSelectedGejala) : {}; 
                
                const diagnosaList = data.diagnosa_list;
                const penyakitList = data.penyakit_list;
                const gejalaMaster = data.gejala_master;
                const riwayatPerhitungan = data.riwayat_perhitungan;

                // Urutkan berdasarkan CF tertinggi
                const sortedDiagnosa = Object.entries(diagnosaList).sort(([, cfA], [, cfB]) => cfB - cfA);

                let html = '';
                let kodeUtama = '';
                let namaUtama = '';
                let persenUtama = '';
                let isDiagnosisWeak = false; // Flag untuk diagnosis lemah

                // Ambil data penyakit utama
                if (sortedDiagnosa.length > 0) {
                    kodeUtama = sortedDiagnosa[0][0];
                    const cfUtama = sortedDiagnosa[0][1];
                    persenUtama = (cfUtama * 100).toFixed(2);

                    const detailUtama = penyakitList[kodeUtama] || {};
                    namaUtama = detailUtama.nama || 'Tidak Teridentifikasi';
                    deskripsiUtama = detailUtama.deskripsi || "Deskripsi tidak tersedia.";
                    solusiUtama = detailUtama.solusi || "Solusi tidak tersedia."; 
                } else {
                    persenUtama = 0.00;
                }

                // Cek apakah skor terlalu rendah
                if (sortedDiagnosa.length === 0 || parseFloat(persenUtama) < AMBANG_BATAS_MINIMAL) {
                    isDiagnosisWeak = true;
                }
                
                // Tentukan class warna berdasarkan kekuatan diagnosis
                const headerClass = isDiagnosisWeak ? 'alert-danger' : 'alert-success';
                const textColorClass = isDiagnosisWeak ? 'text-danger' : 'text-success';
                const iconClass = isDiagnosisWeak ? 'bi-exclamation-octagon-fill' : 'bi-patch-check-fill';
                const messageTitle = isDiagnosisWeak ? 'PERHATIAN: Keyakinan Rendah' : 'Hasil Diagnosa Utama';
                const messageText = isDiagnosisWeak ? 
                    `Diagnosis ini memiliki tingkat kepastian ${persenUtama}% (di bawah ambang batas 25.00%) dan **tidak disarankan** sebagai laporan resmi.` : 
                    `Tingkat keyakinan sistem mencapai ${persenUtama}% berdasarkan gejala yang dikonfirmasi.`;


                // --- Pengecekan Kritis: Gagal Diagnosis dan Tampilan Peringatan ---
                if (isDiagnosisWeak) {
                    const skorKegagalan = parseFloat(persenUtama);

                    resultContent.innerHTML = `
                        <div class="alert alert-danger text-center shadow-lg p-4">
                            <h4 class="fw-bold text-danger mb-3"><i class="bi bi-x-octagon-fill me-2"></i> PERINGATAN: DIAGNOSA TIDAK MEYAKINKAN</h4>
                            <p class="mb-2 lead">
                                Sistem Pakar tidak dapat mengidentifikasi penyakit dengan tingkat keyakinan yang memadai.
                            </p>
                            <h5 class="mt-4 text-start">Rekomendasi Tindakan:</h5>
                            <ul class="text-start list-unstyled">
                                <li><i class="bi bi-arrow-repeat me-1"></i> Mulai Penilaian baru dengan lebih banyak **Gejala Utama** yang relevan.</li>
                                
                               
                            </ul>
                            <div class="mt-4">
                                <a href="diagnose.php" class="btn btn-warning btn-lg"><i class="bi bi-arrow-repeat"></i> Mulai Penilaian Baru</a>
                            </div>
                        </div>
                    `;
                    return; // Hentikan proses rendering jika diagnosis lemah/nol
                }
                // --- Akhir Pengecekan Kritis ---


                // --- Lanjutkan Rendering Normal (Hanya jika isDiagnosisWeak = false) ---

                // Header Hasil Utama (Dinamis berdasarkan isDiagnosisWeak)
                html += `
                    <div class="alert ${headerClass} text-center mb-4 p-4 shadow-sm">
                        <h2 class="display-5 fw-bold ${textColorClass} mb-2"><i class="bi ${iconClass} me-3"></i> ${persenUtama}%</h2>
                        <h3 class="mb-0 text-dark">${messageTitle}: <span class="fw-bolder text-primary">${namaUtama}</span> (${kodeUtama})</h3>
                        <p class="text-muted mt-2 mb-0">${messageText}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 border-end border-2 pe-md-4">
                            <h4 class="text-dark mb-4"><i class="bi bi-info-circle me-2 text-primary"></i> Deskripsi & Solusi</h4>

                            <p class="fw-bold text-accent">Deskripsi Penyakit:</p>
                            <div class="card bg-light shadow-sm p-3 mb-4">
                                <p class="card-text">${deskripsiUtama}</p>
                            </div>

                            <p class="fw-bold text-accent">Rekomendasi Solusi:</p>
                            <div class="card bg-light shadow-sm p-3 mb-4">
                                <p class="card-text">${solusiUtama}</p>
                            </div>

                            <h4 class="text-dark mt-4 mb-3"><i class="bi bi-list-stars me-2 text-danger"></i> Kemungkinan Lain</h4>
                            <ul class="list-group list-group-flush mb-4">
                `;

                // Daftar Kemungkinan Lain
                const kemungkinanLain = sortedDiagnosa.slice(1).slice(0, 2);

                kemungkinanLain.forEach(([kode, cf]) => {
                    const nama = penyakitList[kode].nama;
                    const persen = (cf * 100).toFixed(2);
                    html += `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ${nama} (${kode})
                                    <span class="badge bg-secondary rounded-pill">${persen}%</span>
                                </li>
                    `;
                });

                if (kemungkinanLain.length === 0) {
                    html += `
                                <li class="list-group-item text-center text-muted">Tidak ada kemungkinan penyakit lain yang signifikan (>0% CF).</li>
                    `;
                }

                html += `
                            </ul>
                        </div>

                        <div class="col-md-6 ps-md-4">
                            <h4 class="text-dark mb-4"><i class="bi bi-list-task me-2 text-primary"></i> Gejala yang Dikonfirmasi</h4>
                            <div class="card bg-light shadow-sm mb-5">
                                <ul class="list-group list-group-flush">
                `;

                // Daftar Gejala Dikonfirmasi
                const gejalaTerpilih = Object.entries(selectedGejalaData).filter(([, cf]) => parseFloat(cf) > 0);

                gejalaTerpilih.forEach(([kode, cf]) => {
                    const desc = gejalaMaster[kode] ? gejalaMaster[kode].desc : kode;
                    const cfDesc = CF_OPTIONS[cf] || `${(parseFloat(cf) * 100).toFixed(0)}%`;
                    html += `
                                    <li class="list-group-item">
                                        <p class="mb-0 fw-bold">${kode}: ${desc}</p>
                                        <span class="badge bg-primary">${cfDesc}</span>
                                    </li>
                    `;
                });

                html += `
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div id="cf-detail-table-container"></div>
                        </div>
                    </div>
                `;

                resultContent.innerHTML = html;

                // TAMPILKAN DETAIL PERHITUNGAN CF
                const riwayat = riwayatPerhitungan[kodeUtama];

                if (riwayat) {
                    const cfDetailHtml = generateCfDetailTable(riwayat, namaUtama, persenUtama, gejalaMaster);
                    document.getElementById('cf-detail-table-container').innerHTML = cfDetailHtml;
                }

            } catch (e) {
                // --- PENANGANAN JSON PARSE ERROR DIGANTI PESAN LEBIH JELAS ---
                console.error("ERROR KRITIS: Gagal parse JSON dari SessionStorage.", e);
                
                resultContent.innerHTML = `
                    <div class="alert alert-danger text-center shadow-lg p-4">
                        <h4 class="fw-bold text-danger mb-3"><i class="bi bi-x-octagon-fill me-2"></i> KESALAHAN DATA SISTEM (PARSING GAGAL)</h4>
                        <p class="mb-2 lead">
                            Data diagnosis yang disimpan rusak atau tidak lengkap. Ini kemungkinan disebabkan oleh **PHP Warning/Error** yang merusak output server.
                        </p>
                        <h5 class="mt-4 text-start">Tindakan yang Perlu Dilakukan:</h5>
                        <ul class="text-start list-unstyled">
                            <li><i class="bi bi-dash-circle-fill text-danger me-1"></i> Bersihkan cache/session storage browser Anda (Wajib).</li>
                            <li><i class="bi bi-exclamation-circle-fill text-warning me-1"></i> Pastikan file **api_diagnose.php** memiliki <code>error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);</code> di baris atas.</li>
                        </ul>
                        <div class="mt-4">
                            <a href="diagnose.php" class="btn btn-warning btn-lg"><i class="bi bi-arrow-repeat"></i> Mulai Penilaian Baru</a>
                        </div>
                    </div>
                `;
                // --- AKHIR PENANGANAN JSON PARSE ERROR ---

            }

        } else {
            // Logika Data Sesi Hilang
            resultContent.innerHTML = `
                <div class="alert alert-warning text-center">
                    <h5><i class="bi bi-hourglass-split"></i> Data Sesi Hilang</h5>
                    <p>Silakan kembali ke halaman diagnosa untuk memulai penilaian baru.</p>
                    <div class="text-center mt-3"><a href="diagnose.php" class="btn btn-warning"><i class="bi bi-arrow-left"></i> Mulai Penilaian Baru</a></div>
                </div>
            `;
        }
    });
</script>