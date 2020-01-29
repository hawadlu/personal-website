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
require("connect.php");

?>
<body class="background-img">
<div class="page-grid-container">
    <div>
        <p>
            Hello <?php echo $_SESSION['name']; ?>
            <br>
            Be aware that you may have to scroll down to the desired record while editing
        </p>

        <!--The edit tabs-->
        <div class="edit-tabs">
            <div class="Education">
                <!--Make button grey by default-->
                <button id="showEducation" class="indexButton"
                        style="display: block; border: none; border-radius: 0; background-color: #D3D3D3"
                        onclick="showElement('editEducation')">Education
                </button>
            </div>
            <div class="Projects">
                <button id="showProjects" class="indexButton" style="display: block; border: none; border-radius: 0;"
                        onclick="showElement('editProjects')">Projects
                </button>
            </div>
        </div>

        <!--The div that contains the education edit. Shown by default-->
        <div id="editEducation" style="display: block">
            <?php
            //The query which shows the education history
            $educationQuery = ("SELECT `Education`.`uniqueKey`, `Education`.`subject`, `codeExtension`.`codeExtension`, `Education`.`credits`, `Grade`.`grade`, `Institution`.`institution`, `relevantYear`.`relevantYear`, `subjectCode`.`code`, `subjectLevel`.`subjectLevel`
FROM `Education`
LEFT JOIN `codeExtension` ON `Education`.`codeExtensionFK` = `codeExtension`.`codeExtensionPK`
LEFT JOIN `Grade` ON `Education`.`gradeFk` = `Grade`.`gradePK`
LEFT JOIN `Institution` ON `Education`.`institutionFK` = `Institution`.`institutionPK`
LEFT JOIN `relevantYear` ON `Education`.`classYearFK` = `relevantYear`.`relevantYearPK`
LEFT JOIN `subjectCode` ON `Education`.`codeFK` = `subjectCode`.`codePK`
LEFT JOIN `subjectLevel` ON `Education`.`subjectLevelFK` = `subjectLevel`.`subjectLevelPK`
ORDER BY `Education`.`institutionFK` DESC, `relevantYear`.`relevantYear` DESC, `Education`.`credits` DESC, `Grade`.`grade` ASC,`subjectCode`.`code` ASC");

            $educationResult = mysqli_query($con, $educationQuery);
            $institution = "";
            $recordCount = mysqli_num_rows($educationResult);
            $count = 0;

            //Display a message if there are no records returned
            if ($recordCount == 0) {
                ?>
                <div style="text-align: center">
                    <h1>
                        Nothing to see here.
                    </h1>
                </div>
                <?php
            } else {
                while ($EducationOutput = mysqli_fetch_array($educationResult)) {
                    ?>
                    <!-- Update form-->
                    <form method="post">
                        <!--The record id-->
                        <input type="hidden" name="uniqueKey" value="<?php echo $EducationOutput['uniqueKey']; ?>">
                        <?php
                        //Calculates if any rounding of the examples div is required
                        $classHeader = "";
                        $classContent = "";
                        if ($count == 0) {
                            $classHeader = "roundTop";
                        } else {
                            $classHeader = "";
                        }

                        if ($count == $recordCount - 1) {
                            $classContent = "education-grid-container-edit roundBottom";
                        } else {
                            $classContent = "education-grid-container-edit";
                        }

                        //Calculate the appropriate colour
                        if ($count % 2 == 0) {
                            //even
                            $colour = '#F7F7F7';
                        } else {
                            //odd
                            $colour = 'white';
                        }

                        $count += 1;

                        //Checking for a new institution
                        if ($EducationOutput['institution'] != $institution) {
                            //Reset the institution
                            $institution = $EducationOutput['institution'];
                            ?>
                            <div style="background-color: #D3D3D3; text-align: center"
                                 class="<?php echo $classHeader; ?>">
                                <h1>
                                    <?php
                                    echo $EducationOutput['institution'];
                                    ?>
                                </h1>

                                <!--Display the column titles-->
                                <div class="education-Titles-Large-Edit">
                                    <div>
                                        <p class="alignTextLeft">
                                            Update
                                        </p>
                                    </div>
                                    <div>
                                        <p class="alignTextLeft">
                                            Code
                                        </p>
                                    </div>
                                    <div>
                                        <p class="alignTextLeft">
                                            Subject
                                        </p>
                                    </div>
                                    <div>
                                        <p class="alignTextLeft">
                                            <!--Determine weather the results are NCEA or Not-->
                                            <?php
                                            if ($institution == "Tawa College") {
                                                echo "Credits";
                                            } else {
                                                echo "Grade";
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="alignTextLeft">
                                            Subject Level
                                        </p>
                                    </div>
                                    <div>
                                        <p class="alignTextLeft">
                                            Year
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }

                        //Printing the education information
                        ?>
                        <div style="background-color: <?php echo $colour; ?>" class="<?php echo $classContent; ?>">
                            <!--The update button-->
                            <div class="education-Update">
                                <input name="updateRecord<?php echo $EducationOutput['uniqueKey']; ?>" value="Update"
                                       type="submit">
                            </div>

                            <!--Display the title on a small screen-->
                            <div class="codeTitle">
                                <p class="alignTextLeft">
                                    Code:
                                </p>
                            </div>
                            <div class="education-Code-Edit">
                                <div style="text-align: center;">
                                    <p class="alignTextLeft">
                                        <?php
                                        //Add the code extension if possible
                                        $extension = "";
                                        if ($EducationOutput['codeExtension'] != null) {
                                            $extension = $EducationOutput['codeExtension'];
                                        }
                                        echo $EducationOutput['code'] . $extension;
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <!--Display the title on a small screen-->
                            <div class="subjectTitle">
                                <p class="alignTextLeft">
                                    Subject:
                                </p>
                            </div>
                            <div class="education-Subject-Edit">
                                <div style="text-align: center;">
                                    <p class="alignTextLeft">
                                        <?php
                                        echo $EducationOutput['subject'];
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <!--Display the title on a small screen-->
                            <div class="creditsTitle">
                                <p class="alignTextLeft">
                                    <!--Determine weather the results are NCEA or Not-->
                                    <?php
                                    if ($institution == "Tawa College") {
                                        echo "Credits:";
                                    } else {
                                        echo "Grade:";
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="education-Credits-Edit">
                                <div style="text-align: center;">
                                    <p class="alignTextLeft">
                                        <?php
                                        //Checking if NCEA or uni results should be displayed
                                        if ($EducationOutput['grade'] != null) {
                                            echo $EducationOutput['grade'];
                                        } elseif ($EducationOutput['grade'] == null) {
                                            echo $EducationOutput['credits'];
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <!--Display the title on a small screen-->
                            <div class="subjectLevelTitle">
                                <p class="alignTextLeft">
                                    Level:
                                </p>
                            </div>
                            <div class="education-subjectLevel">
                                <div style="text-align: center;">
                                    <p class="alignTextLeft">
                                        <?php
                                        if ($EducationOutput['subjectLevel'] != null) {
                                            echo $EducationOutput['subjectLevel'];
                                        } else {
                                            echo "Not applicable";
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <!--Display the title on a small screen-->
                            <div class="yearTitle">
                                <p class="alignTextLeft">
                                    Year:
                                </p>
                            </div>
                            <div class="education-Year-Edit">
                                <div style="text-align: center;">
                                    <p class="alignTextLeft">
                                        <?php
                                        if ($EducationOutput['relevantYear'] != null) {
                                            echo $EducationOutput['relevantYear'];
                                        } else {
                                            echo "Not applicable";
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php

                    //Run the update script
                    if (isset($_POST['updateRecord' . $EducationOutput['uniqueKey']])) {
                        echo "updating";
                        echo $_POST['updateRecord' . $EducationOutput['uniqueKey']];
                        $key = $_POST['uniqueKey'];
                        echo $key;
                        unset($_POST['updateRecord' . $EducationOutput['uniqueKey']]);
                        unset($_POST['uniqueKey']);

                        ?>
                        <!--This form is used to update the records-->
                        <!--Todo add styling for mobile-->
                        <form method="post">
                            <div class="add-grid-container">
                                <div class="add-Institution">
                                    <input class="textInput" type="text" name="institution"
                                           value="<?php echo $institution; ?>" required>
                                </div>
                                <div class="add-Subject">
                                    <input class="textInput" type="text" name="subject"
                                           value="<?php echo $EducationOutput['subject']; ?>" required>
                                </div>
                                <div class="add-Subject-Year">
                                    <input class="textInput" type="number" name="subject-Year"
                                           value="<?php echo $EducationOutput['relevantYear']; ?>" required>
                                </div>
                                <div class="add-Subject-Level">
                                    <input class="textInput" type="text" name="subject-Level"
                                           value="<?php echo $EducationOutput['subjectLevel']; ?>" required>
                                </div>
                                <div class="add-Code">
                                    <input class="textInput" type="text" name="code"
                                           value="<?php echo $EducationOutput['code']; ?>" required>
                                </div>
                                <div class="add-Code-Extension">
                                    <input class="textInput" type="number" name="code-Extension" value="<?php echo $EducationOutput['codeExtension']; ?> " required>
                                </div>
                                <div class="add-Grade">
                                    <?php
                                    //Display credits or grade
                                    if ($EducationOutput['grade'] != null) {
                                        //University. Display dropdown
                                        ?>
                                        <!--Todo create query to auto populate the dropdown-->
                                        <select required>
                                            <option selected value = "<?php echo $EducationOutput['grade']; ?>">
                                                <?php echo $EducationOutput['grade'];?>
                                            </option>
                                            <option value="volvo">Volvo</option>
                                            <option value="saab">Saab</option>
                                            <option value="mercedes">Mercedes</option>
                                            <option value="audi">Audi</option>
                                        </select>
                                        <?php
                                    } else {
                                        //College. Display text box
                                        ?>
                                        <input class=textInput" type="number" name="credits"
                                               value = "<?php echo $EducationOutput['credits']; ?>">
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="save-Record">
                                    <input class = "indexButton" type = "submit" name = "submit" value = "submit">
                                </div>
                            </div>
                        </form>
                        <?php
                    }
                }
            }
            ?>
        </div>

        <!--The div that contains the projects edit-->
        <div id="editProjects" style="display: none">
            <p>
                Bye
            </p>
        </div>

        <!--Javascript to control what is shown-->
        <script>
            var elements = [];

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


            // Get the modal
            var modal = document.getElementById("myModal");

            // Get the button that opens the modal
            var btn = document.getElementById("myBtn");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks on the button, open the modal
            btn.onclick = function () {
                modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span.onclick = function () {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
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
                                        <?php echo $file_array[$i]["name"] . " " . $phpFileUploadErrors[$file_array[$i]["error"]] ?>
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
                function reArrayFiles($file_post)
                {
                    $file_ary = array();
                    $file_count = count($file_post['name']);
                    $file_keys = array_keys($file_post);

                    for ($i = 0; $i < $file_count; $i++) {
                        foreach ($file_keys as $key) {
                            $file_ary[$i][$key] = $file_post[$key][$i];
                        }
                    }

                    return $file_ary;
                }

                //Same as print_r surrounded with <pre></pre> HTML tags for better array readability
                function pre_r($array)
                {
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
    btn.onclick = function () {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function () {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
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
