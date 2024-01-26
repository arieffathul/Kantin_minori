<?php

// if( !isset($_SESSION["admin"])){
//     header("location: login.php");
//     exit;
// }

if (isset($_POST['cari'])) {
    $cari = $_POST['search'];

    // Lakukan query pencarian
    $pakets = query("SELECT * FROM paket WHERE nama_paket LIKE '%$cari%' ORDER BY nama_paket ASC");
}