<?php
// pages/diagnose.php - Halaman Input Gejala Interaktif
$is_page_root = false;
$page_title   = "Diagnosa Interaktif";


require_once '../core/CertaintyFactor.php';

$sistem      = new CertaintyFactor();
$gejala_list = $sistem->getGejala();
$gejala_json = json_encode($gejala_list);

// Opsi Keyakinan User (Tabel 3 Jurnal)
$bobot_user_options = [
    "1.0" => "Pasti YA (100%)",
    "0.8" => "Hampir Pasti YA (80%)",
    "0.6" => "Kemungkinan Besar YA (60%)",
    "0.4" => "Mungkin YA (40%)",
    "0.2" => "Tidak Tahu (20%)",
    "0.0" => "Mungkin TIDAK (0%)",
];
require_once '../template/header.php';
?>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-3 scroll-reveal"> 
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0 fs-4"><i class="bi bi-search me-2"></i> Pengumpulan Data Gejala</h3>
                </div>
                <div class="card-body p-4">

                    <div class="alert alert-info border-0 rounded-3 scroll-reveal" style="background-color: #e0f7fa;">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="fw-bold text-dark mb-1"><i class="bi bi-terminal me-2"></i> Protokol Penilaian:</h5>
                                <p class="mb-0 small">
                                    Masukkan **kata kunci deskriptif dan tepat** terkait kelainan visual pada tanaman. Sistem akan mencocokkan input Anda dengan **Basis Pengetahuan Pakar (G01-G28)**.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="badge bg-danger p-2 fs-6" id="gejala-count">0 Gejala Terdaftar</span>
                            </div>
                        </div>
                    </div>

                    <div class="guide-panel mb-4 p-3 border rounded-3 bg-light scroll-reveal" style="transition-delay: 0.1s;">
                        <p class="mb-2 fw-bold text-primary"><i class="bi bi-list-check me-1"></i> Kata Kunci Gejala yang Dikenali (Contoh):</p>
                        <ul class="list-inline mb-0 small text-muted">
                            <li class="list-inline-item">Bercak |</li>
                            <li class="list-inline-item">Busuk |</li>
                            <li class="list-inline-item">Kerdil |</li>
                            <li class="list-inline-item">Kuning |</li>
                            <li class="list-inline-item">Bintik |</li>
                            <li class="list-inline-item">Lunak |</li>
                            <li class="list-inline-item">Mosaik |</li>
                            <li class="list-inline-item">Spora</li>
                        </ul>
                    </div>

                    <div class="input-group mb-4 scroll-reveal" style="transition-delay: 0.2s;">
                        <input type="text" id="keyword-input" class="form-control form-control-lg border-0" placeholder="Masukkan kata kunci atau ketik 'SELESAI' untuk menyelesaikan diagnosa..." aria-label="Gejala Input">
                        <button class="btn btn-primary" type="button" id="search-btn"><i class="bi bi-search"></i> Cari</button>
                        <button class="btn btn-success" type="button" id="finish-btn" disabled><i class="bi bi-check-circle"></i> Finalisasi & Diagnosa</button>
                    </div>

                    <div class="console-log" id="output-area">
                        <p class="text-muted mb-0">Sistem siap. Menunggu input pertama...</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cfModal" tabindex="-1" aria-labelledby="cfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="cfModalLabel"><i class="bi bi-patch-question-fill me-2"></i> Penentuan Tingkat Keyakinan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <p class="fw-bold">Seberapa yakin Anda mengenai keberadaan gejala ini?</p>
                    <strong id="gejala-deskripsi-modal" class="text-primary fs-5"></strong>
                    
                    <div class="mt-3 d-flex flex-column gap-2">
                        <?php foreach ($bobot_user_options as $nilai => $deskripsi): ?>
                            <?php
                                // Logika penentuan warna tombol
                                $btn_class = 'btn-outline-info';
                                
                                if (floatval($nilai) == 1.0) {
                                    $btn_class = 'btn-success text-white';
                                } else if (floatval($nilai) == 0.8) {
                                    $btn_class = 'btn-primary text-white';
                                } else if (floatval($nilai) == 0.6) {
                                    $btn_class = 'btn-info text-dark';
                                } else if (floatval($nilai) == 0.4) {
                                    $btn_class = 'btn-warning text-dark';
                                } else if (floatval($nilai) == 0.2) {
                                    $btn_class = 'btn-outline-secondary';
                                } else if (floatval($nilai) == 0.0) {
                                    $btn_class = 'btn-danger text-white';
                                }
                            ?>
                            <button type="button" class="btn <?php echo $btn_class; ?> cf-select-btn" data-cf-value="<?php echo floatval($nilai); ?>">
                                <?php echo $deskripsi; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const GEJALA_DATA = <?php echo $gejala_json; ?>;
        const CF_OPTIONS = <?php echo json_encode($bobot_user_options); ?>;
    </script>
    <script src="../assets/js/diagnose_flow.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    } else {
                        // Logika untuk menghilangkan animasi saat di-scroll ke atas
                        entry.target.classList.remove('revealed'); 
                    }
                });
            }, {
                rootMargin: '0px',
                threshold: 0.1
            });

            document.querySelectorAll('.scroll-reveal').forEach(element => {
                observer.observe(element);
            });
        });
    </script>

<?php
require_once '../template/footer.php';
?>