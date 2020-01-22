<html lang="English">
    <!--Allows the nav to scale for mobile devices-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    //Gets the current file name. Used to determine the opacity of the header and nav.
    if (basename($_SERVER["PHP_SELF"]) == "index.php") {
        $opacityNav = "50%";
    } else {
        $opacityNav = "100%";
    }
    ?>

    <div style="text-align: center;">
        <div class="dropdown">
            <!--Display hamburger menu icon if required-->
            <div class="hamburger">
                <img class="hamburgerImg" src="images/hamburger icon.png" alt = "Hamburger menu icon">
            </div>
            <div class="dropdown-content">

                <div class="nav-grid-container" style="--navOpacity: <?php echo $opacityNav;?>;">
                    <div class="hover-effect">
                        <div style="text-align: center;">
                            <a href="index.php">
                                <p>
                                    Home
                                </p>
                            </a>
                        </div>

                    </div>
                    <div class="hover-effect">
                        <div style="text-align: center;">
                            <a href="about.php">
                                <p>
                                    About Me
                                </p>
                            </a>
                        </div>

                    </div>
                    <div class="hover-effect">
                        <div style="text-align: center;">
                            <a href="examples.php">
                                <p>
                                    Examples
                                </p>
                            </a>
                        </div>

                    </div>
                    <div class="hover-effect">
                        <div style="text-align: center;">
                            <a href="education.php">
                                <p>
                                    Education
                                </p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</html>

