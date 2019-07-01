
<html>
<!--Pulls in the head and other required pages-->
<?php require("Head.php") ?>       
<body>
    <header>
        
        <div id="header-nav-container">
            <?php
            require("Header.php");
            require("Nav.php");
            ?>
        </div>
        
    </header>
    
    <body>      
    </body>
    <footer>
        <!-- Footer -->
        <?php
            //Pull information from the footer page
            require("Footer.php");//'Require is 100% needed for this site to run
            ?>
        </footer>
        </html>
