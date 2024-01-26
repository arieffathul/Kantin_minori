<?php
session_start();

if (isset($_SESSION["admin"])) {
    header("location: home.php");
    exit;
}
if (isset($_SESSION["user"])) {
    header("location: user_home.php");
    exit;
}

// Koneksi ke database (gantilah dengan informasi koneksi yang sesuai)
$koneksi = mysqli_connect("localhost", "root", "", "project_new");
$error = '';

// Periksa apakah form login telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tangkap data dari form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Lakukan sanitasi data
    $username = htmlspecialchars($username);
    $password = htmlspecialchars($password);

    // Query untuk memeriksa keberadaan username di database
    $query = "SELECT * FROM admin WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    // Periksa apakah query berhasil dijalankan dan hasilnya tidak kosong
    if ($result && mysqli_num_rows($result) > 0) {
        // Data username valid, cek password
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row["password"])) {
            // Password juga valid, catat waktu login
            $loginTime = date("Y-m-d H:i:s");

            if ($row['role'] == 'guest') {
                $error = "Akun anda belum diterima.";
            } elseif ($row['role'] == 'user') {

                // Update waktu login ke dalam tabel admin
                $updateQuery = "UPDATE admin SET last_login = '$loginTime' WHERE username = '$username'";
                mysqli_query($koneksi, $updateQuery);

                // Set session
                $_SESSION["user"] = true;
                $_SESSION["id_admin"] = $row['id_admin']; // Tambahkan baris ini untuk menyimpan id_admin di sesi
                header("Location: user_home.php");
            } else {

                // Update waktu login ke dalam tabel admin
                $updateQuery = "UPDATE admin SET last_login = '$loginTime' WHERE username = '$username'";
                mysqli_query($koneksi, $updateQuery);

                // Set session
                $_SESSION["admin"] = true;

                header("Location: home.php");
                exit();
            }
        } else {
            // Password tidak valid, tandai dengan variabel $error
            $error = "Password salah.";
        }
    } else {
        // Data username tidak valid, tandai dengan variabel $error
        $error = "Username tidak ditemukan.";
    }
}

// Tutup koneksi ke database
mysqli_close($koneksi);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<link rel="stylesheet" href="css/login.css">

<body>
    <div class="container">
        <h1>DATABASE KANTIN MINORI</h1>
        <div class="login-logo-container">
            <div class="login">
                <h2><b>Login</b></h2>


                <form action="" method="post">
                    <!-- Menambahkan area untuk menampilkan pesan kesalahan -->
                    <div class="error">
                        <!-- Menambahkan area untuk menampilkan pesan kesalahan -->
                        <?php if (!empty($error)) { ?>
                            <p><?php echo $error; ?></p>
                        <?php } ?>
                    </div>
                    <table>
                        <tr class="username">
                            <label for="username">
                                <td width="15px">Username</td>
                                <td>:</td>
                            </label>
                            <td>
                                <input type="text" name="username" id="" required>
                            </td>
                        </tr>
                        <tr>
                            <label for="password">
                                <td>Password</td>
                                <td>:</td>
                            </label>
                            <td>
                                <input type="password" name="password" id="" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="submit" name="login">Login</button>
                            </td>
                            <td></td>
                            <td>
                                <a href="register.php">Register</a>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

            <div class="logo">
                <img src="img/login.png" width="180px" align="center">
            </div>
        </div>
    </div>
</body>

</html>