<?php

if (isset($_POST['cari'])) {
    $cari = $_POST['search'];
    $filter = $_POST['filter'];

    // Lakukan query pencarian
    $query = "SELECT makanan.*, kategori.*
              FROM makanan 
              LEFT JOIN kategori ON makanan.id_kategori = kategori.id_kategori
              WHERE makanan.status = 'requested'";

    if (!empty($cari)) {
        $query .= " AND makanan.makanan LIKE '%$cari%'";
    }

    if (!empty($filter)) {
        $query .= " AND makanan.id_kategori = $filter";
    }

    $query .= " ORDER BY makanan.tgl_request ASC";

    $foods = query($query);
}
