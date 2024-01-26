<?php
session_start();

if( !isset($_SESSION["user"])){
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
    <title>Tambah Menu</title>
    <link rel="stylesheet" href="css/list.css">
</head>

<?php

require 'function.php';

$foods = query("SELECT makanan.*, kategori.*
                FROM makanan LEFT JOIN kategori ON makanan.id_kategori = kategori.id_kategori
                WHERE makanan.status = 'acc'
                ORDER BY makanan.makanan ASC");
$kategori = query("SELECT * FROM kategori ORDER BY kategori ASC");

$tanggal_menu = '';

if (isset($_GET['date'])) {
    $tanggal_menu = $_GET['date'];
}

if (isset($_POST['request'])) {
    $tanggal = $_POST['tanggal'];
    $total = $_POST['total'];
    $hitmakanan = count(array_values(array_filter($_POST['makanan'])));
    $makanan = array_values(array_filter($_POST['makanan']));

    // Menghilangkan karakter 'Rp ' dari string
    $total = str_replace('Rp ', '', $total);

    // Menghilangkan karakter ',' untuk memastikan format numerik yang benar
    $total = str_replace(',', '', $total);

    // Mengonversi string menjadi integer
    $total = (int)$total;
    $total = $total * 1000;

    if($total) {
        echo $total;
    }

    if ($hitmakanan == 0) {
        $error = 'harap input minimal 1 makanan';
    } else {

        $insert_menu = mysqli_query($koneksi, "INSERT INTO menu (tanggal, total_menu) VALUES ('$tanggal','$total')");

        if ($insert_menu) {
            $id_menu = mysqli_insert_id($koneksi);

            $tambah_request = mysqli_query($koneksi, "INSERT req_menu (id_menu, id_admin) VALUES ('$id_menu', '$id_admin')");
            // echo $id_menu;

            for ($i = 0; $i < $hitmakanan; $i++) {
                $id_makanan = $makanan[$i];
                $insert_detail = mysqli_query($koneksi, "INSERT INTO detail_menu (id_makanan, id_menu) VALUES ('$id_makanan', $id_menu)");
            }
        }
        if ($insert_detail) {
            $got = "request telah dikirim";
            $_SESSION['success_message'] = $got;

            // Redirect ke URL yang ditentukan
            header("Location: request_menu.php");
            exit;
        } else {
            echo "<script>alert('$nama_makanan gagal disimpan!')</script>";
        }
    }
}

if (isset($_SESSION['success_message'])) {
    $got = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // hapus pesan dari session
}

?>

<body class="tambah_menu">

    <!-- Form -->
    <div class="container">
        <div class="form" id="tambah">
            <div class="judul">
                <h2> Request Menu </h2>
            </div>

            <div class="isian">
                <form action="" method="post">
                    <table class="table">
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td><input type="date" name="tanggal" value="<?= $tanggal_menu ?>" required></td>
                        </tr>
                    </table>
                    <?php if (!empty($error)) { ?>
                        <p class="alert"><?php echo $error; ?></p>
                    <?php } ?>
                    <?php if (!empty($got)) { ?>
                        <p class="got"><?php echo $got; ?></p>
                    <?php } ?>

                    <!-- Pilih makanan -->

                    <table border="1px" cellspacing="0" class="tambah">
                        <thead>
                            <th>Kategori</th>
                            <th>Makanan</th>
                            <th>Harga</th>
                        </thead>
                        <tbody>
                            <?php foreach ($kategori as $category) { ?>
                                <tr>
                                    <td><?= $category['kategori'] ?></td>
                                    <td align="center">
                                        <select name="makanan[]" id="makanan<?= $category['id_kategori'] ?>">
                                            <option value="">--- Makanan ---</option>
                                            <?php
                                            $foodsByCategory = array_filter($foods, function ($food) use ($category) {
                                                return $food['id_kategori'] == $category['id_kategori'];
                                            });
                                            foreach ($foodsByCategory as $food) {
                                            ?>
                                                <option value="<?= $food['id_makanan'] ?>" data-harga="<?= $food['harga'] ?>"><?= ucwords($food['makanan']) ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td class="harga_makanan" id="harga_makanan<?= $category['id_kategori'] ?>"></td>
                                </tr>
                            <?php } ?>
                            <tr></tr>
                            <tr>
                                <td colspan="2" bgcolor="lightgrey">Total</td>
                                <td><input type="text" name="total" id="total"></td>
                        </tbody>
                    </table>
                    <table class="button-container">
                        <tr>
                            <td colspan="3" align="center">
                                <button type="submit" name="request">Request</button>
                                <a href="user_menu.php">Kembali</a>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <!-- Include JavaScript to handle dynamic population -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach ($kategori as $category) { ?>
                var makananSelect<?= $category['id_kategori'] ?> = document.getElementById('makanan<?= $category['id_kategori'] ?>');
                var hargaMakanan<?= $category['id_kategori'] ?> = document.getElementById('harga_makanan<?= $category['id_kategori'] ?>');

                makananSelect<?= $category['id_kategori'] ?>.addEventListener('change', function() {
                    var selectedMakanan = makananSelect<?= $category['id_kategori'] ?>.options[makananSelect<?= $category['id_kategori'] ?>.selectedIndex];
                    var selectedMakananHarga = selectedMakanan.getAttribute('data-harga');

                    // Format angka dengan toLocaleString()
                    hargaMakanan<?= $category['id_kategori'] ?>.innerText = selectedMakananHarga ? 'Rp ' + parseFloat(selectedMakananHarga).toLocaleString('id-ID') : '';

                    // Hitung total
                    hitungTotal();
                });
            <?php } ?>

            // Fungsi untuk menghitung total
            function hitungTotal() {
                var total = 0;

                <?php foreach ($kategori as $category) { ?>
                    var hargaMakanan<?= $category['id_kategori'] ?> = parseFloat(document.getElementById('harga_makanan<?= $category['id_kategori'] ?>').innerText.replace(/[^\d]/g, '')) || 0;
                    total += hargaMakanan<?= $category['id_kategori'] ?>;
                <?php } ?>

                // Tampilkan total
                var totalElement = document.getElementById('total');
                totalElement.value = 'Rp ' + total.toLocaleString('id-ID');

                // Hitung total * qty
                updateTotalQty();
            }
            // Event listener untuk perubahan pada input qty
            var qtyInput = document.getElementsByName('qty')[0];
            qtyInput.addEventListener('input', function() {
                updateTotalQty();
            });

            // Fungsi untuk mengupdate total * qty
            function updateTotalQty() {
                var total = parseFloat(document.getElementById('total').value.replace(/[^\d]/g, '')) || 0;
                var qty = parseInt(qtyInput.value) || 0;
                var totalQty = total * qty;

                // Tampilkan total * qty
                var totalQtyElement = document.getElementById('total_qty');
                totalQtyElement.value = 'Rp ' + totalQty.toLocaleString('id-ID');
            }

        });
    </script>

</body>

</html>