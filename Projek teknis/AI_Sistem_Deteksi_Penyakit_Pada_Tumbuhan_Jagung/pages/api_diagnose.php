<?php
// pages/api_diagnose.php - Endpoint AJAX untuk Perhitungan CF
// PENTING: Menonaktifkan Notice dan Warning agar tidak merusak output JSON
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); 

header('Content-Type: application/json');


require_once '../core/CertaintyFactor.php';

$response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gejala_data'])) {
    $gejala_data_json = $_POST['gejala_data'];
    $gejala_data = json_decode($gejala_data_json, true);

    if (is_array($gejala_data) && !empty($gejala_data)) {
        try {
            $sistem = new CertaintyFactor();
            
            // Format input_pengguna yang dibutuhkan oleh method diagnosa
            $input_for_cf = [];
            foreach($gejala_data as $kode => $bobot) {
                // Pastikan bobot adalah float dan > 0
                $bobot_float = floatval($bobot);
                if ($bobot_float > 0) {
                     $input_for_cf[] = [$kode, $bobot_float];
                }
            }

            // Panggil diagnosa, yang sekarang mengembalikan array dengan 'diagnosa_list' dan 'riwayat'
            $results = $sistem->diagnosa($input_for_cf);

            $response = [
                'status' => 'success',
                'message' => 'Diagnosa berhasil diproses.',
                'results' => [
                    'diagnosa_list' => $results['diagnosa_list'],
                    'riwayat_perhitungan' => $results['riwayat'], 
                    'penyakit_list' => $sistem->getPenyakit(),
                    'gejala_master' => $sistem->getGejala(),
                ]
            ];

        } catch (Exception $e) {
            $response = [
                'status' => 'error', 
                'message' => 'Terjadi kesalahan saat memproses diagnosa: ' . $e->getMessage()
            ];
        }

    }
}

echo json_encode($response);
?>