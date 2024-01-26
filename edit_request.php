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

$id_menus = $_GET['menu'];

// $detail_menu = query("SELECT * FROM menu WHERE tanggal = '$detail_tanggal' ORDER BY tanggal ASC;");
$detail_menu = query("SELECT  menu.*, detail_menu.*, makanan.*, req_menu.*, admin.*
                    FROM menu RIGHT JOIN detail_menu ON menu.id_menu = detail_menu.id_menu
                    LEFT JOIN makanan ON makanan.id_makanan = detail_menu.id_makanan
                    LEFT JOIN req_menu ON menu.id_menu = req_menu.id_menu
                    LEFT JOIN admin ON req_menu.id_admin = admin.id_admin
                    WHERE menu.id_menu = '$id_menus' AND menu.status = 'requested'
                    ORDER BY menu.tanggal ASC;");

if ($detail_menu) {
    foreach ($detail_menu as $daftar_menu) {
        $daftar_id_makanan[] = $daftar_menu['id_makanan']; // Menambahkan nilai 'makanan' ke dalam array
        $id_menu_edit = $daftar_menu['id_menu'];
    }
    $detail_nama = $detail_menu[0]['id_admin'];
    $detail_tanggal = $detail_menu[0]['tanggal']; // Ambil tanggal dari indeks 0
    $detail_id_makanan = implode(", ", $daftar_id_makanan);
} else {
    $detail_qty = '1';
    $detail_id_makanan = '';
}

$query_nama = query("SELECT username FROM admin WHERE id_admin = '$detail_nama'");

if ($query_nama && isset($query_nama[0]['username'])) {
    $nama = $query_nama[0]['username'];
} else {
    $nama = ''; // Tetapkan nilai default jika 'nama' tidak ada dalam hasil
}


if (isset($_POST['edit'])) {
    $nama1 = $_POST['nama'];
    $total = $_POST['total'];
    $tanggal = $_POST['tanggal'];
    $hitmakanan = count(array_values(array_filter($_POST['makanan'])));
    $makanan = array_values(array_filter($_POST['makanan']));
    // Menghilangkan karakter 'Rp ' dari string
    $total = str_replace('Rp ', '', $total);

    // Menghilangkan karakter ',' untuk memastikan format numerik yang benar
    $total = str_replace(',', '', $total);

    // Mengonversi string menjadi integer
    $total = (int)$total;
    $total = $total * 1000;

    if ($detail_nama == $id_admin) {
        if ($hitmakanan == 0) {
            $error = 'harap input minimal 1 makanan';
        } else {
            $update_menu = mysqli_query($koneksi, "UPDATE menu SET total_menu= '$total', tanggal = '$tanggal' WHERE id_menu = '$id_menu_edit'");
            $update_request = mysqli_query($koneksi, "UPDATE req_menu SET tgl_request = NOW()");

            if ($update_menu) {
                $id_menu = mysqli_insert_id($koneksi);

                // echo $id_menu;

                for ($i = 0; $i < $hitmakanan; $i++) {
                    $id_makanan = $makanan[$i];
                    $id_kategori = query("SELECT * FROM makanan WHERE id_makanan = $id_makanan");

                    // Gunakan foreach untuk mengiterasi hasil query
                    foreach ($id_kategori as $detail_kategori) {
                        $detail_kategori = $detail_kategori['id_kategori']; // Gunakan $detail_kategori daripada $id_kategori
                    }
                    $update_detail = mysqli_query($koneksi, "UPDATE detail_menu
                                                JOIN makanan ON detail_menu.id_makanan = makanan.id_makanan
                                                SET detail_menu.id_makanan = $id_makanan
                                                WHERE makanan.id_kategori = $detail_kategori AND id_menu = $id_menu_edit");
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
                        $existingCombination = query("SELECT * FROM detail_menu WHERE id_menu = $id_menu_edit AND id_makanan = $newMakanan");

                        if (empty($existingCombination)) {
                            // Lakukan operasi penyisipan ke dalam database hanya jika kombinasi belum ada
                            $insertQuery = "INSERT INTO detail_menu (id_menu, id_makanan) VALUES ($id_menu_edit, $newMakanan)";
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
                    $deleteQuery = "DELETE FROM detail_menu WHERE id_makanan IN ($removedMakananIds) AND id_menu = $id_menu_edit";
                    mysqli_query($koneksi, $deleteQuery);
                    // echo "Removed Makanan IDs: $removedMakananIds <br>";
                }

                if ($update_detail) {
                    $got = "Menu berhasil diupdate";
                    $_SESSION['success_message'] = $got;
                    echo "<script>history.back()</script>";
                    exit;
                } else {
                    echo "<script>alert('Gagal melakukan update Makanan!')</script>";
                }
            }
        }
    } else {
        $error = 'Anda tidak bisa mengubah request orang lain';
    }
}

if (isset($_POST['hapus'])) {
    if ($detail_nama == $id_admin) {
        $hapus_menu = mysqli_query($koneksi, "DELETE FROM menu WHERE id_menu = '$id_menu_edit'");

        echo "<script>
            alert('Menu berhasil dihapus')
            document.location.href = 'user_request1.php'
            </script>";
    } else {
        $error = 'Anda tidak bisa mengubah request orang lain';
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
                <h2> Tambah Menu </h2>
            </div>

            <div class="isian">
                <form action="" method="post">
                    <table class="table">
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td><input type="text" name="nama" value="<?= $nama ?>" required readonly></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td><input type="date" name="tanggal" value="<?= $detail_tanggal ?>" required></td>
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
                            <td align="center" class="back"><a href="user_request1.php">Kembali</a></td>
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
            return confirm('Apakah Anda yakin ingin menghapus menu ini?');
        }
    </script>

</body>

</html>