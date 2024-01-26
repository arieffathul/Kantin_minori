<?php

if( !isset($_SESSION["admin"])){
    header("location: login.php");
    exit;
}
?>

<!-- Header -->
    <header class="header">
        <div class="img">
            <img src="img/minori1.png">
        </div>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="makanan.php">Makanan</a></li>
                <li><a href="kategori.php">Kategori</a></li>
                <li><a href="paket.php">Paket</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="user.php">User</a></li>
                <li><a href="request.php">Request</a></li>
                <li><a href="logout.php" onclick="return konfirmasi()">Log out</a></li>
            </ul>
        </nav>
    </header>
    <script>
        function konfirmasi() {
            return confirm('Apakah Anda yakin ingin Log Out?');
        }
    </script>