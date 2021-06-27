<?php
    require("header.php");
    require("head.php");

    if (!isset($_COOKIE["CookiePolicy"]) || $_COOKIE["CookiePolicy"] != 1) {
        setcookie("CookiePolicy", 1, time() + 600);
        $show = "grid";
        $opacity = "50%";
    } else {
        $show = "none";
        $opacity = "100%";
    }

    ?>
    <html lang="English">
    <!--Creates the cookie privacy popup-->
    <div id = "popup" class="popup-container" style="display: <?php echo $show;?>">
        <div class="popup-close">
            <span onclick="hidePopup()" class="closeEditAddModal">&times;</span>
        </div>
        <div class="popup-content roundAll">
            <!--The text and other information displayed in the cookie popup-->
            <div style="text-align: center; padding-top: 10px">
                <h1 style="color: black;">
                    Cookies
                </h1>
            </div>
            <p style="padding: 10px;">
                My website uses google analytics and cookies cookies to power the experience (I promise that I'm not tracking you, but if you want to look at the source code on github please feel free to do so).
                Click <a class="onHover" href="about.php?privacy=true">here</a>
                to view my privacy and cookie policies. By continuing to use this website you consent to the
                use of cookies.
                <br>
                <br>
            </p>
        </div>
    </div>
    <!--Disable scrolling-->
    <body id = "body" class="background-img" style="opacity: <?php echo $opacity;?>; overflow: hidden">
        <div style="text-align: center">
            <div class="indexNav">
                <div>
                    <a href = "about.php">
                        <button class="indexButton">
                            <strong class = "indexButtonText">
                                About Me
                            </strong>
                        </button>
                    </a>
                </div>
                <div>
                    <a href="examples.php">
                        <button class="indexButton">
                            <strong class = "indexButtonText">
                                Examples
                            </strong>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </body>

    <script>
        //hide the popup if required
        function hidePopup() {
            //hide the popup
            document.getElementById("popup").style.display = "none";

            //restore the opacity
            document.getElementById("body").style.opacity = "100%";
        }
    </script>

    <?php
        require("footer.php");
    ?>
</html>