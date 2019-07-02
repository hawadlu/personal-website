
<html>
<!--Pulls in the head and other required pages-->
<?php require("Head.php") ?>  
<div class="page-grid-container">     
    <!--The first div of the page grid-->
    <div>
        <?php
        require("Header.php");
        require("Nav.php");
        ?>
    </div>

    <!--The second div of the page grid-->
    <div>
        <!--The grid which contains the main content of the page-->
        <div class="aboutMe-grid-container">
            <div class="aboutMe-Title">
                <center>
                    <h1>
                        About Me
                    </h1>
                </center>
            </div>
            <div class="aboutMe-Image">
                <center>
                    <p>
                        <img src="Images/Test Image.png" alt="TEST IMAGE">
                    </p>
                </center>
            </div>
            <div class="aboutMe-Description">
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sed sagittis sem. Fusce iaculis leo at lacus fermentum vulputate. Integer scelerisque feugiat dui eu maximus. Suspendisse potenti. Integer arcu ligula, gravida ut purus nec, mollis dictum est. Fusce cursus mauris at felis fringilla ultricies. Sed vestibulum mollis congue. Mauris ut scelerisque magna. Curabitur euismod magna at sapien facilisis ultrices. Vestibulum mollis diam feugiat, dignissim sapien ac, vestibulum tellus. Donec gravida bibendum consectetur. Quisque vel erat ac turpis volutpat scelerisque. Nunc aliquet nunc vitae porta rutrum. Donec aliquet suscipit nulla et feugiat. Morbi sed urna sed nunc vulputate porttitor.
                    <br>
                    <br>
                    Sed laoreet quam in elit dapibus, porttitor feugiat eros ultricies. Nulla sit amet tincidunt nunc. Suspendisse ut mi orci. Aenean sit amet faucibus dui. Donec at varius nisl. Pellentesque lacus mauris, imperdiet eu bibendum interdum, gravida ac ex. Sed lobortis eleifend eros a tincidunt.
                </p>
            </div>
        </div>  
    </div>



</div>
<!-- Footer -->
<?php
        //Pull information from the footer page
            require("Footer.php");//'Require is 100% needed for this site to run
            ?>
            </html>
