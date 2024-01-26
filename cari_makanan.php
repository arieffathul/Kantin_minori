<?php

if (isset($_POST['cari'])) {
    $cari = $_POST['search'];
    $filter = $_POST['filter'];

    // Jika filter kategori tidak dipilih, ambil semua makanan
    if (empty($filter)) {
        $foods = query("SELECT makanan.*, kategori.*
                        FROM makanan LEFT JOIN kategori ON makanan.id_kategori = kategori.id_kategori
                        WHERE makanan.makanan LIKE '%$cari%' AND makanan.status = 'acc'
                        ORDER BY kategori.kategori ASC");
    } else {
        // Jika filter kategori dipilih, ambil makanan berdasarkan kategori dan pencarian
        $result_filter = mysqli_query($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = '$filter'");

        if (!mysqli_num_rows($result_filter)) {
            $foods = query("SELECT makanan.*, kategori.*
                            FROM makanan LEFT JOIN kategori ON makanan.id_kategori = kategori.id_kategori
                            WHERE makanan.makanan LIKE '%$cari%' AND makanan.status = 'acc'
                            ORDER BY kategori.kategori ASC");
        } else {
            $foods = query("SELECT makanan.*, kategori.*
                            FROM makanan LEFT JOIN kategori ON makanan.id_kategori = kategori.id_kategori
                            WHERE makanan.makanan LIKE '%$cari%' AND makanan.id_kategori = '$filter' AND makanan.status = 'acc'
                            ORDER BY kategori.kategori ASC");
        }
    }
}
?>