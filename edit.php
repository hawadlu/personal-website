<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
//Todo add a function that allows the user to play around with their own items without having to log in. Use cookies and store info in the browser or PHP session variables
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

echo "It's lonely here";
?>
<html lang="English">
    <!--Pulls in the head and other required pages-->
    <?php
        require("head.php");
    ?>
    <body class="background-img">
        <div class="page-grid-container">
        <div>
            <p>
                Hello <?php echo $_SESSION['name']; ?>
            </p>

            <!-- Trigger/Open The Modal -->
            <button id="newItemButton">Create new item</button>

            <!-- The Modal -->
            <div id="newItemModal" class="newItemModal">

                <!-- Modal content -->
                <div class="newItemModalContent">
                    <span class="closeNewItemModal">&times;</span>
                    <!--Todo for play around file uploads it is probably best not to upload the file, but to store the file path instead and use that to display the image-->
                    <!--Upload files-->
                    <form action="createItem.php" method="post" enctype="multipart/form-data">
                        Select image to upload:
                        <input type="file" name="fileToUpload" id="fileToUpload">
                        <input type="submit" value="Upload Image" name="submit">
                    </form>
                </div>

            </div>

            <a href="logout.php">Logout</a>
        </div>
    </div>
    </body>
    <script>
        //Code used to control the new item modal
        // Get the modal
        var modal = document.getElementById("newItemModal");

        // Get the button that opens the modal
        var btn = document.getElementById("newItemButton");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("closeNewItemModal")[0];

        // When the user clicks on the button, open the modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
    <!--Called last so that it renders at the top-->
    <?php
        require("header.php");;
        //Pull information from the footer page
        require("footer.php");
    ?>
</html>

?>