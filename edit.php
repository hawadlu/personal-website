<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
//Todo add a function that allows the user to play around with their own items without having to log in. Use cookies and store info in the browser or PHP session variables
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}


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

            <!--The edit tabs-->
            <div class="edit-tabs">
                <div class="Education">
                    <!--Make button grey by default-->
                        <button id = "showEducation" class="indexButton" style="display: block; border: none; border-radius: 0; background-color: #D3D3D3" onclick="showElement('editEducation')">Education</button>
                </div>
                <div class="Projects">
                    <button id = "showProjects" class="indexButton" style="display: block; border: none; border-radius: 0;" onclick="showElement('editProjects')">Projects</button>
                </div>
            </div>

            <!--The div that contains the education edit. Shown by default-->
            <div id = "editEducation" style="display: block">
                <p>
                    Hello
                </p>
            </div>

            <!--The div that contains the projects edit-->
            <div id = "editProjects" style="display: none">
                <p>
                    Bye
                </p>
            </div>

            <!--Javascript to control what is shown-->
            <script>
                function showElement(id) {
                    //Hide the relevant elements
                    if (id === "editEducation") {
                        document.getElementById("showProjects").style.backgroundColor = null;
                        document.getElementById("showEducation").style.backgroundColor = "#D3D3D3";
                        hideElement('editProjects');
                    } else if (id === 'editProjects') {
                        hideElement('editEducation');
                        document.getElementById("showEducation").style.backgroundColor = null;
                        document.getElementById("showProjects").style.backgroundColor = "#D3D3D3";
                    }
                    document.getElementById(id).style.display = "block";
                }

                function hideElement(id) {
                    document.getElementById(id).style.display = "none";
                }
            </script>

            <!-- Trigger/Open The Modal -->
            <button id="newItemButton">Create new item</button>

            <!-- The Modal -->
            <div id="newItemModal" class="newItemModal">

                <!-- Modal content -->
                <div class="newItemModalContent">
                    <span class="closeNewItemModal">&times;</span>
                    <!--Todo for play around file uploads it is probably best not to upload the file, but to store the file path instead and use that to display the image-->
                    <!--Upload files. Allow up to five-->
                    <form action="" method="post" enctype="multipart/form-data">
                        Select image to upload:
                        <input type="file" name="userFiles[]" id="" multiple="">
                        <input type="submit" value="Upload" name="submit">
                    </form>

                    <!--Uploading the files-->
                    <?php
                    //Check the upload files form has been submitted
                    if (isset($_FILES['userFiles'])) {
                        //useful functions and variables. Credit to "Clever Techie. https://www.youtube.com/watch?v=KXyMpRp4d2Q"
                        //Array of possible file upload errors
                        $phpFileUploadErrors = array(
                            0 => "The file uploaded successfully",
                            1 => "The file exceeds the maximum file size defined in php.ini",
                            2 => "The file exceeds the maximum file size defines in the HTML form",
                            3 => "The uploaded file was only partially uploaded",
                            4 => "No file was uploaded",
                            6 => "Missing a temporary folder",
                            7 => "Filed to write file to the disc",
                            8 => "A php extension stopped the file from uploading"
                        );

                        $file_array = reArrayFiles($_FILES['userFiles']);
                        //pre_r($file_array);

                        for ($i = 0; $i < count($file_array); $i++) {
                            //Check for errors
                            if ($file_array[$i]['error']) {
                                ?>
                                <div class="alert alert-danger">
                                    <?php echo $file_array[$i]['name'] . " " . $phpFileUploadErrors[$file_array[$i]['error']]; ?>
                                </div>
                                <?php

                                //Check for extensions errors
                            } else {
                                //Allowable file types
                                $extensions = array("jpg", "png", "gif", "jpeg");
                                $file_ext = explode(".", $file_array[$i]["name"]);
                                $file_ext = end($file_ext);

                                //Check if the extension is acceptable
                                if (!in_array($file_ext, $extensions)) {
                                    ?>
                                    <div class="alert alert-danger">
                                        <?php echo $file_array[$i]["name"] . " Invalid file extension!"; ?>
                                    </div>
                                    <?php
                                } else {
                                    //File uploaded successfully
                                    //Check if the file already exists in the directory
                                    if (!file_exists("images/" . $file_array[$i]["name"])) {
                                        //Move the file from the temporary directory to the intended directory
                                        move_uploaded_file($file_array[$i]["tmp_name"], "images/" . $file_array[$i]["name"]);

                                        //Print a success message
                                        ?>
                                        <div class="alert alert-success">
                                            <?php echo $file_array[$i]["name"] . " " . $phpFileUploadErrors[$file_array[$i]["error"]]?>
                                        </div>
                                        <?php
                                    } else {
                                        //Print message stating that the file already exists
                                        ?>
                                        <div class="alert alert-danger">
                                            <?php echo $file_array[$i]["name"] . " already exists"; ?>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                        }
                    }

                    //Converts $_FILES to a cleaner array when uploading multiple files
                    function reArrayFiles($file_post) {
                        $file_ary = array();
                        $file_count = count($file_post['name']);
                        $file_keys = array_keys($file_post);

                        for ($i = 0; $i < $file_count; $i++) {
                            foreach ($file_keys as $key) {
                                $file_ary[$i][$key] = $file_post[$key][$i];
                            }
                        }

                        return$file_ary;
                    }

                    //Same as print_r surrounded with <pre></pre> HTML tags for better array readability
                    function pre_r($array) {
                        echo '<pre>';
                        print_r($array);
                        echo '</pre>';
                    }


                    ?>
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
