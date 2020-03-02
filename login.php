<?php
        session_start();
        if (isset($_SESSION['loggedin'])) {
            header('Location: edit.php');
            exit();
        }

        require("connect.php");
        require("head.php");
        require("header.php");

        //Check to see if an error message has been set
        $errorMessage = null;
        if (isset($_COOKIE['errorMsg'])) {
            //Only output if the value is not a number
            if (!is_numeric($_COOKIE['errorMsg'])) {
                $errorMessage = $_COOKIE['errorMsg'];
            }

            //Delete the cookie
            setcookie('errorMsg', time() - 3600);
        }
?>

    <!--Disable scrolling-->
    <html lang="English">
    <body class="background-img">
        <div style="text-align: center;">
            <form action="authenticate.php" method="post">
                <div class="login">
                    <div>
                        <h2>
                            Login
                        </h2>
                    </div>
                    <div>
                        <input class="loginBox" type="text" name="username" placeholder="Username" id="username" required>
                    </div>
                    <div>
                        <input class="loginBox" type="password" name="password" placeholder="Password" id="password" required>
                    </div>
                    <div>
                        <input class="indexButton loginBox" type="submit" value="Login">
                    </div>
                </div>
            </form>
        </div>
    </body>
    <script>
        //Set the error/success message timeout
        $(document).ready(function(){
            $('.alert').delay(10000).fadeOut(300);
        });
    </script>

    <?php
        require("footer.php");

    //Print error/success messages
    //Show any error messages if required
    if ($errorMessage != null) {
        ?>
        <div class="alert alert-danger" style="width: 100%; position: fixed">
            <strong>Operation failed!</strong> <?php echo $errorMessage; ?>
        </div>
        <?
    }
    ?>
</html>
