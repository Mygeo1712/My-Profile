<?php
// template/header.php
if (!isset($page_title)) {
    $page_title = "Sistem Diagnosa Jagung";
}

$root_path = (isset($is_page_root) && $is_page_root) ? '' : '../';
$nav_diagnosa_path = (isset($is_page_root) && $is_page_root) ? 'pages/diagnose.php' : 'diagnose.php';
$nav_basis_path = $root_path . 'pages/knowledge_base.php'; 

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar CF | <?php echo $page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $root_path; ?>assets/style.css">
</head>
<body class="has-fixed-header">
    
   <!-- UBAH sticky-top MENJADI fixed-top -->
   <header class="navbar navbar-expand-lg navbar-dark bg-accent shadow-lg fixed-top main-header">
        <div class="container">
            <a class="navbar-brand text-highlight" href="<?php echo $root_path; ?>index.php">
                <i class="bi bi-gear-wide-connected me-2 fs-4"></i>
                <span class="fs-5 fw-normal me-1">SISTEM DIAGNOSA JAGUNG</span> 
         
            </a>
            
            <div class="d-flex">
                <a href="<?php echo $nav_diagnosa_path; ?>" class="btn btn-primary-color me-2 fw-bold">
                    <i class="bi bi-activity me-1"></i> Mulai Diagnosa
                </a>
                <a href="<?php echo $nav_basis_path; ?>" class="btn btn-outline-light me-2">
                    <i class="bi bi-book me-1"></i> Basis Pengetahuan
                </a>
            </div>
        </div>
    </header>
    
    <main class="container main-content">