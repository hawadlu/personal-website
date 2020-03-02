<html lang="English">
<!--Allows the nav to scale for mobile devices-->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles.css">

    <div class="topnav" id="myTopnav">
        <a href="index.php" class="active">Home</a>
        <a href="about.php">About Me</a>
        <a href="examples.php">Examples</a>
        <a href="education.php">Education</a>
        <a href="edit.php">Play Around</a>
        <?
        //Check if logged in. Place a logout button if required
        if (isset($_SESSION['loggedin'])) {
            ?>
            <a href="logout.php" style="color: #d90029">Logout</a>
            <?php
        }
        ?>
        <a href="javascript:void(0);" class="icon" onclick="loadHamburger()">
            <img alt = "Hamburger icon" src="images/Hamburger Icon.png">
        </a>
    </div>

    <script>
    </script>
</html>

