
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
      <div class="experience-examples-grid-container">
          <div class="experience-examples-name">
            <center>
                <h1>
                    Experience Name
                </h1>
            </center>
        </div>
        <div class="experience-examples-year">
            <center>
                <p>
                    Year: 2019
                </p>
            </center>
        </div>
        <div class="experience-examples-langauges">
            <center>
                <p>
                    language(s): HTML, CSS, JavaScript
                </p>
            </center>
        </div>
        <div class="experience-examples-link">
            <center>
                <p>
                    link: abcdef
                </p>
            </center>
        </div>
        <div class="experience-examples-description">
            <center>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce ac elementum tellus. Pellentesque vehicula metus eu posuere vulputate. Aliquam erat volutpat. Sed metus nibh, malesuada consectetur dolor a, commodo convallis sapien. Vestibulum ex magna, laoreet vitae nibh feugiat, sodales vestibulum eros. Nullam gravida ultricies magna, quis fringilla est posuere nec. Integer congue cursus eros, quis maximus orci. In hac habitasse platea dictumst. Nam tincidunt, nisl eget eleifend posuere, nisi ex gravida ex, vitae blandit felis erat a urna. Sed id quam odio. Sed suscipit id nunc ut porttitor. Suspendisse feugiat eget erat sit amet dapibus. Donec auctor velit vitae quam lobortis dignissim.
                </p>
            </center>
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
