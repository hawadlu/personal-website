<?php
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
    
        //Todo add default records that the user can use when using cookies

        //Todo only run this if logged in
        //Run several php queries to get arrays of each field of the education
        $institutionArray = getArray("SELECT institution.institution FROM institution WHERE institution != ''", $con);
        $subjectArray = getArray("SELECT DISTINCT subject FROM education", $con);
        $yearArray = getArray("SELECT year.year FROM year WHERE year != ''", $con);
        $subjectLevelArray = getArray("SELECT subjectLevel.subjectLevel FROM subjectLevel WHERE subjectLevel != ''", $con);
        $codeArray = getArray("SELECT subjectCode.subjectCode FROM subjectCode WHERE subjectCode != ''", $con);
        $extensionArray = getArray("SELECT codeExtension.codeExtension FROM codeExtension WHERE codeExtension != ''", $con);
        $gradeArray = getArray("SELECT grade.grade FROM grade WHERE grade != ''", $con);

        //Run queries to get arrays for each examples field
        $exampleNameArray = getArray("SELECT DISTINCT name FROM examples", $con);
        $languageArray = getArray("SELECT languages.languages FROM languages WHERE languages != ''", $con);

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

    $successMessage = null;
    if (isset($_COOKIE['successMsg'])) {
        //Only output if it is not a number
        if (!is_numeric($_COOKIE['successMsg'])) {
            $successMessage = $_COOKIE['successMsg'];
        }

        setcookie('successMsg', time() - 3600);
    }


    function getArray($query, $con) {
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
                    <div id="newItemModal" class="newItemModal">

                    <!-- Modal content -->
                    <div class="newItemModalContent">
                        <span class="closeNewItemModal">&times;</span>
                        <!--Load tabs based on the record being entered-->
                        <div class="edit-tabs">
                            <div class="Education">
                                <!--Make button grey by default-->
                                <button id="addEducationTab" class="indexButton" style="display: block; border: none; border-radius: 0; background-color: #D3D3D3" onclick="showElement('addEducation')">Education</button>
                            </div>
                            <div class="Projects">
                                <button id="addProjectTab" class="indexButton" style="display: block; border: none; border-radius: 0;" onclick="showElement('addProject')">Projects </button>
                            </div>
                        </div>

                        <div id="addEducation" style="display: block">
                            <p>Add education</p>
                            <form autocomplete="off" method="post" action="process.php">
                                <div class="autocomplete">
                                    <input id="newEducationRecordInstitution" class="textInput" type="text" name="newInstitution" placeholder="Institution: e.g. Tawa College" required>
                                </div>
                                <div class="autocomplete">
                                    <input id="newEducationRecordSubject" class="textInput" type="text" name="newSubject" placeholder="Subject: e.g. Science" " required>
                                </div>
                                <div class="autocomplete">
                                    <input id="newEducationRecordYear" class="textInput" type="number" name="newSubjectYear" placeholder="Year: e.g. <?php echo date('Y'); ?>" required>
                                </div>
                                <div class="autocomplete">
                                    <input id="newEducationRecordSubjectLevel" class="textInput" type="text" name="newSubjectLevel" placeholder="Level: e.g. NCEA Level One" required>
                                </div>
                                <div class="autocomplete">
                                    <input id="newEducationRecordCode" class="textInput" type="text" name="newCode"  placeholder="Code: e.g. COMP" required>
                                </div>
                                <div class="autocomplete">
                                    <input id="newEducationRecordCodeExtension" class="textInput" type="text" name="newCodeExtension" placeholder="Code Extension: e.g. 101" required>
                                </div>

                                <!--Change the input type based on the type of grade the user wishes to enter-->
                                <select onchange="showCreditsGpa(this.value)">
                                    <?php
                                        //Set the default order
                                        $optionOne = "showNewCreditsDiv";
                                        $optionTwo = "showNewGpaDiv";
                                        $displayValOne = "Credits";
                                        $displayValTwo = "Gpa";
                                        $displayDivCredits = "block";
                                        $displayDivGpa = "none";
                                    ?>
                                    <option value="<?php echo $optionOne; ?>">
                                        <?php
                                            echo $displayValOne;
                                            //Update the type of grade being submitted
                                        ?>
                                    </option>
                                    <option value="<?php echo $optionTwo; ?>">
                                        <?php
                                            //Update the type of grade being submitted
                                            echo $displayValTwo;
                                        ?>
                                    </option>
                                </select>

                                <div id="showNewCreditsDiv" style="display: <?php echo $displayDivCredits; ?>">
                                    <p>Credits</p>
                                    <input id="newEducationRecordCredits" class=textInput" type="number" name="newCredits" placeholder="Credits: e.g. 22">
                                    <?php
                                        $isNumeric = false;
                                    ?>
                                </div>
                                <div id="showNewGpaDiv" style="display: <?php echo $displayDivGpa; ?>">
                                    <p>GPA</p>
                                    <div class="autocomplete">
                                        <input id="newEducationRecordGpa" class=textInput" type="text" name="newGpa" placeholder="Gpa: e.g. A-">
                                    </div>
                                </div>
                                <input name="newEducationRecord" value="Submit Record" type="submit">
                            </form>

                        </div>
                        <div id="addProject" style="display: none">
                            <p>Add project</p>
                            <!--Todo for play around file uploads it is probably best not to upload the file, but to store the file path instead and use that to display the image. when using cookies-->
                            <!--Upload files. Allow up to five-->
                            <form action="process.php" method="post" enctype="multipart/form-data">
                                <!--The project title-->
                                <div class="autocomplete">
                                    <input id="newExampleName" type="text" name="newExampleName" placeholder="Project Name: E.g. Tarzan" required>
                                </div>

                                <!--The year-->
                                <div class="autocomplete">
                                    <input id="newExampleYear" class="textInput" type="number"
                                           name="newExampleYear" placeholder="Year: E.g. <?php echo date('Y');?>" required>
                                </div>

                                <!--The languages-->
                                <!--Todo ensure that the user cannot select more than five languages-->
                                <?php
                                //Create a checkbox for each language
                                for ($i = 0; $i < sizeof($languageArray); $i++) {
                                   ?>
                                    <br>
                                    <label for="<?php echo $languageArray[$i]; ?>"><?php echo $languageArray[$i] ?></label>
                                    <input type="checkbox" id="<?php echo $languageArray[$i]; ?>"
                                           name="<?php echo $languageArray[$i]; ?>" value="<?php echo $languageArray[$i]; ?>">
                                    <?php
                                }
                                ?>
                                <!--Option that allows the user to add their own code-->
                                <br>
                                <label for="newLanguageInput">Other</label>
                                <input type="checkbox" name = "newLanguageInput" id = "newLanguageInput" onchange="showUpdateLinkInput('newLanguageInputDiv')">

                                <!--Input box for the new language-->
                                <div id = "newLanguageInputDiv" style="display:none;">
                                    <input type="text" name = "newLanguageEntry" placeholder="New Language">
                                </div>

                                <br>
                                <label for="newExamplesLinkCheckbox">Link</label>
                                <input onchange="showUpdateLinkInput('newExamplesLink')"
                                       name="newLinkInput"
                                       type="checkbox" id="newExamplesLinkCheckbox">

                                <!--Div that shows the link-->
                                <div id="newExamplesLink" style="display: none">
                                    <input type="text" name = "newLinkEntry" placeholder="E.g. google.com">
                                </div>

                                <br>
                                <label for="newGithubLinkCheckbox">Github</label>
                                <input onchange="showUpdateLinkInput('newGithubLink')"
                                       type="checkbox" id="newGithubLinkCheckbox"
                                       name = "newGithubInput" >

                                <!--The div that shows the github link-->
                                <div id="newGithubLink" style="display: none">
                                    <input name = "newGithubEntry" type="text"  placeholder="E.g. github.com">
                                </div>

                                <!--The description-->
                                <textarea name="newExampleDescription" style="width: 100%; height: auto" placeholder="Enter some text" required>

                                </textarea>
                                
                                Select image to upload:
                                <input type="file" name="userFiles[]" id="" multiple="">
                                <input type="submit" value="Submit" name="newExampleRecord">
                            </form>
                        </div>
                    </div>

                </div>

                <!--The edit tabs-->
                <div class="edit-tabs">
                    <div class="Education">
                        <!--Make button grey by default-->
                        <button id="educationTab" class="indexButton" style="display: block; border: none; border-radius: 0; background-color: #D3D3D3" onclick="showElement('editEducation')">Education</button>
                    </div>
                    <div class="Projects">
                        <button id="projectTab" class="indexButton" style="display: block; border: none; border-radius: 0;" onclick="showElement('editProjects')">Projects</button>
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
                    ORDER BY education.institutionFK DESC, year.year DESC, credits.credits DESC, grade.gradePK ASC,subjectCode.subjectCode ASC");
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
                                <div style="background-color: #D3D3D3; text-align: center" class="<?php echo $classHeader; ?>">
                                    <h1><?php echo $institution;?></h1>

                                    <!--Display the column titles-->
                                    <div class="education-Titles-Large-Edit">
                                        <div>
                                            <p class="alignTextLeft">Update</p>
                                        </div>
                                        <div>
                                            <p class="alignTextLeft">Code</p>
                                        </div>
                                        <div>
                                            <p class="alignTextLeft">Subject</p>
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
                                            <p class="alignTextLeft">Subject Level</p>
                                        </div>
                                        <div>
                                            <p class="alignTextLeft">Year</p>
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
                                    <!--Show the update div-->
                                    <button id="updateEducation<?php echo $uniqueKey; ?>button"
                                    onclick="showUpdateDiv('updateEducation<?php echo $uniqueKey; ?>', '<?php echo $uniqueKey; ?>', 'Update', 'Hide')">
                                    Update
                                    </button>
                                    <!--Show the delete button-->
                                    <form method="post" action="process.php">
                                        <input type="hidden" value="<?php echo $uniqueKey; ?>" name="uniqueKey">
                                        <input type="submit" value="Delete" name="deleteEducationRecord">
                                    </form>
                                </div>

                                <!--Display the title on a small screen-->
                                <div class="codeTitle">
                                    <p class="alignTextLeft"> Code:</p>
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
                                    <p class="alignTextLeft">Subject:</p>
                                </div>
                                <div class="education-Subject-Edit">
                                    <div style="text-align: center;">
                                        <p class="alignTextLeft"> <?php echo $subject;?></p>
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
                                    <p class="alignTextLeft">Level: </p>
                                </div>
                                <div class="education-subjectLevel">
                                    <div style="text-align: center;">
                                        <p class="alignTextLeft">
                                            <?php
                                            if ($subjectLevel != null) {
                                                echo $subjectLevel;
                                            } else {
                                                echo "NA";
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <!--Display the title on a small screen-->
                                <div class="yearTitle">
                                    <p class="alignTextLeft">Year: </p>
                                </div>
                                <div class="education-Year-Edit">
                                    <div style="text-align: center;">
                                        <p class="alignTextLeft">
                                            <?php
                                            if ($relevantYear != null) {
                                                echo $relevantYear;
                                            } else {
                                                echo "NA";
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!--This form is used to update the records-->
                            <!--Todo add styling for mobile-->
                            <!--todo make it so that the user cannot enter both grades and credits-->

                            <div id="updateEducation<?php echo $uniqueKey; ?>" style="display:none;">
                                <form autocomplete="off" method="post" action="process.php">
                                    <div class="add-grid-container">
                                        <div class="add-Institution autocomplete">
                                            <input id="updateEducationInstitution<?php echo $uniqueKey; ?>" class="textInput"
                                            type="text" name="institution" value="<?php echo $institution; ?>" required>
                                        </div>
                                        <div class="add-Subject autocomplete">
                                            <input id="updateEducationSubject<?php echo $uniqueKey; ?>" class="textInput"
                                            type="text" name="subject" value="<?php echo $subject; ?>" required>
                                        </div>
                                        <div class="add-Subject-Year autocomplete">
                                            <input id="updateEducationYear<?php echo $uniqueKey; ?>" class="textInput"
                                            type="number" name="subjectYear" value="<?php echo $relevantYear; ?>" required>
                                        </div>
                                        <div class="add-Subject-Level autocomplete">
                                            <input id="updateEducationSubjectLevel<?php echo $uniqueKey; ?>" class="textInput"
                                            type="text" name="subjectLevel" value="<?php echo $subjectLevel; ?>" required>
                                        </div>
                                        <div class="add-Code autocomplete">
                                            <input id="updateEducationCode<?php echo $uniqueKey; ?>" class="textInput"
                                            type="text" name="code" value="<?php echo $code; ?>" required>
                                        </div>
                                        <div class="add-Code-Extension autocomplete">
                                            <input id="updateEducationCodeExtension<?php echo $uniqueKey; ?>" class="textInput"
                                            type="text" name="codeExtension" value="<?php echo $codeExtension; ?> " required>
                                        </div>
                                        <div class="add-Grade">
                                            <!--Allow the user to select the type of grade. Set to the current grade type by default-->
                                            <select onchange="showCreditsGpa(this.value)">
                                                <?php
                                                //Set the default order
                                                if ($credits != 0) {
                                                    $optionOne = "showUpdateEducationCreditsDiv" . $uniqueKey;
                                                    $optionTwo = "showUpdateEducationGpaDiv" . $uniqueKey;
                                                    $displayValOne = "Credits";
                                                    $displayValTwo = "Gpa";
                                                    $displayDivCredits = "block";
                                                    $displayDivGpa = "none";
                                                } else {
                                                    $optionOne = "showUpdateEducationGpaDiv" . $uniqueKey;
                                                    $optionTwo = "showUpdateEducationCreditsDiv" . $uniqueKey;
                                                    $displayValOne = "Gpa";
                                                    $displayValTwo = "Credits";
                                                    $displayDivCredits = "none";
                                                    $displayDivGpa = "block";
                                                }
                                                ?>
                                                <option value="<?php echo $optionOne; ?>">
                                                    <?php echo $displayValOne;
                                                    //Update the type of grade being submitted
                                                    ?>
                                                </option>
                                                <option value="<?php echo $optionTwo; ?>">
                                                    <?php
                                                    //Update the type of grade being submitted
                                                    echo $displayValTwo;
                                                    ?>
                                                </option>
                                            </select>

                                            <div id="showUpdateEducationCreditsDiv<?php echo $uniqueKey; ?>" style="display: <?php echo $displayDivCredits; ?>">
                                                <p>Credits</p>
                                                <input id="updateEducationCredits<?php echo $uniqueKey; ?>" class=textInput"
                                                type="number" name="credits" placeholder="<?php echo $credits; ?>">
                                                <?php $isNumeric = false;?>
                                            </div>
                                            <div id="showUpdateEducationGpaDiv<?php echo $uniqueKey; ?>" style="display: <?php echo $displayDivGpa; ?>">
                                                <p>GPA</p>
                                                <div class="autocomplete">
                                                    <input id="updateEducationGrade<?php echo $uniqueKey; ?>" class=textInput"
                                                    type="text" name="gpa" placeholder="<?php echo $grade; ?>">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="save-Record">
                                            <input name="uniqueKey" value="<?php echo $uniqueKey; ?>" type="hidden">
                                            <input name="submitEducationUpdate" value="Submit" type="submit">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>

                <!--The div that contains the projects edit-->
                <div id="editProjects" style="display: none">
                    <p>Projects</p>
                    <?php
                    //The query which shows the examples
                    $experienceQuery = $con->prepare("SELECT examples.uniqueKey, examples.name, year.year, examples.description, 
                    examples.link, examples.github FROM examples LEFT JOIN year ON examples.yearFk = year.yearPK ORDER BY year.year DESC");
                    $experienceQuery->execute();
                    $experienceQuery->bind_result($uniqueKey, $name, $relevantYear, $examplesDescription, $link, $github);
                    $experienceQuery->store_result();
                    $recordCount = $experienceQuery->num_rows();

                    $count = 0;

                    //Display a message if there are no records returned
                    if ($recordCount == 0) {
                        ?>
                        <div style="text-align: center">
                            <h1>Nothing to see here.</h1>
                        </div>
                        <?php
                    } else {
                        while ($row = $experienceQuery->fetch()) {
                            //Calculates if any rounding of the examples div is required
                            $class = "";
                            if ($count == 0) {
                                $class = "examples-grid-container roundTop";
                            } elseif ($count == $recordCount - 1) {
                                $class = "examples-grid-container roundBottom";
                            } else {
                                $class = "examples-grid-container";
                            }

                            //Changing the background colour
                            if ($count % 2 == 0) {
                                //even
                                $colour = '#D3D3D3';
                                $count += 1;
                            } else {
                                //odd
                                $colour = 'white';
                                $count += 1;
                            }

                            $directoryName = "images/examples/" . str_replace(" ", "", $name);

                            //Avoid errors if the file or folder does not exist
                            if (file_exists($directoryName)) {
                                $files = scandir($directoryName);
                                $primaryImage = $files[2];

                                if (!file_exists($directoryName . "/" . $primaryImage)) {
                                    //If the image does not exist, this is the default file path.
                                    $primaryImage = "images/examples/no image.png";
                                } else {
                                    $primaryImage = $directoryName . "/" . $primaryImage;
                                }
                            } else {
                                $primaryImage = "images/examples/no image.png";
                            }

                            //Get the number of other files in the directory
                            $fileCount = 0;
                            $files = glob($directoryName . "/*");
                            if ($files) {
                                $fileCount = count($files);
                            }

                            //Create the slideshow id
                            $slideshowID = "ssID" . $count;

                            $imgWidth = getimagesize($primaryImage)[0];
                            ?>
                            <div style="background-color: <?php echo $colour; ?>;" class="<?php echo $class; ?>">
                                <div class="examples-image">
                                    <div class="slideshowContainer" style="--width: <?php echo $imgWidth; ?>;">
                                        <!-- Load the primary image -->
                                        <div class="<?php echo $slideshowID; ?>">
                                            <div class="slideProgress" style="align-content: center">
                                                <p>
                                                    <?php
                                                    //Only show the next and previous buttons if there are images to be displayed
                                                    if ($primaryImage != "images/examples/no image.png") {
                                                        ?>
                                                        1 / <?php echo $fileCount; ?>
                                                        <?php
                                                    }
                                                    ?>
                                                </p>
                                            </div>
                                            <img class="center rounded" src="<?php echo $primaryImage; ?>" alt="Image of the project">
                                        </div>

                                        <!--Load the next images -->
                                        <?php
                                        //Load the sub images
                                        $progress = 2;
                                        foreach (glob($directoryName . "/*") as $file) {
                                            //Don't show the primary image
                                            if ($file != $primaryImage) {
                                                ?>
                                                <!-- Load the secondary image -->
                                                <div class="<?php echo $slideshowID; ?>">
                                                    <div class="slideProgress">
                                                        <p><?php echo $progress . " / " . $fileCount; ?></p>
                                                    </div>
                                                    <img class="center rounded" src="<?php echo $file; ?>" alt="Image of the project">
                                                </div>
                                                <?php
                                                //Increment the progress
                                                $progress += 1;
                                            }
                                        }

                                        //Only show the next and previous buttons if there are images to be displayed
                                        if ($primaryImage != "images/examples/no image.png") {
                                            ?>
                                            <!--navigation buttons for the sideshow -->
                                            <a class="prev roundBottomLeft" onclick="plusDivs(-1, <?php echo $count - 1; ?>)">&#10094;</a>
                                            <a class="next roundBottomRight" onclick="plusDivs(+1, <?php echo $count - 1; ?>)">&#10095;</a>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="examples-name">
                                    <h1><?php echo $name;?></h1>
                                </div>
                                <div class="examples-year">
                                    <p class="alignTextLeft">Year:<?php echo $relevantYear;?></p>
                                </div>
                                <div class="examples-language">
                                    <?php
                                    $key = $uniqueKey;

                                    //Define queries to get the languages
                                    $langOneQuery = $con->prepare("SELECT languages.languages
                                    FROM examples
                                    LEFT JOIN languages ON examples.languageOneFK = languages.languagesPK
                                    WHERE examples.uniqueKey LIKE $key
                                    ");

                                    $langTwoQuery = $con->prepare("SELECT languages.languages
                                    FROM examples
                                    LEFT JOIN languages ON examples.languageTwoFK = languages.languagesPK
                                    WHERE examples.uniqueKey LIKE $key
                                    ");

                                    $langThreeQuery = $con->prepare("SELECT languages.languages
                                    FROM examples
                                    LEFT JOIN languages ON examples.languageThreeFK = languages.languagesPK
                                    WHERE examples.uniqueKey LIKE $key
                                    ");

                                    $langFourQuery = $con->prepare("SELECT languages.languages
                                    FROM examples
                                    LEFT JOIN languages ON examples.languageFourFK = languages.languagesPK
                                    WHERE examples.uniqueKey LIKE $key
                                    ");

                                    $langFiveQuery = $con->prepare("SELECT languages.languages
                                    FROM examples
                                    LEFT JOIN languages ON examples.languageFiveFK = languages.languagesPK
                                    WHERE examples.uniqueKey LIKE $key
                                    ");

                                    //Execute each query
                                    $langOneQuery->execute();
                                    $langOneQuery->bind_result($langOne);
                                    $langOneQuery->store_result();
                                    $langOneQuery->fetch();

                                    $langTwoQuery->execute();
                                    $langTwoQuery->bind_result($langTwo);
                                    $langTwoQuery->store_result();
                                    $langTwoQuery->fetch();

                                    $langThreeQuery->execute();
                                    $langThreeQuery->bind_result($langThree);
                                    $langThreeQuery->store_result();
                                    $langThreeQuery->fetch();

                                    $langFourQuery->execute();
                                    $langFourQuery->bind_result($langFour);
                                    $langFourQuery->store_result();
                                    $langFourQuery->fetch();

                                    $langFiveQuery->execute();
                                    $langFiveQuery->bind_result($langFive);
                                    $langFiveQuery->store_result();
                                    $langFiveQuery->fetch();


                                    ?>
                                    <p class="alignTextLeft">
                                        language(s):
                                        <?php
                                        $languages = "";
                                        if ($langOne != null) {
                                            $languages = $languages . $langOne;
                                        }
                                        if ($langTwo != null) {
                                            $languages = $languages . ", " . $langTwo;
                                        }
                                        if ($langThree != null) {
                                            $languages = $languages . ", " . $langThree;
                                        }
                                        if ($langFour != null) {
                                            $languages = $languages . ", " . $langFour;
                                        }
                                        if ($langFive != null) {
                                            $languages = $languages . ", " . $langFive;
                                        }
                                        echo $languages;
                                        ?>
                                    </p>
                                </div>
                                <div class="examples-link">
                                    <p class="alignTextLeft">
                                        <?php
                                        //Only displays if there is a link to display
                                        if ($link != null) {
                                            ?>
                                            Link:
                                            <a class="pageLink" href="<?php echo $link; ?>"><?php echo $link;?></a>
                                            <?php
                                        } else {
                                            echo "Sorry, there is no link to be displayed.";
                                        }
                                        ?>
                                    </p>
                                    <p class="alignTextLeft">
                                        <?php
                                        //Only displays if there is a link to display and the repo is no private
                                        if ($github != null) {
                                            ?>
                                            GitHub:
                                            <a class="pageLink" href="<?php echo $github; ?>"><?php echo $github;?> </a>
                                            <?php
                                        } else {
                                            echo "Sorry, there is no github link.";
                                        }
                                        ?>
                                    </p>
                                </div>
                                <div class="examples-description alignTextLeft">
                                    <p><?php echo $examplesDescription;?></p>
                                </div>
                            </div>
                            <button id="updateExample<?php echo $uniqueKey; ?>button"
                            onclick="showUpdateDiv('updateExample<?php echo $uniqueKey; ?>', '<?php echo $uniqueKey; ?>', 'Update', 'Hide')">
                            Update
                            </button>

                            <!-- The div that is used to edit the example-->
                            <div id="updateExample<?php echo $uniqueKey; ?>"style="background-color: <?php echo $colour; ?>; display: none">
                                <p>This is the update div</p>
                                <!--Form for updating the examples-->
                                <form method="post" action="process.php" autocomplete="off">
                                    <!--The project title-->
                                    <div class="autocomplete">
                                        <input id="updateExampleName<?php echo $uniqueKey; ?>" type="text" name="exampleName" value="<?php echo $name; ?>" required>
                                    </div>

                                    <!--The year-->
                                    <div class="autocomplete">
                                        <input id="updateExampleYear<?php echo $uniqueKey; ?>" class="textInput" type="number"
                                        name="exampleYear" value="<?php echo $relevantYear; ?>" required>
                                    </div>

                                    <!--The languages-->
                                    <!--Todo ensure that the user cannot select more than five languages-->
                                    <?php
                                    //Create a checkbox for each language
                                    for ($i = 0; $i < sizeof($languageArray); $i++) {
                                        //todo add an option for adding new languages
                                        //Checking if the language matches one of the languages used in the example
                                        $checked = "";
                                        if ($languageArray[$i] == $langOne || $languageArray[$i] == $langTwo || $languageArray[$i] == $langThree ||
                                            $languageArray[$i] == $langFour || $languageArray[$i] == $langFive) {
                                            $checked = "checked";
                                        }

                                        ?>
                                        <br>
                                        <label for="<?php echo $languageArray[$i]; ?>"><?php echo $languageArray[$i] ?></label>
                                        <input type="checkbox" id="<?php echo $languageArray[$i]; ?>"
                                        name="<?php echo $languageArray[$i]; ?>" value="<?php echo $languageArray[$i]; ?>" <?php echo $checked; ?>>
                                        <?php
                                    }
                                    ?>
                                    <!--Option that allows the user to add their own code-->
                                    <br>
                                    <label for="newLanguage<?php echo $uniqueKey;?>">Other</label>
                                    <input type="checkbox" name = "updateLanguageInput" id = "newLanguage<?php echo $uniqueKey;?>" onchange="showUpdateLinkInput('newLanguageDiv<?php echo $uniqueKey;?>')">

                                    <!--Input box for the new language-->
                                    <div id = "newLanguageDiv<?php echo $uniqueKey;?>" style="display:none;">
                                        <input type="text" name = "updateLanguageEntry" placeholder="New Language">
                                    </div>

                                    <!--The link-->
                                    <?php
                                    //Auto check if there is a link
                                    $checked = "";
                                    $displayLinkDiv = "none";
                                    $linkToDisplay = "";
                                    $placeholder = "Link";
                                    if ($link != null) {
                                        $checked = "checked";
                                        $displayLinkDiv = "block";
                                        $linkToDisplay = $link;
                                        $placeholder = "";
                                    }
                                    ?>
                                    <br>
                                    <label for="updateExamplesLinkCheckbox<?php echo $uniqueKey; ?>">Link</label>
                                    <input onchange="showUpdateLinkInput('updateExamplesLink<?php echo $uniqueKey; ?>')"
                                    name = "updateLinkInput";
                                    type="checkbox" id="updateExamplesLinkCheckbox<?php echo $uniqueKey; ?>" <?php echo $checked; ?>>

                                    <!--Div that shows the link-->
                                    <div id="updateExamplesLink<?php echo $uniqueKey; ?>" style="display: <?php echo $displayLinkDiv; ?>">
                                        <input type="text" name = "updateLinkEntry" value="<?php echo $linkToDisplay; ?>" placeholder="<?php echo $placeholder;?>">
                                    </div>

                                    <!--Github-->
                                    <?php
                                    //Auto check if there is a link
                                    $checked = "";
                                    $displayLinkDiv = "none";
                                    $linkToDisplay = "";
                                    $placeholder = "Github link";
                                    if ($github != null) {
                                        $checked = "checked";
                                        $displayLinkDiv = "block";
                                        $linkToDisplay = $github;
                                        $placeholder = "";
                                    }
                                    ?>

                                    <br>
                                    <label for="updateGithubLinkCheckbox<?php echo $uniqueKey; ?>">Github</label>
                                    <input onchange="showUpdateLinkInput('updateGithubLink<?php echo $uniqueKey; ?>')"
                                    name = "updateGithubInput"
                                    type="checkbox" id="updateGithubLinkCheckbox<?php echo $uniqueKey; ?>" <?php echo $checked ?>>

                                    <!--The div that shows the github link-->
                                    <div id="updateGithubLink<?php echo $uniqueKey; ?>" style="display: <?php echo $displayLinkDiv; ?>">
                                        <input name = "updateGithubEntry" type="text" value="<?php echo $linkToDisplay; ?>" placeholder="<?php echo $placeholder;?>">
                                    </div>

                                    <!--The description-->
                                    <textarea name="exampleDescription" style="width: 100%; height: auto" required>
                                        <?php echo $examplesDescription;?>
                                    </textarea>

                                    <input type="hidden" name="uniqueKey" value="<?php echo $uniqueKey;?>">
                                    <input type="submit" name="submitExampleUpdate" value="Update">
                                </form>

                                <!--The image gallery. Only display if there are images-->
                                <?php
                                if ($fileCount != 0) {
                                    ?>
                                    <button id="editImages<?php echo $uniqueKey; ?>button"
                                    onclick="showUpdateDiv('editImages<?php echo $uniqueKey; ?>', '<?php echo $uniqueKey; ?>', 'Edit Images', 'Hide Images')">
                                    Edit Images
                                    </button>
                                    <?php
                                } else {
                                    echo "There are no images to be edited. Click the button below to add some.";
                                    //todo add button that can be used to add images to the record
                                }
                                ?>
                                <div id="editImages<?php echo $uniqueKey; ?>" style="display: none">
                                    <div class="gallery-container">
                                        <?php
                                        foreach (glob($directoryName . "/*") as $file) {
                                        ?>
                                            <div>
                                                <img src="<?php echo $file; ?>" style="width: 250px">

                                                <!--The delete image form-->
                                                <form method="post" action="process.php">
                                                    <input name="file" type="hidden" value="<?php echo $file; ?>">
                                                    <input type="submit" name="deleteImage" value="Delete">
                                                </form>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>

                <a href="logout.php">Logout</a>
            </div>
        </div>
    </body>
    <script>
        //Code used for the slide shows
        const slideIndex = [];

        //Pre-populate the list. Dynamically adjusts based on the number of instances of the grid class
        const slideId = [];
        for (let i = 0; i < document.querySelectorAll(".examples-grid-container").length; i++) {
            slideId.push("ssID" + (i + 1));
            slideIndex.push(1);
            showDivs(1, i);
        }

        function plusDivs(n, no) {
            showDivs(slideIndex[no] += n, no);
        }

        function showDivs(n, no) {
            let i;
            const x = document.getElementsByClassName(slideId[no]);
            if (n > x.length) {
                slideIndex[no] = 1
            }
            if (n < 1) {
                slideIndex[no] = x.length
            }
            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";
            }
            x[slideIndex[no] - 1].style.display = "block";
        }


        //Code used to control the new item modal
        // Get the modal
        var itemModal = document.getElementById("newItemModal");

        // Get the button that opens the modal
        var itemButton = document.getElementById("newProjectButton");

        // Get the <span> element that closes the modal
        var itemSpan = document.getElementsByClassName("closeNewItemModal")[0];

        // When the user clicks on the button, open the modal
        itemButton.onclick = function () {
            itemModal.style.display = "block";

            //Load the autocomplete for new records
            loadNewEducationRecordAutocomplete();
            loadNewExampleRecordAutocomplete();
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

        //Shows and hides the link input in the examples update
        function showUpdateLinkInput(id) {
            //Check if the element is already displayed
            if (document.getElementById(id).style.display == "block") {
                hideElement(id);
            } else {
                document.getElementById(id).style.display = "block";
            }

        }

        //Hides and shows the update div for education and examples
        function showUpdateDiv(divName, key, showText, hideText) {
            var x = document.getElementById(divName);
            if (x.style.display === "none") {
                x.style.display = "block";

                //Change the button text to hide
                updateButton(divName + 'button', hideText)
            } else {
                x.style.display = "none";
                updateButton(divName + 'button', showText);
            }

            //Load the autocomplete
            if (divName.includes("Education")) {
                loadAutocompleteForEducationUpdate(key);
            } else if (divName.includes("Example")) {
                loadAutocompleteForExamplesUpdate(key);
            }
        }

        //This function takes a button id and the desired text and updates it
        function updateButton(id, text) {
            document.querySelector('#' + id).innerText = text;
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
            }
            document.getElementById(show).style.display = "block";
        }

        //Hide the relevant element
        function hideElement(id) {
            document.getElementById(id).style.display = "none";
        }

        //Takes a parent div and array of child inputs
        function showCreditsGpa(show, childInputs) {
            var id = 0;
            //Change div for updating
            if (show.includes("Update")) {
                if (show.includes("Credits")) {
                    //isolate the id
                    id = show.substring("showUpdateEducationCreditsDiv".length)

                    //Show and hide the relevant divs
                    document.getElementById("showUpdateEducationCreditsDiv" + id).style.display = 'block';
                    hideElement("showUpdateEducationGpaDiv" + id);

                } else if (show.includes("Gpa")) {
                    id = show.substring("showUpdateEducationGpaDiv".length)

                    //Show and hide the relevant divs
                    document.getElementById("showUpdateEducationGpaDiv" + id).style.display = 'block';
                    hideElement("showUpdateEducationCreditsDiv" + id);
                }

                //Change div for new records
            } else if (show.includes("New")) {
                if (show.includes("Credits")) {
                    //Show and hide the relevant divs
                    document.getElementById("showNewCreditsDiv").style.display = 'block';
                    hideElement("showNewGpaDiv");
                } else if (show.includes("Gpa")) {
                    //Show and hide the relevant divs
                    document.getElementById("showNewGpaDiv").style.display = 'block';
                    hideElement("showNewCreditsDiv");
                }
            }

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
        var exampleNames = <?php echo json_encode($exampleNameArray);?>;

        //Load the autocompletes for new education items
        function loadNewEducationRecordAutocomplete() {
            //Autocomplete for new records
            autocomplete(document.getElementById("newEducationRecordInstitution"), institutions);
            autocomplete(document.getElementById("newEducationRecordSubject"), subjects);
            autocomplete(document.getElementById("newEducationRecordYear"), years);
            autocomplete(document.getElementById("newEducationRecordSubjectLevel"), subjectLevels);
            autocomplete(document.getElementById("newEducationRecordCode"), subjectCodes);
            autocomplete(document.getElementById("newEducationRecordCodeExtension"), codeExtensions);
            autocomplete(document.getElementById("newEducationRecordGpa"), grades);
        }

        //Load the autocomplete for new examples
        function loadNewExampleRecordAutocomplete() {
            autocomplete(document.getElementById("newExampleName"), exampleNames);//todo remove this to decrease the chance of potential duplicates?
            autocomplete(document.getElementById("newExampleYear"), years);
        }

        //Loads autocomplete for updating education
        function loadAutocompleteForEducationUpdate(id) {
            autocomplete(document.getElementById("updateEducationInstitution" + id), institutions);
            autocomplete(document.getElementById("updateEducationSubject" + id), subjects);
            autocomplete(document.getElementById("updateEducationYear" + id), years);
            autocomplete(document.getElementById("updateEducationSubjectLevel" + id), subjectLevels);
            autocomplete(document.getElementById("updateEducationCode" + id), subjectCodes);
            autocomplete(document.getElementById("updateEducationCodeExtension" + id), codeExtensions);
            autocomplete(document.getElementById("updateEducationGrade" + id), grades);
        }

        //Loads autocomplete for updating examples
        function loadAutocompleteForExamplesUpdate(id) {
            autocomplete(document.getElementById("updateExampleName" + id), exampleNames);//todo remove this to decrease the amount of potential duplicates?
            autocomplete(document.getElementById("updateExampleYear" + id), years);
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
        <div class="alert alert-danger" style="width: 100%; position: fixed">
            <strong>Operation failed!</strong> <?php echo $errorMessage; ?>
        </div>
        <?
    }

    //Show any success messages if required
    if ($successMessage != null) {
        ?>
        <div class="alert alert-success" role="alert" style="width: 100%; position: fixed">
            <?php echo $successMessage; ?>
        </div>
        <?php
    }
    ?>
</html>
