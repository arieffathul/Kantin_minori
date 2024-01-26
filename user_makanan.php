<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    <title>Daftar Makanan</title>
    <link rel="stylesheet" href="css/list.css">
</head>

<?php

require 'function.php';

$foods = query("SELECT makanan.*, kategori.*
                FROM makanan LEFT JOIN kategori ON makanan.id_kategori = kategori.id_kategori
                WHERE makanan.status = 'acc'
                ORDER BY kategori.kategori ASC");

$kategori = query("SELECT * FROM kategori ORDER BY kategori ASC");

include 'cari_makanan.php';

if (isset($_POST['save'])) {
    $nama_makanan = $_POST['makanan'];
    $id_kategori = $_POST['kategori'];
    $harga = $_POST['harga'];

    $result_kategori = mysqli_query($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = '$id_kategori'");

    if (!mysqli_fetch_assoc($result_kategori)) {
        $error = 'Harap masukkan kategori';
        echo "<script>document.location.href = 'makanan.php#tambah'</script>";
    } else {
        $result = mysqli_query($koneksi, "SELECT makanan FROM makanan WHERE makanan = '$nama_makanan'");

        if (mysqli_fetch_assoc($result)) {
            $error = "$nama_makanan sudah ada";
            echo "<script>document.location.href = 'makanan.php#tambah'</script>";
        } else {
            $insert_data = mysqli_query($koneksi, "INSERT INTO makanan (makanan, id_kategori, harga, status) VALUES ('$nama_makanan','$id_kategori','$harga', 'acc')");
            if ($insert_data) {
                $got = "$nama_makanan telah ditambahkan";
                $_SESSION['success_message'] = $got;
                header('Location: makanan.php#tambah');
                exit;
            } else {
                echo "<script>alert('$nama_makanan gagal disimpan!')</script>";
            }

            // if ($status == true) {
            //     header('location: http://localhost/project/makanan.php#tambah');
            // }
        }
    }
}

if (isset($_SESSION['success_message'])) {
    $got = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // hapus pesan dari session
}

$i = 1
?>

<body>

    <?php
    include 'user_header.php'
    ?>

    <!-- Filter -->
    <div class="filter">
        <h1>LIST MAKANAN</h1>
        <form action="" method="post">
            <table>
                <tr>
                    <td>
                        <input type="text" id="search" name="search" oninput="liveSearch()">
                    </td>
                    <td>
                    <select name="filter" id="filter" class="pilih" onchange="liveSearch()">
                            <option value="">- Kategori -</option>
                            <?php foreach ($kategori as $value) { ?>
                                <option value="<?= $value['id_kategori'] ?>"><?= ucwords($value['kategori']) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td><button type="submit" name="cari">Cari</button></td>
                </tr>
                <tr>
                    <td colspan="3" align="center">
                        <a href="request_makanan.php">Request Makanan</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <!-- list makanan -->
    <div class="list">
        <table border="1px" cellspacing="0">
            <thead>
                <th>No</th>
                <th>Nama Makanan</th>
                <th>Kategori</th>
                <th>Harga</th>
            </thead>
            <tbody>
                <?php foreach ($foods as $food) { ?>
                    <tr>
                        <td align="right"><?= $i++ ?></td>
                        <td><?= $food['makanan'] ?></td>
                        <td><?= $food['kategori'] ?></td>
                        <td align="right"><?= number_format($food['harga']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
        function konfirmasi() {
            return confirm('Apakah Anda yakin ingin menghapus menu ini?');
        }
    </script>
</body>

</html>