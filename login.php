<html lang="English">
    <?php
        //Redirect to the edit page if already logged in
        session_start();
        if (isset($_SESSION['loggedin'])) {
            header('Location: edit.php');
            exit();
        }

        require("connect.php");
        require("head.php");
        require("header.php");
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
