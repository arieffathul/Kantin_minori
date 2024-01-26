   <?php

   if( !isset($_SESSION["user"])){
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
                <li><a href="user_home.php">Home</a></li>
                <li><a href="user_makanan.php">Makanan</a></li>
                <li><a href="user_menu.php">Menu</a></li>
                <li><a href="user_request.php">Request</a></li>
                <li><a href="logout.php" onclick="return konfirmasi()">Log out</a></li>
            </ul>
        </nav>
    </header>
    <script>
        function konfirmasi() {
            return confirm('Apakah Anda yakin ingin Log Out?');
        }
    </script>