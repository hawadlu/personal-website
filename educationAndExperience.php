
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
          <button class="tablinks" onclick="showEducationAndExperience(event, 'Education')">Education</button>
          <button class="tablinks" onclick="showEducationAndExperience(event, 'Experience')">Experience</button>
      </div>

      <div id="Education" class="tabcontent">
          <h3>Education</h3>
          <p>London is the capital city of England.</p>
      </div>

      <div id="Experience" class="tabcontent">
          <h3>Experience</h3>
          <p>Paris is the capital of France.</p> 
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
</script>

</div>



</div>
<!-- Footer -->
<?php
//Pull information from the footer page
require("Footer.php");//'Require is 100% needed for this site to run
?>
</html>
