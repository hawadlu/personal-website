<html lang="English">
<!--Allows the nav to scale for mobile devices-->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles.css">

    <div class="topnav" id="myTopnav">
        <a href="index.php" class="active">Home</a>
        <a href="about.php">About Me</a>
        <a href="examples.php">Examples</a>
        <a href="education.php">Education</a>
        <a href="edit.php">Play Around</a>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <img src="images/hamburger%20icon.png">
        </a>
    </div>

    <script>
        function myFunction() {
            var x = document.getElementById("myTopnav");
            if (x.className === "topnav") {
                x.className += " responsive";
            } else {
                x.className = "topnav";
            }
        }
    </script>
</html>

