
<html>
<!--Pulls in the head and other required pages-->
<?php require("head.php") ?>  
<div class="page-grid-container">     
    <!--The first div of the page grid-->
    <div>
        <?php
        require("header.php");
        require("nav.php");
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
                <div style="text-align: center;">
                    <img src="Images/Profile Pic.png" alt="Me">
                </div>
            </div>
            <div class="aboutMe-Description">
                <p>
                    I have chosen to go down the software development route because I feel that the teamwork environment that the industry provides is a natural fit for my personality. I enjoy working as part of a team (especially sports and development teams). I find coding a program with constraints to be an excellent learning opportunity and an enjoyable challenge.
                    <br><br>
                    I have had five years of experience coding at a college level. This has included learning languages such as Python, PHP, SQL, CSS and HTML.
                    <br><br>
                    I am currently at University and practicing the languages C++ and Java.
                    <br><br>
                    I can bring an element of leadership and teamwork as well a hard working, motivated personality to any role. While in the New Zealand Cadet Corp I was a Flight Seargent which meant that I had to deliver 45 minute lessons to teenagers on a consistent basis. This involved planning the lessons while working around the tasks that I have from University and a part time job as a waiter. This has taught me valuable leadership, communication and time management skills which I think would be a great fit for any company.
                </p>
            </div>
        </div>  
    </div>



</div>
<!-- Footer -->
<?php
        //Pull information from the footer page
            require("footer.php");//'Require is 100% needed for this site to run
            ?>
            </html>
