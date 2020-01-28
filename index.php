<?php
    if (!isset($_COOKIE["CookiePolicy"])) {
        if ($_COOKIE["CookiePolicy"] != 1) {
            setcookie("CookiePolicy", 1, time() + 600);
            ?>
            <script type="text/javascript" src="https://code.jquery.com/jquery-1.8.2.js"></script>
            <script type='text/javascript'>
                $(function () {
                    var overlay = $('<div id="overlay"></div>');
                    overlay.show();
                    overlay.appendTo(document.body);
                    $('.popup').show();
                    $('.close').click(function () {
                        $('.popup').hide();
                        overlay.appendTo(document.body).remove();
                        return false;
                    });

                    $('.x').click(function () {
                        $('.popup').hide();
                        overlay.appendTo(document.body).remove();
                        return false;
                    });
                });
            </script>

            <?php
        }
    }

    require("head.php");
    require("header.php");

    ?>
    <html lang="English">
    <!--Creates the cookie privacy popup-->
    <div class='popup rounded'>
        <div class='cnt223'>
            <!--The text and other information displayed in the cookie popup-->
            <h1 style="color: black;">
                <div style="text-align: center;">
                    Important Notice
                </div>
            </h1>
            <p>
                Our website collects data and uses cookies. Click <a class="onHover" href="about.php">here</a>
                to view our privacy and cookie policies. By continuing to use this website you consent to the
                use of cookies and data.
                <br/>
                <br/>
                <a href='' class='close onHover'>Close</a>
            </p>
        </div>
    </div>
    <!--Disable scrolling-->
    <body class="background-img">
        <div style="text-align: center">
            <div class="indexNav">
                <div>
                    <a href = "about.php">
                        <button class="indexButton">
                            <strong>
                                About Me
                            </strong>
                        </button>
                    </a>
                </div>
                <div>
                    <a href="examples.php">
                        <button class="indexButton">
                            <strong>
                                Examples
                            </strong>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </body>

    <?php
        require("footer.php");
    ?>
</html>