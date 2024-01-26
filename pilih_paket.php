<?php
session_start();

if (!isset($_SESSION["admin"])) {
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

$kategoris = query("SELECT * FROM kategori ORDER BY kategori ASC");

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

if (isset($_SESSION['from'])) {
    $asal = $_SESSION['from'];
    // echo $asal;
    unset($_SESSION['from']);
} else {
    $asal = '#';
}

if (isset($_SESSION['tanggal'])) {
    $tanggal_asal = $_SESSION['tanggal'];
    // echo $tanggal_asal;
    unset($_SESSION['tanggal']);
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
                <?php foreach ($kategoris as $kategori) { ?>
                    <th><?= $kategori['kategori'] ?></th>
                <?php } ?>
                <th>Total</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php foreach ($pakets as $paket) { ?>
                    <tr>
                        <td align="right"><?= $i++ ?></td>
                        <td><?= $paket['nama_paket'] ?></td>


                        <?php
                        // Ambil detail paket untuk paket saat ini
                        $detail_pakets = query("SELECT * FROM detail_paket WHERE id_paket = '$paket[id_paket]' ");

                        // Inisialisasi array untuk makanan berdasarkan kategori
                        $foods_by_category = [];

                        foreach ($detail_pakets as $detail) {
                            // Ambil informasi makanan berdasarkan id makanan dari detail paket
                            $makanan = query("SELECT * FROM makanan WHERE id_makanan = '$detail[id_makanan]'");

                            // Ambil kategori makanan
                            $kategori_makanan = $makanan[0]['id_kategori'];

                            // Tambahkan makanan ke dalam array makanan berdasarkan kategori
                            $foods_by_category[$kategori_makanan][] = $makanan[0]['makanan'];
                        }

                        // Loop melalui setiap kategori dan tampilkan makanan
                        foreach ($kategoris as $kategori) {
                            $nokategori = $kategori['id_kategori'];
                            $foods = isset($foods_by_category[$nokategori]) ? implode(', ', $foods_by_category[$nokategori]) : '-';
                            echo "<td width='150px' align='center'>" . $foods . "</td>";
                        }
                        ?>

                        <td align="right"><?= number_format($paket['total']) ?></td>
                        <td align="center">
                            <a href="<?= $asal ?>?detail=<?= $paket['id_paket'] ?>&tanggal=<?= $tanggal_asal ?>">Ambil paket</a>
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