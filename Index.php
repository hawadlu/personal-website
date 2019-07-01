
<html>
        <!--Pulls in the head and other required pages-->
	<?php require("Head.php") ?>       
	<body>
        <header>
                <?php
                        require("Header.php");
                        
                ?>
        </header>
        <?php
                        require("Nav.php")
                ?>
    <body></body>       
	</body>
        <footer>
                <!-- Footer -->
                <?php
                        //Pull information from the footer page
                        require("Footer.php")//'Require is 100% needed for this site to run
                ?>
        </footer>
</html>
