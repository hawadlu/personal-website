
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
        <div class="about-grid-container">
            <div class="about-Title">
                <p>
                    About Me
                </p>
            </div>
            <div class="about-Image">
                <p>
                    <img src="Images/Test Image.png" alt="TEST IMAGE">
                </p>
            </div>
            <div class="about-Description">
                <p>
                    One
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
