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
                WHERE role = 'guest'
                ORDER BY tgl_register ASC");

if (isset($_GET['terima'])) {

    mysqli_query($koneksi, "UPDATE admin SET role = 'user' WHERE id_admin ='$_GET[terima]'");
    echo "<script>
    alert('Status telah diperbaharui')
    document.location.href = 'guest.php'
    </script>";
}

if (isset($_GET['tolak'])) {

    mysqli_query($koneksi, "DELETE FROM admin WHERE id_admin ='$_GET[tolak]'");
    echo "<script>
    alert('Akun berhasil dihapus')
    document.location.href = 'guest.php'
    </script>";
}

if (isset($_POST['cari'])) {
    $cari = $_POST['search'];

    // Lakukan query pencarian
    $users = query("SELECT * FROM admin WHERE username LIKE '%$cari%' AND role = 'guest' ORDER BY tgl_register ASC");
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
                <th>Tanggal Register</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php foreach ($users as $user) { ?>
                    <tr>
                        <td align="right"><?= $i++ ?></td>
                        <td width="250px"><?= $user['username'] ?></td>
                        <td align="center"><?= $user['tgl_register'] ?></td>
                        <td align="center">
                            <a href="?terima= <?= $user['id_admin'] ?>">Terima</a>
                            <a href="?tolak= <?= $user['id_admin'] ?>" onclick="return konfirmasi(); ">Tolak</a>
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