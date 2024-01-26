<?php
session_start();

if( !isset($_SESSION["user"])){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu</title>
    <link rel="stylesheet" href="css/list.css">
</head>

<?php

require 'function.php';

$foods = query("SELECT makanan.*, kategori.*
                FROM makanan LEFT JOIN kategori ON makanan.id_kategori = kategori.id_kategori
                WHERE makanan.status = 'acc'
                ORDER BY makanan.makanan ASC");
$kategori = query("SELECT * FROM kategori ORDER BY kategori ASC");

$detail_tanggal = $_GET['date'];

// $detail_menu = query("SELECT * FROM menu WHERE tanggal = '$detail_tanggal' ORDER BY tanggal ASC;");
$detail_menu = query("SELECT makanan.*, detail_menu.*, menu.*
                    FROM makanan RIGHT JOIN detail_menu ON makanan.id_makanan = detail_menu.id_makanan
                    LEFT JOIN menu ON menu.id_menu = detail_menu.id_menu
                    WHERE menu.tanggal = '$detail_tanggal' AND menu.status = 'acc'
                    ORDER BY menu.tanggal ASC;");

if ($detail_menu) {
    foreach ($detail_menu as $daftar_menu) {
        $detail_qty = $daftar_menu['qty'];
        $daftar_id_makanan[] = $daftar_menu['id_makanan']; // Menambahkan nilai 'makanan' ke dalam array
        $id_menu_edit = $daftar_menu['id_menu'];
        // $id_detail_menu[] = $daftar_menu['id_dm'];
    }
    $detail_id_makanan = implode(", ", $daftar_id_makanan);
    // $detail_id_menu = implode(", ", $daftar_id_makanan);
} else {
    $detail_qty = '1';
    $detail_id_makanan = '';
}

?>

<body class="tambah_menu">

    <!-- Form -->
    <div class="container">
        <div class="form" id="tambah">
            <div class="judul">
                <h2> Tambah Menu </h2>
            </div>

            <div class="isian">
                <form action="" method="post">
                    <table class="table">
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td><?= $detail_tanggal ?></td>
                        </tr>
                    </table>

                    <!-- Pilih makanan -->

                    <table border="1px" cellspacing="0" class="tambah">
                        <thead>
                            <th>Kategori</th>
                            <th>Makanan</th>
                        </thead>
                        <tbody>
                            <?php foreach ($kategori as $category) { ?>
                                <tr>
                                    <td><?= $category['kategori'] ?></td>
                                    <td align="center">
                                        <?php
                                        $selectedFood = '-';
                                        foreach ($foods as $food) {
                                            if ($food['id_kategori'] == $category['id_kategori'] && in_array($food['id_makanan'], explode(", ", $detail_id_makanan))) {
                                                $selectedFood = ucwords($food['makanan']);
                                                break;
                                            }
                                        }
                                        ?>
                                        <span><?= $selectedFood ?></span>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr></tr>
                        </tbody>
                    </table>
                    <table class="button-container">
                        <tr>
                            <td align="center" class="back"><a href="user_menu.php">Kembali</a></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <!-- Include JavaScript to handle dynamic population -->
    <script>
        function konfirmasi() {
            return confirm('Apakah Anda yakin ingin menghapus menu ini?');
        }
    </script>

</body>

</html>