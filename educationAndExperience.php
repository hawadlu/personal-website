
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
        <div class="tab">
            <div class="educationAndExperience-grid-container">
              <div>               
                  <button class="tablinks" id="defaultOpen" onclick="showEducationAndExperience(event, 'Education')">Education</button>
              </div>
              <div>
                  <button class="tablinks" onclick="showEducationAndExperience(event, 'Experience')">Experience</button>
              </div>
          </div>
          
          
      </div>

      <div id="Education" class="tabcontent">
          <div class="education-grid-container">
            <div class="education-Institution">
                <center>
                    <h1>
                        Institution
                    </h1>
                </center>
            </div>
            <div class="education-Subject">
                <center>
                        <p><strong>Subject: </strong>xyz</p>
                </center>
            </div>
            <div class="education-Grade">
                <center>
                    <p>
                        Grade: E
                    </p>
                </center>
            </div>
            <div class="education-Credits">
                <center>
                    <p>
                        Credits: 25
                    </p>
                </center>
            </div>
            <div class="education-Year">
                <center>
                    <p>
                        Year: 2019
                    </p>
                </center>
            </div>
        </div>
    </div>

    <div id="Experience" class="tabcontent">
      <div class="experience-grid-container">
  <div class="experience-name"><p>Hi</p></div>
  <div class="experience-year"><p>Hi</p></div>
  <div class="experience-langauges"><p>Hi</p></div>
  <div class="experience-link"><p>Hi</p></div>
  <div class="experience-description"><p>Hi</p></div>
</div>
  </div>

  <script>
    function showEducationAndExperience(evt, cityName) {
      var i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tabcontent");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}
    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();
</script>

</div>



</div>
<!-- Footer -->
<?php
//Pull information from the footer page
require("Footer.php");//'Require is 100% needed for this site to run
?>
</html>
