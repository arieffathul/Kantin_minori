<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("location: login.php");
    exit;
}
if (isset($_SESSION["id_admin"])) {
    $id_admin = $_SESSION["id_admin"]; // Ambil nilai id_admin dari sesi
    // Lanjutkan dengan penggunaan $id_admin sesuai kebutuhan
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Request</title>
    <link rel="stylesheet" href="css/list.css">
</head>

<?php

require 'function.php';

$foods = query("SELECT makanan.*, kategori.*, req_makanan.*, admin.* FROM makanan
                LEFT JOIN kategori ON makanan.id_kategori = kategori.id_kategori
                RIGHT JOIN req_makanan ON makanan.id_makanan = req_makanan.id_makanan
                LEFT JOIN admin ON req_makanan.id_admin = admin.id_admin
                WHERE makanan.status = 'requested'
                ORDER BY req_makanan.tgl_request ASC");

$kategori = query("SELECT * FROM kategori ORDER BY kategori ASC");

include 'cari_makanan1.php';
// if (isset($_GET['terima'])) {

//     mysqli_query($koneksi, "UPDATE admin SET role = 'user' WHERE id_admin ='$_GET[terima]'");
//     echo "<script>
//     alert('Status telah diperbaharui')
//     document.location.href = 'guest.php'
//     </script>";
// }

if (isset($_GET['batal']) && isset($_GET['username'])) {
    $batal = $_GET['batal'];
    $username = $_GET['username'];
    if ($username == $id_admin) {
        mysqli_query($koneksi, "DELETE FROM makanan WHERE id_makanan ='$_GET[batal]'");
        echo "<script>
        alert('Request dibatalkan')
        document.location.href = 'user_request.php'
        </script>";
    } else {
        echo "<script>
        alert('Tidak dapat membatalkan request orang lain')
        document.location.href = 'user_request.php'
        </script>";
    }
}

$i = 1
?>

<body>
    <?php
    include 'user_header.php'
    ?>

    <!-- Filter -->
    <div class="filter">
        <h1>LIST REQUEST</h1>
        <form action="" method="post">
            <table class="menu">
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
            </table>
        </form>
    </div>


    <!-- List Role -->
    <div class="role">
        <h3><a href="user_request.php">Makanan</a></h3>
        <h3><a href="user_request1.php">Menu</a></h3>
    </div>

    <!-- list User -->
    <div class="list">
        <table border="1px" cellspacing="0">
            <thead>
                <th>No</th>
                <th>Nama</th>
                <th>Makanan</th>
                <th>Kategori</th>
                <th>Tanggal Request</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php foreach ($foods as $food) { ?>
                    <tr>
                        <td align="right"><?= $i++ ?></td>
                        <input type="hidden" name="nama" value="<?= $food['username'] ?>">
                        <td width="250px"><?= $food['username'] ?></td>
                        <td width="250px"><?= $food['makanan'] ?></td>
                        <td align="center"><?= $food['kategori'] ?></td>
                        <td align="center"><?= $food['tgl_request'] ?></td>
                        <td align="center">
                            <a href="?batal= <?= $food['id_makanan'] ?> &username= <?= $food['id_admin'] ?>" onclick="return konfirmasi(); ">Batal</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
        function konfirmasi() {
            return confirm('Yakin ingin membatalkan request ini?');
        }
    </script>
</body>

</html>