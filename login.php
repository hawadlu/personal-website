<html lang="English">
    <?php
        //Start the session
        session_start();
        require("connect.php");
        require("head.php");
        require("header.php");

        // Now we check if the data from the login form was submitted, isset() will check if the data exists.
        //        if ( !isset($_POST['username'], $_POST['password']) ) {
        //            // Could not get the data that should have been sent.
        //            die ('Please fill both the username and password field!');
        //        }
    ?>

    <!--Disable scrolling-->
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

    <?php
        require("footer.php");
    ?>
</html>
