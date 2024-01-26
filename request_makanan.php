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

if (isset($_POST['request'])) {
    $nama_makanan = $_POST['makanan'];
    $id_kategori = $_POST['kategori'];

    $result_kategori = mysqli_query($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = '$id_kategori'");

    if (!mysqli_fetch_assoc($result_kategori)) {
        $result = mysqli_query($koneksi, "SELECT makanan FROM makanan WHERE makanan = '$nama_makanan' AND status = 'acc'");

        if (mysqli_fetch_assoc($result)) {
            $error = "$nama_makanan sudah ada";
        } else {
            $insert_data = mysqli_query($koneksi, "INSERT INTO makanan (makanan) VALUES ('$nama_makanan')");
            if ($insert_data) {
                $id_makanan = mysqli_insert_id($koneksi);
                $tambah_request = mysqli_query($koneksi, "INSERT req_makanan (id_makanan, id_admin) VALUES ('$id_makanan', '$id_admin')");
                if ($tambah_request) {
                    $got = "request telah disimpan";
                    $_SESSION['success_message'] = $got;
                    header('Location: request_makanan.php');
                    exit;
                }
            } else {
                echo "<script>alert('$nama_makanan gagal disimpan!')</script>";
            }

            // if ($status == true) {
            //     header('location: http://localhost/project/makanan.php#tambah');
            // }
        }
    } else {
        $result = mysqli_query($koneksi, "SELECT makanan FROM makanan WHERE makanan = '$nama_makanan' AND status = 'acc'");

        if (mysqli_fetch_assoc($result)) {
            $error = "$nama_makanan sudah ada";
        } else {
            $insert_data = mysqli_query($koneksi, "INSERT INTO makanan (makanan, id_kategori) VALUES ('$nama_makanan','$id_kategori')");
            if ($insert_data) {
                $id_makanan = mysqli_insert_id($koneksi);
                $tambah_request = mysqli_query($koneksi, "INSERT req_makanan (id_makanan, id_admin) VALUES ('$id_makanan', '$id_admin')");
                if ($tambah_request) {
                    $got = "request telah disimpan";
                    $_SESSION['success_message'] = $got;
                    header('Location: request_makanan.php');
                    exit;
                }
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

    <!-- Form -->
    <div class="request_makanan">
        <div class="form" id="tambah">
            <div class="judul">
                <h2> Request Makanan </h2>
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
                        <!-- <tr>
                            <td colspan="3" align="center">
                                <button type="submit" name="request">Request</button>
                                <a href="user_makanan.php">Batal</a>
                            </td>
                        </tr> -->
                    </table>
                    <table class="tombol">
                        <tr>
                            <td colspan="3" align="center">
                                <button type="submit" name="request">Request</button>
                                <a href="user_makanan.php">Kembali</a>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</body>

</html>