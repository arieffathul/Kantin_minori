<?php
session_start();

if( !isset($_SESSION["admin"])){
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akses</title>
    <link rel="stylesheet" href="css/list.css">
</head>

<?php

require 'function.php';

$users = query("SELECT * FROM admin
                WHERE role = 'admin'
                ORDER BY last_login ASC");

if (isset($_GET['turun'])) {

    mysqli_query($koneksi, "UPDATE admin SET role = 'user' WHERE id_admin ='$_GET[turun]'");
    echo "<script>
    alert('Status telah diperbaharui')
    document.location.href = 'admin.php'
    </script>";
}

if (isset($_GET['hapus'])) {

    mysqli_query($koneksi, "DELETE FROM admin WHERE id_admin ='$_GET[hapus]'");
    echo "<script>
    alert('Akun berhasil dihapus')
    document.location.href = 'admin.php'
    </script>";
}

if (isset($_POST['cari'])) {
    $cari = $_POST['search'];

    // Lakukan query pencarian
    $users = query("SELECT * FROM admin WHERE username LIKE '%$cari%' AND role = 'admin' ORDER BY tgl_register ASC");
}

$i = 1
?>

<body>
    <?php
    include 'header.php'
    ?>

    <!-- Filter -->
    <div class="filter">
        <h1>LIST AKUN</h1>
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
        <h3><a href="guest.php">Guest</a></h3>
        <h3><a href="user.php">User</a></h3>
        <h3><a href="admin.php">Admin</a></h3>
    </div>

    <!-- list User -->
    <div class="list">
        <table border="1px" cellspacing="0">
            <thead>
                <th>No</th>
                <th>Username</th>
                <th>Last Login</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php foreach ($users as $user) { ?>
                    <tr>
                        <td align="right"><?= $i++ ?></td>
                        <td width="250px"><?= $user['username'] ?></td>
                        <td align="center"><?= $user['last_login'] ?></td>
                        <td align="center">
                            <a href="?hapus= <?= $user['id_admin'] ?>" onclick="return konfirmasi(); ">Hapus Akun</a>
                            <a href="?turun= <?= $user['id_admin'] ?>">Keluar Admin</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
        function konfirmasi() {
            return confirm('Yakin ingin menolak akun ini?');
        }
    </script>
</body>

</html>