<?php
        session_start();
        if (isset($_SESSION['loggedin'])) {
            header('Location: edit.php');
            exit();
        }

        require("connect.php");
        require("head.php");
        require("header.php");

        //Todo look into restricting the session time so that the user is automatically logged out. This link may be useful https://solutionfactor.net/blog/2014/02/08/implementing-session-timeout-with-php/
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

    <?php
        require("footer.php");
    ?>
</html>
