<?php

//koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "project_new");

function query($query)
{
    global $koneksi; // Tambahkan ini untuk membuat variabel $koneksi dapat diakses di dalam fungsi
    $result = mysqli_query($koneksi, $query);
    $rows = [];
    while ($row = mysqli_fetch_array($result)) {
        $rows[] = $row;
    }
    return $rows;
}
