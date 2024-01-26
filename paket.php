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

$pakets = query("SELECT * FROM paket
                WHERE status = 'acc'
                ORDER BY nama_paket ASC");

// $kategori = query("SELECT * FROM kategori ORDER BY kategori ASC");

include 'cari_paket.php';

// if (isset($_POST['save'])) {
//     $nama_makanan = $_POST['makanan'];
//     $id_kategori = $_POST['kategori'];
//     $harga = $_POST['harga'];

//     $result_kategori = mysqli_query($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = '$id_kategori'");

//     if (!mysqli_fetch_assoc($result_kategori)) {
//         $error = 'Harap masukkan kategori';
//         echo "<script>document.location.href = 'makanan.php#tambah'</script>";
//     } else {
//         $result = mysqli_query($koneksi, "SELECT makanan FROM makanan WHERE makanan = '$nama_makanan'");

//         if (mysqli_fetch_assoc($result)) {
//             $error = "$nama_makanan sudah ada";
//             echo "<script>document.location.href = 'makanan.php#tambah'</script>";
//         } else {
//             $insert_data = mysqli_query($koneksi, "INSERT INTO makanan (makanan, id_kategori, harga, status) VALUES ('$nama_makanan','$id_kategori','$harga', 'acc')");
//             if ($insert_data) {
//                 $got = "$nama_makanan telah ditambahkan";
//                 $_SESSION['success_message'] = $got;
//                 header('Location: makanan.php#tambah');
//                 exit;
//             } else {
//                 echo "<script>alert('$nama_makanan gagal disimpan!')</script>";
//             }

//             // if ($status == true) {
//             //     header('location: http://localhost/project/makanan.php#tambah');
//             // }
//         }
//     }
// }

if (isset($_SESSION['success_message'])) {
    $got = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // hapus pesan dari session
}

// if (isset($_GET['delete'])) {

//     mysqli_query($koneksi, "DELETE FROM makanan WHERE id_makanan ='$_GET[delete]'");
//     echo "<script>
//     alert('Makanan berhasil dihapus')
//     document.location.href = 'makanan.php'
//     </script>";
// }

$i = 1
?>

<body>

    <?php
    include 'header.php'
    ?>

      <!-- Filter -->
      <div class="filter">
        <h1>LIST PAKET</h1>
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
                        <a href="tambah_paket.php">Tambah Paket</a>
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
                <th>Nama Paket</th>
                <th>Total</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php foreach ($pakets as $paket) { ?>
                    <tr>
                        <td align="right"><?= $i++ ?></td>
                        <td><?= $paket['nama_paket'] ?></td>
                        <td align="right"><?= number_format($paket['total']) ?></td>
                        <td align="center">
                            <a href="detail_paket.php?detail= <?= $paket['id_paket'] ?>">Detail Paket</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!-- <script>
        function konfirmasi() {
            return confirm('Apakah Anda yakin ingin menghapus makanan ini?');
        }
    </script> -->
</body>

</html>