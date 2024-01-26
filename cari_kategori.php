<?php

if( !isset($_SESSION["admin"])){
    header("location: login.php");
    exit;
}

if (isset($_POST['cari'])) {
    $cari = $_POST['search'];

    // Lakukan query pencarian
    $categories = query("SELECT * FROM kategori WHERE kategori LIKE '%$cari%' ORDER BY kategori ASC");
}