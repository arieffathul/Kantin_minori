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
    <title>Detail Paket</title>
    <link rel="stylesheet" href="css/list.css">
</head>

<?php

require 'function.php';

$foods = query("SELECT makanan.*, kategori.*
                FROM makanan LEFT JOIN kategori ON makanan.id_kategori = kategori.id_kategori
                WHERE makanan.status = 'acc'
                ORDER BY makanan.makanan ASC");
$kategori = query("SELECT * FROM kategori ORDER BY kategori ASC");

$detail_id = $_GET['detail'];

// $detail_menu = query("SELECT * FROM menu WHERE tanggal = '$detail_tanggal' ORDER BY tanggal ASC;");
// $detail_menu = query("SELECT makanan.*, detail_menu.*, menu.*
//                     FROM makanan RIGHT JOIN detail_menu ON makanan.id_makanan = detail_menu.id_makanan
//                     LEFT JOIN menu ON menu.id_menu = detail_menu.id_menu
//                     WHERE menu.tanggal = '$detail_tanggal' AND menu.status = 'acc'
//                     ORDER BY menu.tanggal ASC;");

$detail_paket = query("SELECT paket.*, detail_paket.*, makanan.* FROM paket
                        RIGHT JOIN detail_paket ON paket.id_paket = detail_paket.id_paket
                        LEFT JOIN makanan ON detail_paket.id_makanan = makanan.id_makanan
                        WHERE detail_paket.id_paket = '$detail_id'
                        ORDER BY paket.id_paket ASC");

if ($detail_paket) {
    foreach ($detail_paket as $daftar_paket) {
        $daftar_id_makanan[] = $daftar_paket['id_makanan']; // Menambahkan nilai 'makanan' ke dalam array
        $id_paket_edit = $daftar_paket['id_paket'];
        $nama_paket = $daftar_paket['nama_paket'];

        // $id_detail_menu[] = $daftar_menu['id_dm'];
    }
    $detail_id_makanan = implode(", ", $daftar_id_makanan);
    // $detail_id_menu = implode(", ", $daftar_id_makanan);
} else {
    $detail_id_makanan = '';
}


if (isset($_POST['edit'])) {
    $nama = $_POST['nama'];
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

    // Cek apakah nama paket baru tidak sama dengan nama paket lain
    $checkNamaPaket = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM paket WHERE nama_paket = '$nama' AND id_paket != '$id_paket_edit'");
    $row = mysqli_fetch_assoc($checkNamaPaket);
    $jumlahPaketSama = $row['count'];

    if ($jumlahPaketSama > 0) {
        $error = "$nama sudah ada";
        $_SESSION['failure_message'] = $error;
        echo "<script>history.back()</script>";
        exit;
    } else {
        if ($hitmakanan == 0) {
            $error = 'harap input minimal 1 makanan';
        } else {
            $update_paket = mysqli_query($koneksi, "UPDATE paket SET total= '$total', nama_paket = '$nama' WHERE id_paket = '$id_paket_edit'");

            if ($update_paket) {
                $id_paket = mysqli_insert_id($koneksi);

                // echo $id_menu;

                for ($i = 0; $i < $hitmakanan; $i++) {
                    $id_makanan = $makanan[$i];
                    $id_kategori = query("SELECT * FROM makanan WHERE id_makanan = $id_makanan");

                    // Gunakan foreach untuk mengiterasi hasil query
                    foreach ($id_kategori as $detail_kategori) {
                        $detail_kategori = $detail_kategori['id_kategori']; // Gunakan $detail_kategori daripada $id_kategori
                    }
                    $update_detail = mysqli_query($koneksi, "UPDATE detail_paket
                                            JOIN makanan ON detail_paket.id_makanan = makanan.id_makanan
                                            SET detail_paket.id_makanan = $id_makanan
                                            WHERE makanan.id_kategori = $detail_kategori AND id_paket = $id_paket_edit");
                    // echo $id_makanan . '&' . $detail_kategori . '&' . $id_menu_edit . '<br>';
                }

                // Get the previously selected food items
                $previouslySelectedMakanan = explode(", ", $detail_id_makanan);

                // Identify the food items that are newly selected
                $newlySelectedMakanan = array_diff($makanan, $previouslySelectedMakanan);

                // Insert the newly selected food items into the database
                if (!empty($newlySelectedMakanan)) {
                    foreach ($newlySelectedMakanan as $newMakanan) {
                        $newMakanan = (int)$newMakanan;
                        $id_kategori = query("SELECT * FROM makanan WHERE id_makanan = $newMakanan");

                        // Gunakan foreach untuk mengiterasi hasil query
                        foreach ($id_kategori as $detail_kategori) {
                            $detail_kategori = $detail_kategori['id_kategori']; // Gunakan $detail_kategori daripada $id_kategori
                        }

                        // Cek apakah kombinasi id_makanan dan id_menu sudah ada
                        $existingCombination = query("SELECT * FROM detail_paket WHERE id_paket = $id_paket_edit AND id_makanan = $newMakanan");

                        if (empty($existingCombination)) {
                            // Lakukan operasi penyisipan ke dalam database hanya jika kombinasi belum ada
                            $insertQuery = "INSERT INTO detail_paket (id_paket, id_makanan) VALUES ($id_paket_edit, $newMakanan)";
                            mysqli_query($koneksi, $insertQuery);
                            // echo "Inserted Makanan ID: $newMakanan, Kategori ID: $detail_kategori, Menu ID: $id_menu_edit <br>";
                        } else {
                            // echo "Combination already exists for Makanan ID: $newMakanan, Menu ID: $id_menu_edit <br>";
                        }
                    }
                }

                // Identify the food items that were removed
                $removedMakanan = array_diff($previouslySelectedMakanan, $makanan);
                if (!empty($removedMakanan)) {
                    $removedMakananIds = implode(", ", $removedMakanan);
                    // Perform the necessary database deletion operation, for example:
                    $deleteQuery = "DELETE FROM detail_paket WHERE id_makanan IN ($removedMakananIds) AND id_paket = $id_paket_edit";
                    mysqli_query($koneksi, $deleteQuery);
                    // echo "Removed Makanan IDs: $removedMakananIds <br>";
                }

                if ($update_detail) {
                    $got = "Paket berhasil diupdate";
                    $_SESSION['success_message'] = $got;
                    echo "<script>history.back()</script>";
                    exit;
                } else {
                    echo "<script>alert('Gagal melakukan update Makanan!')</script>";
                }
            }
        }
    }
}

if (isset($_POST['hapus'])) {
    $hapus_paket = mysqli_query($koneksi, "DELETE FROM paket WHERE id_paket = '$id_paket_edit'");

    echo "<script>
        alert('Paket berhasil dihapus')
        document.location.href = 'paket.php'
        </script>";
}

if (isset($_SESSION['success_message'])) {
    $got = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // hapus pesan dari session
}

if (isset($_SESSION['failure_message'])) {
    $error = $_SESSION['failure_message'];
    unset($_SESSION['failure_message']); // hapus pesan dari session
}

?>

<body class="tambah_menu">

    <!-- Form -->
    <div class="container">
        <div class="form" id="tambah">
            <div class="judul">
                <h2> Detail Paket </h2>
            </div>

            <div class="isian">
                <form action="" method="post">
                    <table class="table">
                        <tr>
                            <td>Nama Paket</td>
                            <td>:</td>
                            <td><input type="text" name="nama" value="<?= $nama_paket ?>" min="0" required></td>
                        </tr>
                    </table>
                    <?php if (!empty($error)) { ?>
                        <p class="alert"><?php echo $error; ?></p>
                    <?php } ?>
                    <?php if (!empty($got)) { ?>
                        <p class="got" id="got"><?php echo $got; ?></p>
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
                                                $isSelected = in_array($food['id_makanan'], explode(", ", $detail_id_makanan));
                                            ?>
                                                <option value="<?= $food['id_makanan'] ?>" data-harga="<?= $food['harga'] ?>" <?= $isSelected ? 'selected' : ''; ?>>
                                                    <?= ucwords($food['makanan']) ?>
                                                </option>
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
                        </tbody>
                    </table>
                    <table class="button-container">
                        <tr>
                            <td colspan="3" align="center">
                                <button type="submit" name="edit">Update</button>
                                <button type="submit" name="hapus" onclick="return konfirmasi()">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" class="back"><a href="paket.php">Kembali</a></td>
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
            }
        });

        function konfirmasi() {
            return confirm('Apakah Anda yakin ingin menghapus paket ini?');
        }
    </script>

</body>

</html>