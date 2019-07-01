
<html>
<!--Pulls in the head and other required pages-->
<?php require("Head.php") ?>
<div class="page-grid-container">       
    <!--The first div of the page grid-->
    <div class="page-grid-container">  
        <div style="background-color: red">
            <?php
            require("Header.php");
            require("Nav.php");
            ?>
        </div>
    </div>

    <!--The second div of the page grid-->
    <div>
        <!--The grid which contains the main content of the page-->
        <div class="grid-container">
            <div class="Title"><p>One</p></div>
            <div class="Image"><p>One</p></div>
            <div class="Description"><p>One</p></div>
            <div class="Other"><p>One</p></div>
        </div>  
    </div>
    
    
    
</div>
<!-- Footer -->
<?php
        //Pull information from the footer page
            require("Footer.php");//'Require is 100% needed for this site to run
            ?>
            </html>
