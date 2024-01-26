<?php
session_start();

if( !isset($_SESSION["user"])){
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
            <a href="user_makanan.php">Makanan</a>
            <a href="user_menu.php">Menu</a>
            <a href="user_request.php">Request</a>
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