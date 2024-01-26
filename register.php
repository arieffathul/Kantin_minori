<?php
echo '<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>';
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

// Koneksi ke database (gantilah dengan informasi koneksi yang sesuai)
$koneksi = mysqli_connect("localhost", "root", "", "project_new");
$error = '';

// Periksa apakah form login telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = strtolower(stripslashes($_POST["username"]));
    $password = mysqli_real_escape_string($koneksi, $_POST["password"]);
    $password2 = mysqli_real_escape_string($koneksi, $_POST["password2"]);

    // Cek apakah username sudah terdaftar
    $result = mysqli_query($koneksi, "SELECT username FROM admin WHERE username = '$username'");

    if (mysqli_fetch_assoc($result)) {
        $error = "Username sudah terdaftar";
    } elseif ($password !== $password2) {
        $error = "Konfirmasi password tidak sesuai";
    } else {
        // Enkripsi password
        $password = password_hash($password, PASSWORD_DEFAULT);

        // Tambahkan user ke database
        $result = mysqli_query($koneksi, "INSERT INTO admin (username, password) VALUES ('$username', '$password')");

        // Cek apakah data berhasil ditambahkan
        if ($result) {
            echo "<script>
                    alert('Akun anda akan segera diproses');
                    document.location.href='login.php';
                </script>";
        } else {
            echo "<script>
                    alert('Registrasi gagal!');
                </script>";
        }
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
    <title>Register</title>
</head>
<link rel="stylesheet" href="css/login.css">

<body>
    <div class="container">
        <h1>DATABASE KANTIN MINORI</h1>
        <div class="login-logo-container">
            <div class="login">
                <h2><b>Register</b></h2>

                <form action="" method="post">
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
                            <label for="password">
                                <td>Konfirmasi</td>
                                <td>:</td>
                            </label>
                            <td>
                                <input type="password" name="password2" id="" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="submit" name="register">Register</button>
                            </td>
                            <td></td>
                            <td>
                                <a href="login.php">Batal</a>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

            <div class="logo2">
                <img src="img/login.png" width="180px" align="center">
            </div>
        </div>
    </div>
</body>

</html>