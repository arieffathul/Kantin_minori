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
    <title>Kelola Request</title>
    <link rel="stylesheet" href="css/list.css">
</head>

<?php

require 'function.php';

$menus = query("SELECT menu.*, req_menu.*, admin.* FROM menu
                RIGHT JOIN req_menu ON menu.id_menu = req_menu.id_menu
                LEFT JOIN admin ON req_menu.id_admin = admin.id_admin
                WHERE status = 'requested'
                ORDER BY tanggal ASC");

// if (isset($_GET['terima'])) {

//     mysqli_query($koneksi, "UPDATE admin SET role = 'user' WHERE id_admin ='$_GET[terima]'");
//     echo "<script>
//     alert('Status telah diperbaharui')
//     document.location.href = 'guest.php'
//     </script>";
// }

// if (isset($_GET['batal'])) {

//     mysqli_query($koneksi, "DELETE FROM makanan WHERE id_makanan ='$_GET[batal]'");
//     echo "<script>
//     alert('Request dibatalkan')
//     document.location.href = 'user_request.php'
//     </script>";
// }

// if (isset($_POST['cari'])) {
//     $cari = $_POST['search'];

//     // Lakukan query pencarian
//     $user = query("SELECT * FROM makanan WHERE makanan LIKE '%$cari%' AND status = 'requested' ORDER BY tgl_request ASC");
// }

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
                <th>Tanggal</th>
                <th>Total Menu</th>
                <th>Tanggal Request</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php foreach ($menus as $menu) { ?>
                    <tr>
                        <td align="right"><?= $i++ ?></td>
                        <td width="250px"><?= $menu['username'] ?></td>
                        <td width="250px"><?= date('d/m/Y', strtotime($menu['tanggal'])) ?></td>
                        <td align="center"><?= $menu['total_menu'] ?></td>
                        <td align="center"><?= date('d/m/Y H:i:s', strtotime($menu['tgl_request'])) ?></td>
                        <td align="center">
                            <a href="edit_request.php ?menu= <?= $menu['id_menu'] ?>">Lihat Request</a>
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