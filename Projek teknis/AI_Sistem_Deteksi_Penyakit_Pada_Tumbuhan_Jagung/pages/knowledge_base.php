<?php
// pages/knowledge_base.php - Menampilkan Basis Pengetahuan Sistem Pakar

$is_page_root = false;
$page_title   = "Basis Pengetahuan"; 

// --- BLOK INISIALISASI PHP (Tetap Sama) ---

require_once '../core/CertaintyFactor.php';

$sistem       = new CertaintyFactor();
$penyakit_raw = $sistem->getPenyakit();
$gejala       = $sistem->getGejala();

$rules_reflection = new ReflectionProperty('CertaintyFactor', 'rules');
$rules_reflection->setAccessible(true);
$rules = $rules_reflection->getValue($sistem);

require_once '../template/header.php'; 
?>

<div class="p-4 mb-5 bg-white shadow-lg hero-section-container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card border-0 rounded-3 scroll-reveal"> 
                
               
                
                <div class="card-body p-4">

                    <h4 class="fw-bold text-primary mb-3"><i class="bi bi-tag me-2"></i> 7 Jenis Penyakit Jagung</h4>
                    <table class="table table-hover table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th style="width: 10%;">Kode</th>
                                <th>Nama Penyakit</th>
                                <th>Contoh Gejala Kunci</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($penyakit_raw as $kode => $detail_penyakit): 
                                $nama = $detail_penyakit['nama'];
                                $gejala_kunci_list = [];
                                
                                if (isset($rules[$kode])) {
                                    $kode_utama = array_slice($rules[$kode], 0, 2); 
                                    
                                    foreach ($kode_utama as $g_code) {
                                        if (isset($gejala[$g_code])) {
                                            $gejala_kunci_list[] = $gejala[$g_code]['desc'];
                                        }
                                    }
                                }
                                $gejala_output = implode('; ', $gejala_kunci_list);
                            ?>
                            <tr>
                                <td><?php echo $kode; ?></td>
                                <td class="fw-medium"><?php echo $nama; ?></td>
                                <td><?php echo $gejala_output ?: '— Data gejala tidak tersedia —'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <hr class="my-5">

                    <h4 class="fw-bold text-success mb-3"><i class="bi bi-list-columns me-2"></i> Gejala dan Bobot Certainty Factor (CF Pakar)</h4>
                    <p class="small text-muted">Nilai CF Pakar dihitung dari MB - MD.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-success">
                                <tr>
                                    <th style="width: 10%;">Kode</th>
                                    <th>Deskripsi Gejala</th>
                                    <th style="width: 10%;">MB</th>
                                    <th style="width: 10%;">MD</th>
                                    <th style="width: 10%;">CF Pakar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gejala as $kode => $detail): ?>
                                <?php $cf_pakar = $detail['mb'] - $detail['md']; ?>
                                <tr>
                                    <td><?php echo $kode; ?></td>
                                    <td><?php echo $detail['desc']; ?></td>
                                    <td><?php echo $detail['mb']; ?></td>
                                    <td><?php echo $detail['md']; ?></td>
                                    <td class="fw-bold text-primary"><?php echo number_format($cf_pakar, 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
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

        // Targetkan card utama dan tabel di dalamnya
        document.querySelectorAll('.scroll-reveal').forEach(element => {
            observer.observe(element);
        });
    });
</script>
<?php
require_once '../template/footer.php'; 
?>