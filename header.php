<html lang="English">
    <!--Links to the stylesheet-->
    <link rel="stylesheet" href="styles.css">
    <!--Makes the page scale properly for mobile devices-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    //Gets the current file name. Used to determine the opacity of the header and nav.
    if (basename($_SERVER["PHP_SELF"]) == "index.php") {
        $opacityHeader = "75%";
    } else {
        $opacityHeader = "100%";
    }
    ?>
    <div class="header-nav-container" style = "--headerOpacity: <?php echo $opacityHeader; ?>;">
        <div class="header">
        <!--This is what is shown at the top of the page-->
            <div style="text-align: center;">
                    <h1>
                        <strong>
                            Luke Hawinkels
                        </strong>
                    </h1>
            </div>
        </div>
        <?php
            require("nav.php");
        ?>
    </div>
</html>
