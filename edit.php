<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
//Todo add a function that allows the user to play around with their own items without having to log in. Use cookies and store info in the browser or PHP session variables
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

//Todo only run this if logged in
//Run several php queries to get arrays of each field
$institutionArray = getArray("SELECT institution.institution FROM institution WHERE institution != ''", $con);
$subjectArray = getArray("SELECT DISTINCT subject FROM education", $con);
$yearArray = getArray("SELECT year.year FROM year WHERE year != ''", $con);
$subjectLevelArray = getArray("SELECT subjectLevel.subjectLevel FROM subjectLevel WHERE subjectLevel != ''", $con);
$codeArray = getArray("SELECT subjectCode.subjectCode FROM subjectCode WHERE subjectCode != ''", $con);
$extensionArray = getArray("SELECT codeExtension.codeExtension FROM codeExtension WHERE codeExtension != ''", $con);
$gradeArray = getArray("SELECT grade.grade FROM grade WHERE grade != ''", $con);

//Check to see if an error message has been set
$errorMessage = null;
if (isset($_COOKIE['errorMsg'])) {
    //Only output if the value is not a number
    if (!is_numeric($_COOKIE['errorMsg'])) {
        $errorMessage = $_COOKIE['errorMsg'];
    }

    //Delete the cookie
    setcookie('errorMsg', time() - 3600);
}


function getArray($query, $con)
{
    $value = null;
    $query = $con->prepare($query);
    $query->execute();
    $query->bind_result($value);
    $query->store_result();

    $array = [];

    while ($row = $query->fetch()) {
        array_push($array, $value);
    }
    return $array;
}

?>
<body class="background-img">
<div class="page-grid-container">
    <div>
        <p>
            Hello <?php echo $_SESSION['name']; ?>
            <br>
            Be aware that you may have to scroll down to the desired record while editing
        </p>

        <!--Button for adding new items-->
        <!-- Trigger/Open The Modal -->
        <button id="newProjectButton">Create New Item</button>

        <!-- The new project Modal -->
        <div id="newProjectModal" class="newItemModal">

            <!-- Modal content -->
            <div class="newItemModalContent">
                <span class="closeNewProjectModal">&times;</span>
                <!--Load tabs based on the record being entered-->
                <div class="edit-tabs">
                    <div class="Education">
                        <!--Make button grey by default-->
                        <button id="addEducationTab" class="indexButton"
                                style="display: block; border: none; border-radius: 0; background-color: #D3D3D3"
                                onclick="showElement('addEducation')">Education
                        </button>
                    </div>
                    <div class="Projects">
                        <button id="addProjectTab" class="indexButton"
                                style="display: block; border: none; border-radius: 0;"
                                onclick="showElement('addProject')">Projects
                        </button>
                    </div>
                </div>

                <div id="addEducation" style="display: block">
                    <p>
                        Add education
                    </p>
                    <form autocomplete="off" method="post" action="process.php">
                        <div class="autocomplete">
                            <input id="newRecordInstitution" class="textInput" type="text" name="newInstitution" placeholder="Institution: e.g. Tawa College" required>
                        </div>
                        <div class="autocomplete">
                            <input id="newRecordSubject" class="textInput" type="text" name="newSubject"
                                   placeholder="Subject: e.g. Science" " required>
                        </div>
                        <div class="autocomplete">
                            <input id="newRecordYear" class="textInput" type="number" name="newSubjectYear"
                                   placeholder="Year: e.g. <?php echo date('Y');?>" required>
                        </div>
                        <div class="autocomplete">
                            <input id="newRecordSubjectLevel" class="textInput" type="text" name="newSubjectLevel"
                                   placeholder="Level: e.g. NCEA Level One" required>
                        </div>
                        <div class="autocomplete">
                            <input id="newRecordCode" class="textInput" type="text" name="newCode"
                                   placeholder="Code: e.g. COMP" required>
                        </div>
                        <div class="autocomplete">
                            <input id="newRecordCodeExtension" class="textInput" type="text" name="newCodeExtension"
                                   placeholder="Code Extension: e.g. 101" required>
                        </div>

                        <!--Change the input type based on the type of grade the user wishes to enter-->
                        <select onchange="showElement(this.value)">
                            <option value = "showNceaInput">
                                NCEA (number of credits)
                            </option>
                            <option value="showGpaInput">
                                GPA (A+, B+, etc)
                            </option>
                        </select>

                        <!--todo make it so that the user cannot enter both grades and credits-->
                        <div id = "newNceaInput" style="display: block">
                            <p>
                                NCEA
                            </p>
                            <input id = "newRecordCredits" class="textInput" type = "text" name = "newCredits" placeholder="Credits: e.g. 22">
                        </div>
                        <div id = "newGpaInput" style="display: none">
                            <p>
                                GPA
                            </p>
                            <div class="autocomplete">
                                <input id = "newRecordGrade" class="textInput" type = "text" name = "newGrade" placeholder="Grade: e.g. A-">
                            </div>
                        </div>


                            <input name="newEducationRecord" value="Submit Record" type="submit">
                    </form>

                </div>
                <div id="addProject" style="display: none">
                    <p>
                        Add project
                    </p>
                    <!--Todo for play around file uploads it is probably best not to upload the file, but to store the file path instead and use that to display the image. when using cookies-->
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

        </div>

        <!--The edit tabs-->
        <div class="edit-tabs">
            <div class="Education">
                <!--Make button grey by default-->
                <button id="educationTab" class="indexButton"
                        style="display: block; border: none; border-radius: 0; background-color: #D3D3D3"
                        onclick="showElement('editEducation')">Education
                </button>
            </div>
            <div class="Projects">
                <button id="projectTab" class="indexButton" style="display: block; border: none; border-radius: 0;"
                        onclick="showElement('editProjects')">Projects
                </button>
            </div>
        </div>

        <!--The div that contains the education edit. Shown by default-->
        <div id="editEducation" style="display: block">

            <?php
            //Perform the query to get the grades. Done here so that it is not repeated every time
            $dropdownGradeQuery = $con->prepare("SELECT grade.grade FROM grade ");
            $dropdownGradeQuery->execute();
            $dropdownGradeQuery->bind_result($dropdownGrade);
            $dropdownGradeQuery->store_result();

            //The query which shows the education history
            $educationQuery = $con->prepare("SELECT education.uniqueKey, education.subject, codeExtension.codeExtension, credits.credits, grade.grade, institution.institution, 
            year.year, subjectCode.subjectCode, subjectLevel.subjectLevel
            FROM education 
            LEFT JOIN credits ON education.creditsFK = credits.creditsPK 
            LEFT JOIN codeExtension ON education.codeExtensionFK = codeExtension.codeExtensionPK 
            LEFT JOIN grade ON education.gradeFk = grade.gradePK 
            LEFT JOIN institution ON education.institutionFK = institution.institutionPK 
            LEFT JOIN year ON education.yearFK = year.yearPK 
            LEFT JOIN subjectCode ON education.subjectCodeFK = subjectCode.subjectCodePK
            LEFT JOIN subjectLevel ON education.subjectLevelFK = subjectLevel.subjectLevelPK 
            ORDER BY education.institutionFK DESC, year.year DESC, credits.credits DESC, grade.grade ASC,subjectCode.subjectCode ASC");
            $educationQuery->execute();
            $educationQuery->bind_result($uniqueKey, $subject, $codeExtension, $credits, $grade, $institution, $relevantYear, $code, $subjectLevel);
            $educationQuery->store_result();
            $recordCount = $educationQuery->num_rows();
            $currentInstitution = "";

            $count = 0;
            $isNumeric = "true"; //Used to tell if a grade is parley numeric when updating the records

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
                while ($row = $educationQuery->fetch()) {
                    ?>
                    <!-- Update form-->
                    <form method="post">
                        <!--The record id-->
                        <input type="hidden" name="uniqueKey" value="<?php echo $uniqueKey; ?>">
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
                        if ($institution != $currentInstitution) {
                            //Reset the institution
                            $currentInstitution = $institution;
                            ?>
                            <div style="background-color: #D3D3D3; text-align: center"
                                 class="<?php echo $classHeader; ?>">
                                <h1>
                                    <?php
                                    echo $institution;
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
                                            if ($grade == null) {
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
                                <input name="updateRecord<?php echo $uniqueKey; ?>" value="Update"
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
                                        if ($codeExtension != null) {
                                            $extension = $codeExtension;
                                        }
                                        echo $code . $extension;
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
                                        echo $subject;
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <!--Display the title on a small screen-->
                            <div class="creditsTitle">
                                <p class="alignTextLeft">
                                    <!--Determine weather the results are NCEA or Not-->
                                    <?php
                                    if ($grade == null) {
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
                                        if ($grade != null) {
                                            echo $grade;
                                        } else {
                                            echo $credits;
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
                                        if ($subjectLevel != null) {
                                            echo $subjectLevel;
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
                                        if ($relevantYear != null) {
                                            echo $relevantYear;
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

                    //Run the update record script
                    if (isset($_POST['updateRecord' . $uniqueKey])) {
                        echo "updating";
                        echo $_POST['updateRecord' . $uniqueKey];
                        $key = $_POST['uniqueKey'];
                        echo $key;
                        unset($_POST['updateRecord' . $uniqueKey]);
                        unset($_POST['uniqueKey']);

                        ?>
                        <!--This form is used to update the records-->
                        <!--Todo add styling for mobile-->
                        <!--todo make it so that the user cannot enter both grades and credits-->

                        <form autocomplete="off" method="post" action="process.php">
                            <div class="add-grid-container">
                                <div class="add-Institution autocomplete">
                                    <input id="institution" class="textInput" type="text"
                                           name="institution" value="<?php echo $institution; ?>" required>
                                </div>
                                <div class="add-Subject autocomplete">
                                    <input id="subject" class="textInput" type="text" name="subject"
                                           value="<?php echo $subject; ?>" required>
                                </div>
                                <div class="add-Subject-Year autocomplete">
                                    <input id="year" class="textInput" type="number" name="subjectYear"
                                           value="<?php echo $relevantYear; ?>" required>
                                </div>
                                <div class="add-Subject-Level autocomplete">
                                    <input id="subjectLevel" class="textInput" type="text" name="subjectLevel"
                                           value="<?php echo $subjectLevel; ?>" required>
                                </div>
                                <div class="add-Code autocomplete">
                                    <input id="code" class="textInput" type="text" name="code"
                                           value="<?php echo $code; ?>" required>
                                </div>
                                <div class="add-Code-Extension autocomplete">
                                    <input id="extension" class="textInput" type="text" name="codeExtension"
                                           value="<?php echo $codeExtension; ?> " required>
                                </div>
                                <div class="add-Grade">
                                    <!--Allow the user to select the type of grade. Set to the current grade type by default-->
                                    <select onchange="showElement(this.value)">
                                        <?php
                                            //Set the default order
                                            if ($credits != 0) {
                                                $optionOne = "showCredits";
                                                $optionTwo = "showGpa";
                                                $displayValOne = "Credits";
                                                $displayValTwo = "Gpa";
                                                $displayDivCredits = "block";
                                                $displayDivGpa = "none";
                                                $requiredOne = "required";
                                                $requiredTwo = "";
                                            } else {
                                                $optionOne = "showGpa";
                                                $optionTwo = "showCredits";
                                                $displayValOne = "Gpa";
                                                $displayValTwo = "Credits";
                                                $displayDivCredits = "none";
                                                $displayDivGpa = "block";
                                                $requiredOne = "";
                                                $requiredTwo = "required";
                                            }
                                        ?>
                                        <option onclick="alert('Clicked')" value="<?php echo $optionOne;?>">
                                            <?php echo $displayValOne;
                                            //Update the type of grade being submitted
                                            ?>
                                        </option>
                                        <option value="<?php echo $optionTwo;?>">
                                            <?php
                                            //Update the type of grade being submitted
                                            echo $displayValTwo;
                                            ?>
                                        </option>
                                    </select>

                                    <div id = "showCreditsDiv" style="display: <?php echo $displayDivCredits;?>">
                                        <p>
                                            Credits
                                        </p>
                                        <input id = "creditsInput" class=textInput" type="number" name="credits"
                                               placeholder="<?php echo $credits; ?>" <?php echo $requiredOne;?>>
                                        <?php
                                        $isNumeric = false;
                                        ?>
                                    </div>
                                    <div id = "showGpaDiv" style="display: <?php echo $displayDivGpa;?>">
                                        <p>
                                            GPA
                                        </p>
                                        <div class="autocomplete">
                                            <input id="gradeInput" class=textInput" type="text" name="grade"
                                                   placeholder="<?php echo $grade; ?>" <?php echo $requiredTwo;?>>
                                        </div>
                                    </div>

                                </div>
                                <div class="save-Record">
                                    <!--Tell process.php which type of query to execute-->
                                    <input name="uniqueKey" value="<?php echo $uniqueKey; ?>" type="hidden">
                                    <input name="submitEducationUpdate<?php $uniqueKey; ?>" value="Update" type="submit">
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
                Projects
            </p>
        </div>

        <a href="logout.php">Logout</a>
    </div>
</div>
</body>
<script>
    //Code used to control the new item modal
    // Get the modal
    var itemModal = document.getElementById("newProjectModal");

    // Get the button that opens the modal
    var itemButton = document.getElementById("newProjectButton");

    // Get the <span> element that closes the modal
    var itemSpan = document.getElementsByClassName("closeNewProjectModal")[0];

    // When the user clicks on the button, open the modal
    itemButton.onclick = function () {
        itemModal.style.display = "block";

        //Load the autocomplete for new records
        loadNewRecordAutocomplete();
    }

    // When the user clicks on <span> (x), close the modal
    itemSpan.onclick = function () {
        itemModal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == itemModal) {
            itemModal.style.display = "none";
        }
    }


    function showElement(show) {
        //alert(show);
        //Show the relevant element
        if (show === "addEducation") {
            document.getElementById("addProjectTab").style.backgroundColor = null;
            document.getElementById("addEducationTab").style.backgroundColor = "#D3D3D3";
            hideElement('addProject');
        } else if (show === 'addProject') {
            document.getElementById("addEducationTab").style.backgroundColor = null;
            document.getElementById("addProjectTab").style.backgroundColor = "#D3D3D3";
            hideElement('addEducation');
        } else if (show === "editEducation") {
            document.getElementById("projectTab").style.backgroundColor = null;
            document.getElementById("educationTab").style.backgroundColor = "#D3D3D3";
            hideElement('editProjects');
        } else if (show === 'editProjects') {
            document.getElementById("educationTab").style.backgroundColor = null;
            document.getElementById("projectTab").style.backgroundColor = "#D3D3D3";
            hideElement('editEducation');
        } else if (show == "showNceaInput") {
            document.getElementById("newNceaInput").style.display = "block";
            hideElement("newGpaInput");
        } else if (show == "showGpaInput") {
            document.getElementById("newGpaInput").style.display = "block";
            hideElement("newNceaInput");
        } else if (show == "showGpa") {
            document.getElementById("showGpaDiv").style.display = "block";
            //Set the the required attribute
            document.getElementById("gradeInput").required = true;
            document.getElementById("creditsInput").required = false;
            hideElement("showCreditsDiv");
        } else if (show == "showCredits") {
            document.getElementById("showCreditsDiv").style.display = "block";
            document.getElementById("creditsInput").required = true;
            document.getElementById("gradeInput").required = false;
            hideElement("showGpaDiv");
        }

        document.getElementById(show).style.display = "block";
    }

    //Hide the relevant element
    function hideElement(id) {
        document.getElementById(id).style.display = "none";
    }


    //Autocomplete code
    function autocomplete(inp, arr) {
        /*the autocomplete function takes two arguments,
        the text field element and an array of possible autocompleted values:*/
        var currentFocus;
        /*execute a function when someone writes in the text field:*/
        inp.addEventListener("input", function (e) {
            var a, b, i, val = this.value;
            /*close any already open lists of autocompleted values*/
            closeAllLists();
            if (!val) {
                return false;
            }
            currentFocus = -1;
            /*create a DIV element that will contain the items (values):*/
            a = document.createElement("DIV");
            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            /*append the DIV element as a child of the autocomplete container:*/
            this.parentNode.appendChild(a);

            /*for each item in the array...*/
            for (i = 0; i < arr.length; i++) {
                /*check if the item starts with the same letters as the text field value:*/
                if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                    /*create a DIV element for each matching element:*/
                    b = document.createElement("DIV");
                    /*make the matching letters bold:*/
                    b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                    b.innerHTML += arr[i].substr(val.length);
                    /*insert a input field that will hold the current array item's value:*/
                    b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                    /*execute a function when someone clicks on the item value (DIV element):*/
                    b.addEventListener("click", function (e) {
                        /*insert the value for the autocomplete text field:*/
                        inp.value = this.getElementsByTagName("input")[0].value;
                        /*close the list of autocompleted values,
                        (or any other open lists of autocompleted values:*/
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            }
        });
        /*execute a function presses a key on the keyboard:*/
        inp.addEventListener("keydown", function (e) {
            var x = document.getElementById(this.id + "autocomplete-list");
            if (x) x = x.getElementsByTagName("div");
            if (e.keyCode == 40) {
                /*If the arrow DOWN key is pressed,
                increase the currentFocus variable:*/
                currentFocus++;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.keyCode == 38) { //up
                /*If the arrow UP key is pressed,
                decrease the currentFocus variable:*/
                currentFocus--;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.keyCode == 13) {
                /*If the ENTER key is pressed, prevent the form from being submitted,*/
                e.preventDefault();
                if (currentFocus > -1) {
                    /*and simulate a click on the "active" item:*/
                    if (x) x[currentFocus].click();
                }
            }
        });

        function addActive(x) {
            /*a function to classify an item as "active":*/
            if (!x) return false;
            /*start by removing the "active" class on all items:*/
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            /*add class "autocomplete-active":*/
            x[currentFocus].classList.add("autocomplete-active");
        }

        function removeActive(x) {
            /*a function to remove the "active" class from all autocomplete items:*/
            for (var i = 0; i < x.length; i++) {
                x[i].classList.remove("autocomplete-active");
            }
        }

        function closeAllLists(elmnt) {
            /*close all autocomplete lists in the document,
            except the one passed as an argument:*/
            var x = document.getElementsByClassName("autocomplete-items");
            for (var i = 0; i < x.length; i++) {
                if (elmnt != x[i] && elmnt != inp) {
                    x[i].parentNode.removeChild(x[i]);
                }
            }
        }

        /*execute a function when someone clicks in the document:*/
        document.addEventListener("click", function (e) {
            closeAllLists(e.target);
        });
    }

    //Arrays containing relevant autocomplete material
    var institutions = <?php echo json_encode($institutionArray);?>;
    var subjects = <?php echo json_encode($subjectArray);?>;
    var years = convertArrayToString(<?php echo json_encode($yearArray);?>);
    var subjectLevels = <?php echo json_encode($subjectLevelArray);?>;
    var subjectCodes = <?php echo json_encode($codeArray);?>;
    var codeExtensions = convertArrayToString(<?php echo json_encode($extensionArray);?>);
    var grades = <?php echo json_encode($gradeArray);?>;


    /*initiate the autocomplete function on the "each" element, and pass along the countries array as possible autocomplete values:*/
    //Autocomplete updating records
    autocomplete(document.getElementById("institution"), institutions);
    autocomplete(document.getElementById("subject"), subjects);
    autocomplete(document.getElementById("year"), years);
    autocomplete(document.getElementById("subjectLevel"), subjectLevels);
    autocomplete(document.getElementById("code"), subjectCodes);
    autocomplete(document.getElementById("extension"), codeExtensions);
    autocomplete(document.getElementById("gradeInput"), grades);

    //Load the autocompletes for new items
    function loadNewRecordAutocomplete() {
        //Autocomplete for new records
        autocomplete(document.getElementById("newRecordInstitution"), institutions);
        autocomplete(document.getElementById("newRecordSubject"), subjects);
        autocomplete(document.getElementById("newRecordYear"), years);
        autocomplete(document.getElementById("newRecordSubjectLevel"), subjectLevels);
        autocomplete(document.getElementById("newRecordCode"), subjectCodes);
        autocomplete(document.getElementById("newRecordCodeExtension"), codeExtensions);
        autocomplete(document.getElementById("newRecordGrade"), grades);
    }

    //Convert array to string
    function convertArrayToString(array) {
        for (var i = 0; i < array.length; i++) {
            array[i] = String(array[i]);
        }
        return array;
    }


</script>
<!--Called last so that it renders at the top-->
<?php
require("header.php");;
//Pull information from the footer page
require("footer.php");

//Show any error messages if required
if ($errorMessage != null) {
    ?>
    <div class="alert alert-danger" style="position: absolute">
        <strong>Operation failed!</strong> <?php echo $errorMessage;?>
    </div>
    <?
}
?>
</html>
