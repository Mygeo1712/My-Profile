// assets/js/diagnose_flow.js - Logika Interaktif Certainty Factor

// --- Global State Variables ---
let selectedGejala = {}; 
let selectedGejalaCode = null; 

// --- DOM Elements ---
// Deklarasi di sini, binding listener di DOMContentLoaded
const outputArea = document.getElementById('output-area');
const keywordInput = document.getElementById('keyword-input');
const searchBtn = document.getElementById('search-btn');
const finishBtn = document.getElementById('finish-btn');
const gejalaCountBadge = document.getElementById('gejala-count');
const cfModalElement = document.getElementById('cfModal');
const modalGejalaDesc = document.getElementById('gejala-deskripsi-modal');

// Variabel untuk instance Modal Bootstrap
let cfModal;


// --- UTILITY FUNCTIONS ---

function updateGejalaCount() {
    const count = Object.keys(selectedGejala).length;
    gejalaCountBadge.textContent = `${count} Gejala Terdaftar`;
    
    if (count > 0) {
        finishBtn.disabled = false;
        gejalaCountBadge.classList.replace('bg-danger', 'bg-success');
    } else {
        finishBtn.disabled = true;
        gejalaCountBadge.classList.replace('bg-success', 'bg-danger');
    }
}

function logMessage(message, type = 'info') {
    let icon = '';
    let colorClass = 'text-muted';

    if (type === 'error') {
        icon = '<i class="bi bi-x-circle-fill text-danger me-2"></i>';
        colorClass = 'text-error'; 
    } else if (type === 'success') {
        icon = '<i class="bi bi-check-circle-fill text-success me-2"></i>';
        colorClass = 'text-success';
    } else if (type === 'search') {
        icon = '<i class="bi bi-search text-primary me-2"></i>';
        colorClass = 'text-search';
    } else if (type === 'info') {
        icon = '<i class="bi bi-info-circle-fill text-info me-2"></i>';
        colorClass = 'text-info';
    }
    
    outputArea.innerHTML += `<p class="${colorClass} mb-1">${icon}${message}</p>`;
    outputArea.scrollTop = outputArea.scrollHeight; 
}

/**
 * Fungsi untuk membuat elemen HTML hasil pencarian yang dapat diklik
 */
function createSelectableGejalaHtml(results) {
    let resultHtml = '<div class="mt-3">';
    
    results.forEach((kode, index) => {
        const desc = GEJALA_DATA[kode].desc;
        const isSelected = selectedGejala.hasOwnProperty(kode);
        
        const cfValue = isSelected ? selectedGejala[kode] : 0;
        const cfDesc = isSelected ? CF_OPTIONS[cfValue] : ''; 

        resultHtml += `
            <div class="d-flex align-items-center mb-2">
                <button type="button" class="btn btn-sm btn-outline-primary select-gejala-btn flex-grow-1 text-start me-2" 
                        data-kode="${kode}" data-desc="${desc}" 
                        ${isSelected ? 'disabled' : ''}>
                    [${index + 1}] ${kode} - ${desc}
                </button>
                ${isSelected ? `<span class="badge bg-secondary">${cfDesc} (${(cfValue * 100).toFixed(0)}%)</span>` : ''}
            </div>`;
    });
    
    resultHtml += `<button type="button" class="btn btn-sm btn-outline-secondary" id="cancel-search-btn"><i class="bi bi-arrow-counterclockwise"></i> Reset Pencarian</button></div>`;
    
    outputArea.innerHTML += resultHtml;
    
    // Pasang listener untuk tombol-tombol yang baru dibuat
    document.querySelectorAll('.select-gejala-btn').forEach(btn => {
        btn.addEventListener('click', handleGejalaSelect);
    });
    
    document.getElementById('cancel-search-btn').addEventListener('click', () => {
        logMessage("Pencarian sebelumnya diabaikan. Masukkan kata kunci baru.", 'info');
    });
}


// --- CORE LOGIC: SEARCH AND SELECT ---

function searchGejala() {
    const keyword = keywordInput.value.trim().toLowerCase();
    
    if (keyword === '') return;

    if (keyword === 'selesai' || keyword === 'diagnosa') {
        if (Object.keys(selectedGejala).length > 0) {
            finishDiagnosis();
        } else {
             logMessage("(!) Masukkan minimal satu gejala sebelum menyelesaikan diagnosa.", 'error');
        }
        return;
    }
    
    outputArea.innerHTML = '';
    
    logMessage(`> Mencari kata kunci: '${keyword}'`, 'search');
    
    const results = Object.keys(GEJALA_DATA).filter(kode => {
        const desc = GEJALA_DATA[kode].desc.toLowerCase();
        return desc.includes(keyword) || kode.toLowerCase() === keyword;
    });

    if (results.length === 0) {
        logMessage("(!) Gejala tidak ditemukan. Coba kata kunci yang lebih umum.", 'error');
    } else {
        logMessage(`Ditemukan ${results.length} gejala yang cocok. Klik tombol untuk memilih:`, 'info');
        createSelectableGejalaHtml(results); 
    }

    keywordInput.value = '';
    outputArea.scrollTop = outputArea.scrollHeight;
}

/**
 * Dipanggil saat tombol gejala di hasil pencarian diklik
 */
function handleGejalaSelect(event) {
    const kode = event.currentTarget.dataset.kode;
    const desc = event.currentTarget.dataset.desc;

    if (selectedGejala.hasOwnProperty(kode)) {
        return;
    }

    selectedGejalaCode = kode;
    modalGejalaDesc.textContent = desc;
    cfModal.show();
}


// --- CORE LOGIC: CF MODAL HANDLER ---

// Listener untuk tombol-tombol pilihan CF di modal (Perlu dipasang di DOMContentLoaded)
function initializeCfModalListeners() {
    document.querySelectorAll('.cf-select-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            const cfValue = parseFloat(event.currentTarget.dataset.cfValue);
            
            if (selectedGejalaCode) {
                selectedGejala[selectedGejalaCode] = cfValue;
                
                const cfDesc = CF_OPTIONS[cfValue];

                logMessage(`(v) ${selectedGejalaCode} (${GEJALA_DATA[selectedGejalaCode].desc}) berhasil disimpan. Keyakinan: ${cfDesc}.`, 'success');
                
                outputArea.innerHTML = '';
                logMessage("Sistem siap. Masukkan gejala berikutnya atau ketik 'SELESAI'.", 'info');

                selectedGejalaCode = null;
                updateGejalaCount();
                cfModal.hide();
            }
        });
    });
}


// --- CORE LOGIC: DIAGNOSIS FINAL (AJAX) ---

function finishDiagnosis() {
    if (Object.keys(selectedGejala).length === 0) {
         logMessage("(!) Masukkan minimal satu gejala sebelum menyelesaikan diagnosa.", 'error');
         return;
    }
    
    logMessage("--------------------------------------------------", 'info');
    logMessage("Memproses diagnosa menggunakan Certainty Factor...", 'search');
    
    keywordInput.disabled = true;
    searchBtn.disabled = true;
    finishBtn.disabled = true;

    const formData = new FormData();
    formData.append('gejala_data', JSON.stringify(selectedGejala));

    fetch('api_diagnose.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            logMessage("Penilaian Selesai. Mengarahkan ke laporan resmi...", 'success');
            
            sessionStorage.setItem('diagnosis_result', JSON.stringify(data.results));
            sessionStorage.setItem('selected_gejala', JSON.stringify(selectedGejala));
            
            setTimeout(() => {
                window.location.href = 'result.php';
            }, 1000);
        } else {
            logMessage(`Error Server: ${data.message}`, 'error');
            
            keywordInput.disabled = false;
            searchBtn.disabled = false;
            finishBtn.disabled = false;
        }
    })
    .catch(error => {
        logMessage(`Error Jaringan: ${error.message}`, 'error');
        keywordInput.disabled = false;
        searchBtn.disabled = false;
        finishBtn.disabled = false;
    });
}

// --- INITIALIZATION AND EVENT LISTENERS (REVISI FINAL) ---

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inisialisasi Modal Bootstrap (Harus setelah DOMContentLoaded)
    cfModal = new bootstrap.Modal(cfModalElement);

    // 2. Pasang Event Listeners Utama
    if (searchBtn) searchBtn.addEventListener('click', searchGejala);

    if (keywordInput) {
        keywordInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchGejala();
            }
        });
    }

    if (finishBtn) finishBtn.addEventListener('click', finishDiagnosis);
    
    // 3. Pasang Listener untuk Tombol Modal (CF Selection)
    initializeCfModalListeners();

    // 4. Update UI
    updateGejalaCount();
});