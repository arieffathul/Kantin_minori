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
    <title>Edit Kategori</title>
    <link rel="stylesheet" href="css/list.css">
</head>
<?php

require 'function.php';

$categories = query("SELECT * FROM kategori ORDER BY kategori ASC");

include 'cari_kategori.php';

if (isset($_GET['delete'])) {

    mysqli_query($koneksi, "DELETE FROM kategori WHERE id_kategori ='$_GET[delete]'");
    echo "<script>
    alert('data berhasil dihapus')
    document.location.href = 'kategori.php'
    </script>";
}
// Ambil ID kategori dari parameter URL
$edit_id = $_GET['edit'];

// Lakukan query untuk mendapatkan data kategori berdasarkan ID
$kategori = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kategori WHERE id_kategori = '$edit_id'"));
if (isset($_POST['update'])) {
    $edit_id = $_POST['edit_id'];
    $edit_nama = $_POST['edit_nama'];

    // Lakukan query untuk update data kategori
    $update_data = mysqli_query($koneksi, "UPDATE kategori SET kategori = '$edit_nama' WHERE id_kategori = '$edit_id'");

    if ($update_data) {
        echo "<script>alert('Kategori berhasil diupdate!')</script>";
        echo "<script>document.location.href = 'kategori.php'</script>";
        exit;
    } else {
        echo "<script>alert('Gagal melakukan update kategori!')</script>";
    }
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
        <div class="form" id="edit">
            <div class="judul">
                <h2> Edit Kategori </h2>
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
                            <input type="hidden" name="edit_id" value="<?= $kategori['id_kategori'] ?>">
                            <label for="edit_nama">
                                <td>Nama Kategori</td>
                                <td>:</td>
                                <td><input type="text" id="edit_nama" name="edit_nama" value="<?= $kategori['kategori'] ?>" required></td>
                            </label>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">
                                <button type="submit" name="update">Update</button>
                                <a href="kategori.php#tambah">Batal</a>
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