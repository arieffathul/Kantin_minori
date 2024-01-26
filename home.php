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
    <title>Home</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <div class="container">
        <h1>PILIH ACTION</h1>
        <div class="option">
            <a href="makanan.php">Kelola Makanan</a>
            <a href="kategori.php">Kelola Kategori</a>
            <a href="paket.php">Kelola Paket</a>
            <a href="menu.php">Kelola Menu</a>
            <a href="user.php">Kelola User</a>
            <a href="request.php">Kelola Request</a>
            <a href="logout.php" onclick="return konfirmasi()">Log out</a>
        </div>
    </div>
</body>
<script>
    function konfirmasi() {
        return confirm('Apakah Anda yakin ingin Log Out?');
    }
</script>
</html>