<html>
<!--Pulls in the head and other required pages-->
<?php
require("Head.php");
require("Connect.php");
?>
<body class="background-img">
<div class="page-grid-container">
    <div>
        <!--The grid which contains the main content of the page-->
        <div style="text-align: center;">
            <h1>
                About Me
            </h1>
        </div>
        <p style="padding: 10px">
            <img class="aboutMeImg" src="Images/Profile Pic.png" alt="Me">

            I have chosen to go down the software development route because I feel that the teamwork environment that the industry provides is a natural fit for my personality. I enjoy working as part of a team (especially sports and development teams).
            <br><br>
            I find coding a program with constraints to be an excellent learning opportunity and an enjoyable challenge. I have been programming for five-plus years. This has included learning languages such as python, PHP, SQL, CSS and HTML.
            <br><br>
            I am currently at University and practising the languages C++, Python and Java. I can bring an element of leadership and teamwork
            as well a hard-working, motivated personality to any role.
            <br><br>
            While in the New Zealand Cadet Corp I was a Flight Sargent which meant that I regularly had to deliver forty-five-minute lessons to thirteen and fourteen-year-olds. This involved planning the lessons while working around the tasks that I have from University and a part-time job as a waiter. This has taught me valuable leadership, communication and time management skills which I think would be a great fit for any company.

        </p>
    </div>
</div>
</body>
<!--Called last so that it renders at the top-->
<?php
    require("Header.php");;
?>
<!-- Footer -->
<?php
//Pull information from the footer page
require("Footer.php");//'Require is 100% needed for this site to run
?>
</html>
