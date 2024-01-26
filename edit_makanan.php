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
    <title>Edit Makanan</title>
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

if (isset($_GET['delete'])) {

    mysqli_query($koneksi, "DELETE FROM makanan WHERE id_makanan ='$_GET[delete]'");
    echo "<script>
    alert('Makanan berhasil dihapus')
    document.location.href = 'makanan.php'
    </script>";
}

$edit_id = $_GET['edit'];

// Lakukan query untuk mendapatkan data kategori berdasarkan ID
$makanan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM makanan WHERE id_makanan = '$edit_id'"));
if (isset($_POST['update'])) {
    $edit_id = $_POST['edit_id'];
    $edit_makanan = $_POST['edit_makanan'];
    $edit_kategori = $_POST['edit_kategori'];
    $edit_harga = $_POST['edit_harga'];

    // Lakukan query untuk update data kategori
    $update_data = mysqli_query($koneksi, "UPDATE makanan
                                SET makanan = '$edit_makanan', id_kategori = '$edit_kategori', harga = '$edit_harga'
                                WHERE id_makanan = '$edit_id'");

    if ($update_data) {
        echo "<script>alert('Makanan berhasil diupdate!')</script>";
        echo "<script>document.location.href = 'makanan.php'</script>";
        exit;
    } else {
        echo "<script>alert('Gagal melakukan update Makanan!')</script>";
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
        <h1>LIST MAKANAN</h1>
        <form action="" method="post">
            <table>
                <tr>
                    <td>
                        <input type="text" id="search" name="search">
                    </td>
                    <td>
                        <select name="filter" id="filter" class="pilih">
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
                        <a href="makanan.php#tambah">Tambah Produk</a>
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
                <th>Action</th>
            </thead>
            <tbody>
                <?php foreach ($foods as $food) { ?>
                    <tr>
                        <td align="right"><?= $i++ ?></td>
                        <td><?= $food['makanan'] ?></td>
                        <td><?= $food['kategori'] ?></td>
                        <td align="right"><?= number_format($food['harga']) ?></td>
                        <td align="center">
                            <a href="edit_makanan.php?edit= <?= $food['id_makanan'] ?>#edit">Edit</a>
                            <a href="?delete= <?= $food['id_makanan'] ?>" onclick="return konfirmasi(); ">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Edit -->
    <div class="container">
        <div class="form" id="edit">
            <div class="judul">
                <h2> Edit Makanan </h2>
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
                            <input type="hidden" name="edit_id" value="<?= $makanan['id_makanan'] ?>">
                            <label for="edit_makanan">
                                <td>Nama Makanan</td>
                                <td>:</td>
                                <td><input type="text" id="edit_makanan" name="edit_makanan" value="<?= $makanan['makanan'] ?>" required></td>
                            </label>
                        </tr>
                        <tr>
                            <label for="edit_kategori">
                                <td>Kategori</td>
                                <td>:</td>
                                <td>
                                    <select id="edit_kategori" name="edit_kategori" required>
                                        <?php foreach ($kategori as $kategoriItem) { ?>
                                            <option value="<?= $kategoriItem['id_kategori'] ?>" <?php echo ($kategoriItem['id_kategori'] == $makanan['id_kategori']) ? 'selected' : ''; ?>>
                                                <?= $kategoriItem['kategori'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </label>
                        </tr>
                        <tr>
                            <label for="edit_harga">
                                <td>Harga</td>
                                <td>:</td>
                                <td><input type="number" id="edit_harga" name="edit_harga" value="<?= $makanan['harga'] ?>" required></td>
                            </label>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">
                                <button type="submit" name="update">Update</button>
                                <a href="makanan.php#tambah">Batal</a>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <script>
        function konfirmasi() {
            return confirm('Apakah Anda yakin ingin menghapus makanan ini?');
        }
    </script>
</body>

</html>