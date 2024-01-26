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

if (isset($_GET['detail']) && isset($_GET['tanggal'])) {
    $id_paket = $_GET['detail'];

    $tanggal_menu = $_GET['tanggal'];

    // Query untuk mendapatkan makanan berdasarkan id_paket
    $makanan_paket = query("SELECT makanan.* FROM makanan
                            INNER JOIN detail_paket ON makanan.id_makanan = detail_paket.id_makanan
                            WHERE detail_paket.id_paket = '$id_paket'");
}

if (isset($_POST['save'])) {
    $tanggal = $_POST['tanggal'];
    $qty = $_POST['qty'];
    $total = $_POST['total'];
    $hitmakanan = count(array_values(array_filter($_POST['makanan'])));
    $makanan = array_values(array_filter($_POST['makanan']));

    // Menghilangkan karakter 'Rp ' dari string
    $total = str_replace('Rp ', '', $total);

    // Menghilangkan karakter ',' untuk memastikan format numerik yang benar
    $total = str_replace(',', '', $total);

    // Mengonversi string menjadi integer
    $total = (int)$total;
    $total = $total * 1000 * $qty;

    // if($total) {
    //     echo $total;
    // }

    if ($hitmakanan == 0) {
        $error = 'harap input minimal 1 makanan';
    } else {
        $result = mysqli_query($koneksi, "SELECT tanggal FROM menu WHERE tanggal = '$tanggal' AND status= 'acc'");

        if (mysqli_fetch_assoc($result)) {
            $error = "menu tanggal $tanggal sudah ada";
            // echo "<script>document.location.href = 'tambah_sehari.php'</script>";
        } else {
            $insert_menu = mysqli_query($koneksi, "INSERT INTO menu (tanggal, qty, total_menu, status) VALUES ('$tanggal','$qty','$total','acc')");

            if ($insert_menu) {
                $id_menu = mysqli_insert_id($koneksi);

                // echo $id_menu;

                for ($i = 0; $i < $hitmakanan; $i++) {
                    $id_makanan = $makanan[$i];
                    $insert_detail = mysqli_query($koneksi, "INSERT INTO detail_menu (id_makanan, id_menu) VALUES ('$id_makanan', $id_menu)");
                }
            }
            if ($insert_detail) {
                $got = "menu telah ditambahkan";
                $_SESSION['success_message'] = $got;

                $redirect_url = isset($_GET['date']) ? "detail_menu.php?date=$tanggal_menu" : "tambah_sehari.php";

                // Redirect ke URL yang ditentukan
                header("Location: $redirect_url");
                exit;
            } else {
                echo "<script>alert('$nama_makanan gagal disimpan!')</script>";
            }
        }
    }
}

$_SESSION['tanggal'] = $tanggal_menu;

$_SESSION['from'] = 'tambah_sehari.php';

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
                <h2> Tambah Menu </h2>
            </div>

            <div class="isian">
                <form action="" method="post">
                    <table class="table">
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td><input type="date" name="tanggal" value="<?= $tanggal_menu ?>" required></td>
                        </tr>
                        <tr>
                            <td>Banyak</td>
                            <td>:</td>
                            <td><input type="number" name="qty" value="1" min='0' required></td>
                        </tr>
                        <tr>
                            <td colspan="3" align="left">
                                <a href="pilih_paket.php" style="width: 100px; margin-left: 0;">Pilih Paket</a>
                            </td>
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
                                                <?php
                                                $selected = '';
                                                foreach ($makanan_paket as $makanan) {
                                                    if ($makanan['id_makanan'] == $food['id_makanan']) {
                                                        $selected = 'selected';
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <option value="<?= $food['id_makanan'] ?>" data-harga="<?= $food['harga'] ?>" <?= $selected ?>><?= ucwords($food['makanan']) ?></option>
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
                            </tr>
                            <tr>
                                <td colspan="2" bgcolor="lightgrey">Total * Qty</td>
                                <td><input type="text" name="total_qty" id="total_qty"></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="button-container">
                        <tr>
                            <td colspan="3" align="center">
                                <button type="submit" name="save">Save</button>
                                <a href="menu.php">Kembali</a>
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

                // Tambahkan panggilan fungsi saat halaman dimuat
                updateHarga(makananSelect<?= $category['id_kategori'] ?>, hargaMakanan<?= $category['id_kategori'] ?>);

                makananSelect<?= $category['id_kategori'] ?>.addEventListener('change', function() {
                    // Panggil fungsi saat opsi berubah
                    updateHarga(makananSelect<?= $category['id_kategori'] ?>, hargaMakanan<?= $category['id_kategori'] ?>);

                    // Hitung total saat opsi berubah
                    hitungTotal();
                });
            <?php } ?>

            // Panggil fungsi hitungTotal saat halaman dimuat untuk menginisialisasi total
            hitungTotal();

            // Fungsi untuk mengupdate harga berdasarkan opsi terpilih
            function updateHarga(selectElement, hargaElement) {
                var selectedMakanan = selectElement.options[selectElement.selectedIndex];
                var selectedMakananHarga = selectedMakanan.getAttribute('data-harga');

                // Format angka dengan toLocaleString()
                hargaElement.innerText = selectedMakananHarga ? 'Rp ' + parseFloat(selectedMakananHarga).toLocaleString('id-ID') : '';
            }

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
            }
            // Fungsi untuk menghitung total * qty
            function hitungTotalQty() {
                var totalQty = 0;
                var qtyElement = document.querySelector('input[name="qty"]');
                var qty = parseInt(qtyElement.value) || 0;

                <?php foreach ($kategori as $category) { ?>
                    var hargaMakanan<?= $category['id_kategori'] ?> = parseFloat(document.getElementById('harga_makanan<?= $category['id_kategori'] ?>').innerText.replace(/[^\d]/g, '')) || 0;
                    totalQty += hargaMakanan<?= $category['id_kategori'] ?>;
                <?php } ?>

                // Hitung total * qty
                var totalQtyResult = totalQty * qty;

                // Tampilkan total * qty
                var totalQtyElement = document.getElementById('total_qty');
                totalQtyElement.value = 'Rp ' + totalQtyResult.toLocaleString('id-ID');
            }

            hitungTotalQty();

            // Tambahkan event listener untuk input qty
            var qtyInput = document.querySelector('input[name="qty"]');
            qtyInput.addEventListener('input', function() {
                // Hitung total * qty saat input qty berubah
                hitungTotalQty();
            });

            <?php foreach ($kategori as $category) { ?>
                var makananSelect<?= $category['id_kategori'] ?> = document.getElementById('makanan<?= $category['id_kategori'] ?>');
                var hargaMakanan<?= $category['id_kategori'] ?> = document.getElementById('harga_makanan<?= $category['id_kategori'] ?>');

                // Tambahkan panggilan fungsi saat halaman dimuat
                updateHarga(makananSelect<?= $category['id_kategori'] ?>, hargaMakanan<?= $category['id_kategori'] ?>);

                makananSelect<?= $category['id_kategori'] ?>.addEventListener('change', function() {
                    // Panggil fungsi saat opsi berubah
                    updateHarga(makananSelect<?= $category['id_kategori'] ?>, hargaMakanan<?= $category['id_kategori'] ?>);

                    // Hitung total saat opsi berubah
                    hitungTotal();
                    // Hitung total * qty saat opsi berubah
                    hitungTotalQty();
                });
            <?php } ?>

            // Panggil fungsi hitungTotal saat halaman dimuat untuk menginisialisasi total
            hitungTotal();

            // Fungsi untuk mengupdate harga berdasarkan opsi terpilih
            function updateHarga(selectElement, hargaElement) {
                var selectedMakanan = selectElement.options[selectElement.selectedIndex];
                var selectedMakananHarga = selectedMakanan.getAttribute('data-harga');

                // Format angka dengan toLocaleString()
                hargaElement.innerText = selectedMakananHarga ? 'Rp ' + parseFloat(selectedMakananHarga).toLocaleString('id-ID') : '';
            }

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

                // Hitung total * qty saat total berubah
                hitungTotalQty();
            }
            // Fungsi untuk menghitung total * qty
            function hitungTotalQty() {
                var totalQty = 0;
                var qtyElement = document.querySelector('input[name="qty"]');
                var qty = parseInt(qtyElement.value) || 0;

                <?php foreach ($kategori as $category) { ?>
                    var hargaMakanan<?= $category['id_kategori'] ?> = parseFloat(document.getElementById('harga_makanan<?= $category['id_kategori'] ?>').innerText.replace(/[^\d]/g, '')) || 0;
                    totalQty += hargaMakanan<?= $category['id_kategori'] ?>;
                <?php } ?>

                // Hitung total * qty
                var totalQtyResult = totalQty * qty;

                // Tampilkan total * qty
                var totalQtyElement = document.getElementById('total_qty');
                totalQtyElement.value = 'Rp ' + totalQtyResult.toLocaleString('id-ID');
            }

            // Panggil fungsi hitungTotal saat halaman dimuat untuk menginisialisasi total
            hitungTotal();

            // Tambahkan event listener untuk input qty
            var qtyInput = document.querySelector('input[name="qty"]');
            qtyInput.addEventListener('input', function() {
                // Hitung total * qty saat input qty berubah
                hitungTotalQty();
            });

            // Tambahkan event listener untuk setiap select makanan
            <?php foreach ($kategori as $category) { ?>
                var makananSelect<?= $category['id_kategori'] ?> = document.getElementById('makanan<?= $category['id_kategori'] ?>');
                var hargaMakanan<?= $category['id_kategori'] ?> = document.getElementById('harga_makanan<?= $category['id_kategori'] ?>');

                // Tambahkan panggilan fungsi saat halaman dimuat
                updateHarga(makananSelect<?= $category['id_kategori'] ?>, hargaMakanan<?= $category['id_kategori'] ?>);

                makananSelect<?= $category['id_kategori'] ?>.addEventListener('change', function() {
                    // Panggil fungsi saat opsi berubah
                    updateHarga(makananSelect<?= $category['id_kategori'] ?>, hargaMakanan<?= $category['id_kategori'] ?>);

                    // Hitung total saat opsi berubah
                    hitungTotal();
                    // Hitung total * qty saat opsi berubah
                    hitungTotalQty();
                });
            <?php } ?>
        });

        function konfirmasi() {
            return confirm('Apakah Anda yakin ingin menghapus menu ini?');
        }
    </script>

</body>

</html>