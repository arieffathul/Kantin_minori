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
    <title>Daftar Kategori</title>
    <link rel="stylesheet" href="css/list.css">
</head>
<?php

require 'function.php';

$categories = query("SELECT * FROM kategori ORDER BY kategori ASC");

include 'cari_kategori.php';

if (isset($_POST['save'])) {
    $nama_kategori = $_POST['nama'];

    $insert_data = mysqli_query($koneksi, "INSERT INTO kategori(kategori) VALUES ('$nama_kategori')");

    if ($insert_data) {
        $got = "$nama_kategori telah ditambahkan";
        $_SESSION['success_message'] = $got;
        header('Location: kategori.php#tambah');
        exit;
    } else {
        echo "<script>alert('Kategori gagal disimpan!')</script>";
    }
}
if (isset($_GET['delete'])) {

    mysqli_query($koneksi, "DELETE FROM kategori WHERE id_kategori ='$_GET[delete]'");
    echo "<script>
    alert('data berhasil dihapus')
    document.location.href = 'kategori.php'
    </script>";
}
if (isset($_SESSION['success_message'])) {
    $got = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // hapus pesan dari session
}

$i = 1
?>

<body>
<?php
include 'header.php'
?>

    <!-- Filter -->
    <div class="filter">
        <h1>LIST KATEGORI</h1>
        <form action="" method="post">
            <table>
                <tr>
                    <td>
                        <input type="text" id="search" name="search">
                    </td>
                    <td><button type="submit" name="cari">Cari</button></td>
                </tr>
                <tr>
                    <td colspan="3" align="center">
                        <a href="#tambah">Tambah Kategori</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <!-- list -->
    <div class="list">
        <table border="1" cellspacing="0">
            <thead>
                <th class="number">No</th>
                <th>Nama Kategori</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php foreach ($categories as $category) { ?>
                    <tr>
                        <td align="right"><?= $i++ ?></td>
                        <td><?= $category['kategori'] ?></td>
                        <td align="center">
                            <a href="edit_kategori.php?edit= <?= $category['id_kategori'] ?>#edit">Edit</a>
                            <a href="?delete= <?= $category['id_kategori'] ?>" onclick="return confirm(); ">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Form -->
    <div class="container">
        <div class="form" id="tambah">
            <div class="judul">
                <h2> Tambah Kategori </h2>
            </div>
            <div class="isian">
                <form action="" method="post">
                    <table>
                        <tr>
                            <td colspan="3">
                                <?php if (!empty($error)) { ?>
                                    <p class="alert"><?php echo $error; ?></p>
                                <?php } ?>
                                <?php if (!empty($got)) { ?>
                                    <p class="got"><?php echo $got; ?></p>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Nama Kategori</td>
                            <td>:</td>
                            <td><input type="text" name="nama" required></td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">
                                <button type="submit" name="save">Save</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <script>
        function confirm() {
            return confirm('Apakah Anda yakin ingin menghapus kategori ini?');
        }
    </script>
</body>

</html>