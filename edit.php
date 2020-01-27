<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
//Todo add a function that allows the user to play around with their own items without having to log in. Use cookies and store info in the browser
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

echo "It's lonely here";
?>
<html lang="English">
    <!--Pulls in the head and other required pages-->
    <?php
        require("head.php");
    ?>
    <body class="background-img">
        <div class="page-grid-container">
        <div>
            <p>
                Hello <?php echo $_SESSION['name']; ?>
            </p>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    </body>
    <!--Called last so that it renders at the top-->
    <?php
        require("header.php");;
        //Pull information from the footer page
        require("footer.php");
    ?>
</html>

?>