<html>
    <head>
        <?php
        require("head.php");
        require("Header.php");
        require("Nav.php");
        ?>
    </head>

    <!--Disable scrolling-->
    <body style = "margin: 0; height: 100%; overflow: hidden;">
            <?php
            //Load BG Images
            foreach (glob("BG Images/*") as $file) {
            ?>
            <div class="bgImage fade">
                        <img src="<?php echo $file; ?>" class="center" style = "border-radius: 0px; width: 100%">
            </div>
                <?php
            }
            ?>


        <script>
            var slideIndex = 0;
            showSlides();

            function showSlides() {
                var i;
                var slides = document.getElementsByClassName("bgImage");
                for (i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";
                }
                slideIndex++;
                if (slideIndex > slides.length) {slideIndex = 1}
                slides[slideIndex-1].style.display = "block";
                setTimeout(showSlides, 5000); // Change image every 2 seconds
            }
        </script>
    </body>


    <?php
    require("Footer.php");
    ?>
</html>