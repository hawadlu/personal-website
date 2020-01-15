<html>
<head>
    <?php
    require("Head.php");
    require("Header.php");
    require("Nav.php");
    ?>
</head>

<!--Disable scrolling-->
<body style="overflow: hidden" onload="onload()">
    <div id="bgImage" style = "height: 100vh">
        <!--Calculating the image size-->
        <script>
            function onload() {
                //The size of the users device
                let windowWidth = window.innerWidth;
                let windowHeight = document.querySelector("bgImage").offsetHeight;

                //The size of the image to be loaded
                var img = new Image();
                img.src = "Images/BG Image.jpg"
                var imgWidth = img.width;
                var imgHeight = img.height;

                var scaleFactor = 1;

                //Calculating the degree to which the image should be scaled in terms of percentage
                scaleFactor = windowHeight / imgHeight;
                scaleFactor = imgHeight * scaleFactor;
                scaleFactor = +scaleFactor.toFixed(2);

                //Change the CSS property
                var bgImage = document.querySelector('img');
                bgImage.style.setProperty('--imgHeight', scaleFactor + 'px');

                //document.getElementById("calcImgSize").innerHTML = ("Window height: " + windowHeight + " img height: " + imgHeight + " img width: " + imgWidt + " scale factor: " + scaleFactor);
                document.getElementById("calcImgSize").innerHTML = scaleFactor;
            };
        </script>

        <!--    <div id = "calcImgSize"></div>-->
        <div id = "calcImgSize"></div>
        <img id="calcImgSize" src="Images/BG Image.jpg">
    </div>
</body>


<?php
require("Footer.php");
?>
</html>