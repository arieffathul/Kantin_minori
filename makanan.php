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

if (isset($_GET['delete'])) {

    mysqli_query($koneksi, "DELETE FROM makanan WHERE id_makanan ='$_GET[delete]'");
    echo "<script>
    alert('Makanan berhasil dihapus')
    document.location.href = 'makanan.php'
    </script>";
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
                        <a href="#tambah">Tambah Makanan</a>
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

    <!-- Form -->
    <div class="container">
        <div class="form" id="tambah">
            <div class="judul">
                <h2> Tambah Makanan </h2>
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
                            <td>Nama Makanan</td>
                            <td>:</td>
                            <td><input type="text" name="makanan" required></td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>:</td>
                            <td>
                                <select name="kategori" id="kategori">
                                    <option value="">--- Kategori ---</option>
                                    <?php foreach ($kategori as $value) { ?>
                                        <option value="<?= $value['id_kategori'] ?>"><?= ucwords($value['kategori']) ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Harga</td>
                            <td>:</td>
                            <td><input type="number" name="harga" min="0" required></td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">
                                <button type="submit" name="save">Save</button>
                                <button type="reset" name="reset">Reset</button>
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