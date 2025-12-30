<?php
// index.php
$is_page_root = true; 
$page_title = "Dashboard"; 


require_once 'core/CertaintyFactor.php';
require_once 'template/header.php'; 
?>

 <div class="p-5 mb-5 bg-white shadow-lg hero-section-container">
        <div class="row align-items-center">
            
            <div class="col-md-7 py-4 scroll-reveal">
                <h1 class="display-5 fw-bolder text-accent"><i class="bi bi-journal-check me-3 text-primary"></i> Antarmuka Peluncuran Sistem Pakar</h1>
                
                <p class="fs-5 text-lead mt-4">
                    Platform canggih ini menggunakan **Metode Certainty Factor (CF)**—fitur inti dari sistem pakar—untuk memberikan penilaian yang tepat dan berkeyakinan tinggi terhadap patologi tanaman jagung berdasarkan gejala yang dilaporkan petani.
                </p>
                
                <p class="text-caption mt-3 mb-5"> 
                    Inisiasi sistem disarankan untuk segera dilakukan guna memitigasi risiko kegagalan panen yang disebabkan oleh serangan penyakit.
                </p>
                
                <a href="pages/diagnose.php" class="btn btn-primary-color btn-lg"><i class="bi bi-send-fill me-2"></i> Mulai Penilaian Resmi</a>
            </div>
            
            <div class="col-md-5 text-center scroll-reveal hero-image-box" style="transition-delay: 0.2s;">
                <img src="assets/images/Jagung.jpg" alt="Ilustrasi Tanaman Jagung" class="img-fluid hero-image">
            </div>
            
        </div>
    </div>
    
    <h2 class="text-center mb-5 mt-5 text-accent fw-bold fs-3">Pilar Sistem Kami</h2>

    <div class="row g-4 mt-4">
        
        <div class="col-md-4">
            <div class="card shadow-md h-100 scroll-reveal card-info">
                <div class="card-body">
                    <h3 class="card-title text-success fw-bold"><i class="bi bi-rulers me-2"></i> Metodologi CF</h3>
                    <p class="card-text text-secondary fw-light">Menggunakan model **Certainty Factor (CF)**, dirancang khusus untuk menganalisis dan mengukur data yang mengandung unsur ketidakpastian (inexact reasoning).</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-md h-100 scroll-reveal card-info" style="transition-delay: 0.1s;">
                <div class="card-body">
                    <h3 class="card-title text-primary fw-bold"><i class="bi bi-database-check me-2"></i> Basis Pengetahuan Pakar</h3>
                    <p class="card-text fw-normal">Diagnosis mencakup **7 penyakit utama** pada jagung. Semua aturan dan bobot (MB dan MD) divalidasi oleh pakar, membentuk pengetahuan inti sistem.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-md h-100 scroll-reveal card-info" style="transition-delay: 0.2s;">
                <div class="card-body">
                    <h3 class="card-title text-highlight fw-bold"><i class="bi bi-graph-up-arrow me-2"></i> Kinerja & Akurasi</h3>
                    <p class="card-text fw-medium">Sistem ini telah diuji dengan 28 kasus sampel dan menunjukkan tingkat akurasi mencapai **90%**, menjadikannya alat bantu pengambilan keputusan yang andal.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    } else {
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
require_once 'template/footer.php'; 
?>