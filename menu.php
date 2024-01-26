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
    <title>Daftar Menu</title>
    <link rel="stylesheet" href="css/list.css">
</head>

<body>
    <?php

    include 'header.php';

    require 'function.php';

    // Mengambil data dari mysql
    // $now = date('Y-m-d');
    $data = query("SELECT makanan.*, detail_menu.*, menu.*
                FROM makanan LEFT JOIN detail_menu ON makanan.id_makanan = detail_menu.id_makanan
                LEFT JOIN menu ON menu.id_menu = detail_menu.id_menu
                WHERE menu.status = 'acc'
                ORDER BY makanan.makanan ASC");
    // // echo '<pre>' . print_r($data, true) . '</pre>';
    // die;
    ?>

    <!-- Filter -->
    <div class="filter">
        <h1>DAFTAR MENU</h1>
        <form action="" method="post">
            <table class="menu">
                <tr>
                    <td colspan="3" align="center">
                        <a href="tambah_sehari.php">Tambah Menu</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <!-- Fungsi bulan -->
    <div class="bulan_tahun">
        <?php
        $currentMonth = isset($_POST['month']) ? (int)$_POST['month'] : date('n');
        $currentYear = isset($_POST['year']) ? (int)$_POST['year'] : date('Y');

        if (isset($_POST['reset'])) {
            $currentMonth = date('n');
            $currentYear = date('Y');
        }

        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if ($action) {
            list($currentMonth, $currentYear) = updateMonthYear($currentMonth, $currentYear, $action);
        }

        // Menampilkan antarmuka
        displayMonthYear($currentMonth, $currentYear);
        // displayUpdatedMonthYear($currentMonth, $currentYear);

        // Fungsi untuk menampilkan bulan dan tahun pada antarmuka
        function displayMonthYear($month, $year)
        {
            echo '<form method="post" action="">';
            echo '<input type="number" name="month" id="month" value="' . $month . '" min="1" max="12" required>';
            echo '<input type="number" name="year" id="year" value="' . $year . '" required>';
            echo '<button type="submit" name="action" value="prev">&lt;</button>';
            displayUpdatedMonthYear($month, $year);
            echo '<span><button type="submit" name="action" value="next">&gt;</button></span><br>';
            echo '<button type="submit" name="reset" value="reset" class="reset">Hari Ini</button>';
            echo '</form>';
        }

        // Fungsi untuk menampilkan pesan bulan dan tahun yang diupdate
        function displayUpdatedMonthYear($updatedMonth, $updatedYear)
        {
            $monthNames = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];
            echo '<span class="bulan">' . $monthNames[$updatedMonth] . ' ' . $updatedYear . '</span>';
        }



        // Fungsi untuk mengupdate bulan dan tahun berdasarkan aksi
        function updateMonthYear($currentMonth, $currentYear, $action)
        {
            if ($action == 'next') {
                $currentMonth++;
                if ($currentMonth > 12) {
                    $currentMonth = 1;
                    $currentYear++;
                }
            } elseif ($action == 'prev') {
                $currentMonth--;
                if ($currentMonth < 1) {
                    $currentMonth = 12;
                    $currentYear--;
                }
            }

            return [$currentMonth, $currentYear];
        }

        // Logika utama
        // echo $currentYear
        ?>
    </div>

    <div class="list">
        <table border="1px" cellspacing="0" class="tanggal">
            <thead>
                <tr>
                    <th>Senin</th>
                    <th>Selasa</th>
                    <th>Rabu</th>
                    <th>Kamis</th>
                    <th>Jumat</th>
                    <th>Sabtu</th>
                    <th>Minggu</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $firstDayOfMonth = date('N', strtotime("$currentYear-$currentMonth-01"));
                $lastDayOfMonth = date('t', strtotime("$currentYear-$currentMonth-01"));

                $dayOfWeek = 2;
                $currentDate = 1;
                $dayOfWeek2 = 2;
                $currentDate2 = 1;
                $week = 1;
                $monthlyTotal = 0;

                while ($currentDate <= $lastDayOfMonth) {
                    echo '<tr>';

                    $weeklyTotal[$week] = 0;

                    for ($i = 1; $i <= 7; $i++) {
                        if ($dayOfWeek >= $firstDayOfMonth && $currentDate <= $lastDayOfMonth) {
                            // Membuat objek DateTime dari string tanggal
                            $dateString = "$currentYear-$currentMonth-$currentDate";
                            $dateTimeObj = new DateTime($dateString);

                            // Format tanggal sesuai kebutuhan (misalnya, 'Y-m-d')
                            $formattedDate = $dateTimeObj->format('Y-m-d');

                            $total_menu_result = mysqli_query($koneksi, "SELECT total_menu FROM menu WHERE tanggal = '$formattedDate' AND status = 'acc'");
                            $total_menu_row = mysqli_fetch_assoc($total_menu_result);
                            if ($total_menu_row) {
                                $total_menu[$currentDate] = $total_menu_row['total_menu'];
                                $weeklyTotal[$week] += $total_menu[$currentDate];
                                $monthlyTotal += $total_menu[$currentDate];

                                // Membuat URL dengan menggunakan objek tanggal
                                echo '<td>' . $currentDate . '<a href="detail_menu.php?date=' . urlencode($formattedDate) . '">Detail Menu</a></td>';
                                $currentDate++;

                                // echo '<pre>' . var_export($total_menu, true) . '</pre>';
                            } else {
                                $total_menu[$currentDate] = '';

                                // Membuat URL dengan menggunakan objek tanggal
                                echo '<td>' . $currentDate . '<a href="tambah_sehari.php?date=' . urlencode($formattedDate) . '">Detail Menu</a></td>';
                                $currentDate++;
                            }
                        } else {
                            echo '<td></td>';
                        }
                        $dayOfWeek++;
                    }


                    echo '<td rowspan="2" bgcolor="lightgrey" align="center" class="total">' . $weeklyTotal[$week] . '</td>'; // Kolom total per minggu
                    // Tambahkan baris ini untuk menambahkan sel kosong di bawah setiap current date
                    echo '</tr>';
                    echo '<tr>';


                    for ($i = 1; $i <= 7; $i++) {
                        // Kode PHP yang memberikan data ke dalam sel class "harian"
                        if ($dayOfWeek2 >= $firstDayOfMonth && $currentDate2 <= $lastDayOfMonth) {
                            echo '<td style="height: 35px" class="harian">' . $total_menu[$currentDate2] . '</td>';
                            $currentDate2++;
                        } else {
                            echo '<td style="height: 35px" class="harian"></td>';
                        }
                        $dayOfWeek2++;
                    }
                    echo '</tr>';
                    $week++;
                }
                ?>
                <tr>
                    <td style="height: 35px" colspan="7" class="harian"> Total Bulan Ini </td>
                    <td style="height: 35px" align="center"><?= $monthlyTotal ?></td>
                </tr>
            </tbody>
        </table>

    </div>

</body>

</html>