<?php
session_start();
require("connect.php");
if (!isset($_SESSION['loggedin'])) {
    //Set a NOT logged in flag
    $loggedIn = false;
} else {
    //set a logged in flag
    $loggedIn = true;

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
    $languageArrayDB = getArray("SELECT languages.languages FROM languages WHERE languages != ''", $con);
}

//Gets an array of values from the database. Used for autocomplete
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

//Function to check if a directory is empty
function dir_is_empty($dir)
{
    $handle = opendir($dir);
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            closedir($handle);
            return FALSE;
        }
    }
    closedir($handle);
    return TRUE;
}

//Finds the unique key of a value in one of the play around arrays. Returns it's location, false if it was not found
function findPlayAroundKeyLocation($key, $array)
{
    for ($i = 0; $i < sizeof($array); $i++) {
        if ($array[$i][0] == $key) {
            return $i;
        }
    }
    return false;
}

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

//Check to see if the playAroundEducation session exists and the user is not logged in
if (!isset($_SESSION['playAroundEducation']) && $loggedIn == false) {
    //Create some default records
    $defaultRec1 = [0, "Tawa College", "Computer Science", "COMP", 301, null, 22, "NCEA Level Three", 2018];
    $defaultRec2 = [1, "Tawa College", "Data Science", "DATS", 312, null, 18, "NCEA Level Two", 2017];
    $defaultRec3 = [2, "A Test School", "Computer Graphics", "CGRA", 151, "A", null, "Year One University", 2019];
    //Create the session
    $_SESSION['playAroundEducation'] = [$defaultRec1, $defaultRec3, $defaultRec2];
}

//Check to see if the playAroundExamples session exists and the user is not logged in
if (!isset($_SESSION['playAroundExamples']) && $loggedIn == false) {
    //Create some default Records
    $defaultRec1 = [0, ['Images/userImages/cat.jpeg', 'Images/userImages/eraser.jpeg', 'Images/userImages/phone.jpeg'], "How I Saved To World Twice", ["CSS", "PHP", "JavaScript", "HTML"],
        "http://luke.dx.am", "http://github.com/hawadlu", "Whoops, I forgot to write a description here.", 2019];
    $defaultRec2 = [1, ['Images/userImages/fork.jpeg', 'Images/userImages/nailClippers.jpeg', 'Images/userImages/phone.jpeg', 'Images/userImages/spring.jpeg'],
        "What I Ate For Breakfast", ["CSS", "PHP", "RUBY", "Perl"], 'https://breakfast.co.nz', "https://github.com/PushyPixels/BreakfastWithUnity",
        "Contrary to the opinions of many people. I did not eat a fork, nail clippers, a phone and a spring for breakfast. I ate cornflakes instead. 
        I know that it's disappointing and a little anticlimactic.", 2020];
    $_SESSION['playAroundExamples'] = array($defaultRec1, $defaultRec2);
}

if (isset($_SESSION['playAroundEducation']) && $loggedIn == false) {
    //Setup the autocomplete education arrays for the session variables
    $institutionArray = getUniqueValuesForSession($_SESSION['playAroundEducation'], 1);
    $subjectArray = getUniqueValuesForSession($_SESSION['playAroundEducation'], 2);
    $codeArray = getUniqueValuesForSession($_SESSION['playAroundEducation'], 3);
    $extensionArray = getUniqueValuesForSession($_SESSION['playAroundEducation'], 4);
    $gradeArray = getUniqueValuesForSession($_SESSION['playAroundEducation'], 5);
    $subjectLevelArray = getUniqueValuesForSession($_SESSION['playAroundEducation'], 7);
    $yearArray = getUniqueValuesForSession($_SESSION['playAroundEducation'], 8);

    //sort by the institution
    $arrayLower = array_map('strtolower', $institutionArray);
    array_multisort($arrayLower, SORT_ASC, SORT_STRING, $institutionArray);

    //Order the education by the institution array
    $educationArray = $_SESSION['playAroundEducation'];
    $_SESSION['playAroundEducation'] = [];
    for ($i = 0; $i < sizeof($institutionArray); $i++) {
        $tmp = [];

        for ($j = 0; $j < sizeof($educationArray); $j++) {
            if ($educationArray[$j][1] == $institutionArray[$i]) {
                array_push($tmp, $educationArray[$j]);
            }
        }

        //Get the unique years
        $uniqueYears = getUniqueValuesForSession($tmp, 8);
        array_multisort($uniqueYears, SORT_ASC, SORT_NUMERIC, $uniqueYears);

        //Sort the temporary array by the years
        $tmp2 = [];
        for ($j = 0; $j < sizeof($uniqueYears); $j++) {
            for ($k = 0; $k < sizeof($tmp); $k++) {
                if ($tmp[$k][8] == $uniqueYears[$j]) {
                    array_push($tmp2, $tmp[$k]);
                }
            }
        }

        //Push to the session array
        for ($j = 0; $j < sizeof($tmp2); $j++) {
            array_push($_SESSION['playAroundEducation'], $tmp2[$j]);
        }
    }
}

if (isset($_SESSION['playAroundExamples']) && $loggedIn == false) {
    //Setup arrays for the autocomplete
    $exampleNameArray = getUniqueValuesForSession($_SESSION['playAroundExamples'], 2);

    //Set some default languages
    if (!isset($_SESSION['sessionLanguages'])) {
        $_SESSION['sessionLanguages'] = ['CSS', 'HTML', 'JavaScript', 'Java', 'PHP', 'Perl', 'python', 'Ruby', 'C++', 'C', 'C#'];
    }

    //All of the images that the user can use
    if (!isset($_SESSION['sessionImages'])) {
        $_SESSION['sessionImages'] = [];

        //Get all of the possible images
        $path    = 'Images/userImages';
        $files = scandir($path);
        $files = array_diff(scandir($path), array('.', '..'));

        //Add to the images array
        foreach ($files as $image) {
            if (strpos($image, '.jpeg') !== false) {
                array_push($_SESSION['sessionImages'], "Images/userImages/" . $image);
            }
        }
    }

    $imageArray = $_SESSION['sessionImages'];
    $languageArray = $_SESSION['sessionLanguages'];
}


//function to get all the unique values for the session variables. Takes an array and the position of the elements to look at
function getUniqueValuesForSession($array, $location)
{
    $uniqueValues = [];
    for ($i = 0; $i < sizeof($array); $i++) {
        //Check if the item at the specified location already exists in the uniqueValues array
        if (!in_array($array[$i][$location], $uniqueValues)) {
            array_push($uniqueValues, $array[$i][$location]);
        }
    }
    return $uniqueValues;
}

//get the head
require("head.php");
?>
<html lang="English">
<!-- The new project Modal -->
<body id="body" class="background-img">
<div id="pageGrid" class="page-grid-container">
    <div style="background-color: white; opacity: 80%" class="roundAll">
        <!--The edit tabs-->
        <div class="edit-tabs">
            <div class="Education">
                <!--Make button grey by default-->
                <button id="educationTab" class="indexButton"
                        style="display: block; border: none; border-radius: 20px 0 0 0;  background-color: #D3D3D3"
                        onclick="showElement('editEducation')">Education
                </button>
            </div>
            <div class="Projects">
                <button id="projectTab" class="indexButton"
                        style="display: block; border: none; border-radius: 0 20px 0 0; background-color: white"
                        onclick="showElement('editProjects')">Projects
                </button>
            </div>
        </div>

        <?php
        //Only show the database fields to the user if they are logged in
        if ($loggedIn == true) {
            ?>
            <div style="background-color: #D3D3D3">
                <!--Button for adding new items-->
                <!-- Trigger/Open The Modal -->
                <br>
                <div style="text-align: center;">
                    <button onclick="showPopup('popup'); loadNewEducationRecordAutocomplete(); loadNewExampleRecordAutocomplete()"
                            class="newItemButton">Create New Item
                    </button>
                </div>
                <br>
            </div>

            <!--The div that contains the education edit. Shown by default-->
            <div id="editEducation" style="display: block">
                <?php
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
                        <h1>Nothing to see here.</h1>
                    </div>
                    <?php
                } else {
                    while ($row = $educationQuery->fetch()) {
                        //Calculates if any rounding of the examples div is required
                        $classHeader = "";
                        $classContent = "";

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
                                <h1><?php echo $institution; ?></h1>

                                <!--Display the column titles-->
                                <div class="education-Titles-Large-Edit">
                                    <div>
                                        <p class="alignTextLeft">Update/Delete</p>
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
                                <div class="updateDelete-container educationUpdateDelete">
                                    <div class="delete" id="deleteEducation<?php echo $uniqueKey; ?>">
                                        <!--Show the delete button-->
                                        <form method="post" action="process.php">
                                            <input type="hidden" value="<?php echo $uniqueKey; ?>" name="uniqueKey">
                                            <?php
                                            //If it is the last record round the lower left corner
                                            $round = "";
                                            if ($count == $recordCount) {
                                                $round = "roundBottomLeft";
                                            }
                                            ?>
                                            <button type="submit" class="deleteButton <?php echo $round; ?>"
                                                    name="deleteEducationRecord"
                                                    style="padding: 0; --bgColour: <?php echo $colour; ?>">
                                                <img src="images/bin.png" class="binImage <?php echo $round; ?>">
                                            </button>
                                        </form>
                                    </div>
                                    <div class="update">
                                        <!--Show the update div-->
                                        <button id="updateEducation<?php echo $uniqueKey; ?>button" class="updateButton"
                                                onclick="showUpdateDiv('updateEducation<?php echo $uniqueKey; ?>', '<?php echo $uniqueKey; ?>', 'Update', 'Hide', 'deleteEducation<?php echo $uniqueKey; ?>');
                                                        loadAutocompleteForEducationUpdate(<?php echo $uniqueKey; ?>)">
                                            Update
                                        </button>
                                    </div>
                                </div>
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
                                    <p class="alignTextLeft"> <?php echo $subject; ?></p>
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
                        <div id="updateEducation<?php echo $uniqueKey; ?>"
                             style="display:none; background-color: <?php echo $colour; ?>">
                            <form autocomplete="off" method="post" action="process.php">
                                <div class="addEducationContainer">
                                    <!--Institution header-->
                                    <div class="institutionTitle" style="text-align: center;">
                                        <h2>Institution</h2>
                                    </div>

                                    <!--Institution-->
                                    <div class="addInstitution">
                                        <div class="add-Institution autocomplete">
                                            <input id="updateEducationInstitution<?php echo $uniqueKey; ?>"
                                                   class="textInput"
                                                   type="text" name="institution" value="<?php echo $institution; ?>"
                                                   required>
                                        </div>
                                    </div>

                                    <!--Subject level header-->
                                    <div class="levelTitle" style="text-align: center">
                                        <h2>Level</h2>
                                    </div>

                                    <!--Subject Level-->
                                    <div class="addLevel">
                                        <div class="add-Subject-Level autocomplete">
                                            <input id="updateEducationSubjectLevel<?php echo $uniqueKey; ?>"
                                                   class="textInput"
                                                   type="text" name="subjectLevel" value="<?php echo $subjectLevel; ?>"
                                                   required>
                                        </div>
                                    </div>

                                    <!--Year header-->
                                    <div class="yearTitleNewEducation" style="text-align: center">
                                        <h2>Year</h2>
                                    </div>

                                    <!--The year-->
                                    <div class="addYear">
                                        <div class="add-Subject-Year autocomplete">
                                            <input id="updateEducationYear<?php echo $uniqueKey; ?>" class="textInput"
                                                   type="number" name="subjectYear" value="<?php echo $relevantYear; ?>"
                                                   required>
                                        </div>
                                    </div>

                                    <!--Subject header-->
                                    <div class="subjectTitleNew" style="text-align: center">
                                        <h2>Subject</h2>
                                    </div>

                                    <!--The subject-->
                                    <div class="addSubject">
                                        <div class="add-Subject autocomplete">
                                            <input id="updateEducationSubject<?php echo $uniqueKey; ?>"
                                                   class="textInput"
                                                   type="text" name="subject" value="<?php echo $subject; ?>" required>
                                        </div>
                                    </div>

                                    <!--Code header-->
                                    <div class="codeTitleNew" style="text-align: center;">
                                        <h2>Code</h2>
                                    </div>

                                    <!--The code-->
                                    <div class="addCode">
                                        <div class="add-Code autocomplete">
                                            <input id="updateEducationCode<?php echo $uniqueKey; ?>" class="textInput"
                                                   type="text" name="code" value="<?php echo $code; ?>" required>
                                        </div>
                                    </div>

                                    <!--Code extension header-->
                                    <div class="codeExtensionTitle" style="text-align: center">
                                        <h2>Code Extension</h2>
                                    </div>

                                    <!--Code extension-->
                                    <div class="addCodeExtension">
                                        <div class="add-Code-Extension autocomplete">
                                            <input id="updateEducationCodeExtension<?php echo $uniqueKey; ?>"
                                                   class="textInput"
                                                   type="text" name="codeExtension"
                                                   value="<?php echo $codeExtension; ?> " required>
                                        </div>
                                    </div>

                                    <!--Grade type header-->
                                    <div class="gradeTypeTitle" style="text-align: center">
                                        <h2>Grade Type</h2>
                                    </div>

                                    <!--Grade type-->
                                    <div class="addGradeType">
                                        <div class="add-Grade">
                                            <!--Allow the user to select the type of grade. Set to the current grade type by default-->
                                            <select onchange="showCreditsGpa(this.value)" class="textInput">
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

                                        </div>
                                    </div>

                                    <!--Grade header-->
                                    <div class="gradeTitleNew" style="text-align: center">
                                        <h2>Grade/Credits</h2>
                                    </div>

                                    <!--Grade-->
                                    <div class="addGrade">
                                        <div id="showUpdateEducationCreditsDiv<?php echo $uniqueKey; ?>"
                                             style="display: <?php echo $displayDivCredits; ?>">
                                            <input id="updateEducationCredits<?php echo $uniqueKey; ?>" class=textInput"
                                                   type="number" name="credits" placeholder="<?php echo $credits; ?>">
                                            <?php $isNumeric = false; ?>
                                        </div>
                                        <div id="showUpdateEducationGpaDiv<?php echo $uniqueKey; ?>"
                                             style="display: <?php echo $displayDivGpa; ?>">
                                            <div class="autocomplete">
                                                <input id="updateEducationGrade<?php echo $uniqueKey; ?>"
                                                       class=textInput"
                                                       type="text" name="gpa" placeholder="<?php echo $grade; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!--Submit header-->
                                    <div class="submitTitleNew" style="text-align: center">
                                        <h2>Submit</h2>
                                    </div>

                                    <!--Submit-->
                                    <div class="submit">
                                        <div class="save-Record">
                                            <input name="uniqueKey" value="<?php echo $uniqueKey; ?>" type="hidden">
                                            <input name="submitEducationUpdate" value="Submit" type="submit"
                                                   class="textInput updateButton">
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </form>
                        </div>
                        <?php
                    }
                    $educationQuery->close();
                }
                ?>
            </div>

            <!--The div that contains the projects edit-->
            <div id="editProjects" style="display: none">
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
                        if ($count == $recordCount - 1) {
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
                        if (file_exists($directoryName) && !dir_is_empty($directoryName)) {
                            $files = scandir($directoryName);
                            $primaryImage = $files[2];

                            if (!file_exists($directoryName . "/" . $primaryImage)) {
                                //If the image does not exist, this is the default file path.
                                $primaryImage = null;
                            } else {
                                $primaryImage = $directoryName . "/" . $primaryImage;
                                $imgWidth = getimagesize($primaryImage)[0];
                            }
                        } else {
                            $primaryImage = null;
                        }

                        //Get the number of other files in the directory
                        $fileCount = 0;
                        $files = glob($directoryName . "/*");
                        if ($files) {
                            $fileCount = count($files);
                        }

                        //Create the slideshow id
                        $slideshowID = "ssID" . $count; ?>

                        <div style="background-color: <?php echo $colour; ?>;" class="<?php echo $class; ?>">
                            <div class="examples-name">
                                <h1><?php echo $name; ?></h1>
                            </div>
                            <div class="examples-year">
                                <p class="alignTextLeft">Year:<?php echo $relevantYear; ?></p>
                            </div>
                            <div class="examples-language">
                                <?php
                                $key = $uniqueKey;

                                //Define queries to get the languages
                                $langOneQuery = $con->prepare("SELECT languages.languages
                                        FROM examples
                                        LEFT JOIN languages ON examples.languageOneFK = languages.languagesPK
                                        WHERE examples.uniqueKey LIKE ?");
                                $langOneQuery->bind_param('i', $key);

                                $langTwoQuery = $con->prepare("SELECT languages.languages
                                        FROM examples
                                        LEFT JOIN languages ON examples.languageTwoFK = languages.languagesPK
                                        WHERE examples.uniqueKey LIKE ? ");
                                $langTwoQuery->bind_param('i', $key);

                                $langThreeQuery = $con->prepare("SELECT languages.languages
                                        FROM examples
                                        LEFT JOIN languages ON examples.languageThreeFK = languages.languagesPK
                                        WHERE examples.uniqueKey LIKE ?");
                                $langThreeQuery->bind_param('i', $key);

                                $langFourQuery = $con->prepare("SELECT languages.languages
                                        FROM examples
                                        LEFT JOIN languages ON examples.languageFourFK = languages.languagesPK
                                        WHERE examples.uniqueKey LIKE ?");
                                $langFourQuery->bind_param('i', $key);

                                $langFiveQuery = $con->prepare("SELECT languages.languages
                                        FROM examples
                                        LEFT JOIN languages ON examples.languageFiveFK = languages.languagesPK
                                        WHERE examples.uniqueKey LIKE ?");
                                $langFiveQuery->bind_param('i', $key);

                                //Execute each query
                                $langOneQuery->execute();
                                $langOneQuery->bind_result($langOne);
                                $langOneQuery->store_result();
                                $langOneQuery->fetch();
                                $langOneQuery->close();

                                $langTwoQuery->execute();
                                $langTwoQuery->bind_result($langTwo);
                                $langTwoQuery->store_result();
                                $langTwoQuery->fetch();
                                $langTwoQuery->close();

                                $langThreeQuery->execute();
                                $langThreeQuery->bind_result($langThree);
                                $langThreeQuery->store_result();
                                $langThreeQuery->fetch();
                                $langThreeQuery->close();

                                $langFourQuery->execute();
                                $langFourQuery->bind_result($langFour);
                                $langFourQuery->store_result();
                                $langFourQuery->fetch();
                                $langFourQuery->close();

                                $langFiveQuery->execute();
                                $langFiveQuery->bind_result($langFive);
                                $langFiveQuery->store_result();
                                $langFiveQuery->fetch();
                                $langFiveQuery->close();


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
                                        <a class="pageLink" href="<?php echo $link; ?>"><?php echo $link; ?></a>
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
                                        <a class="pageLink" href="<?php echo $github; ?>"><?php echo $github; ?> </a>
                                        <?php
                                    } else {
                                        echo "Sorry, there is no github link.";
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="examples-description alignTextLeft">
                                <?php
                                //Only display images if there are images to display
                                if (!is_null($primaryImage)) {
                                    ?>
                                    <div style="padding-right: 10px; float: left">
                                        <div class="slideshowContainer" style="--width: <?php echo $imgWidth; ?>;">
                                            <!-- Load the primary image -->
                                            <div class="<?php echo $slideshowID; ?>">
                                                <div class="slideProgress" style="align-content: center">
                                                    <p>
                                                        1 / <?php echo $fileCount; ?>
                                                    </p>
                                                </div>
                                                <img class="center roundAll" src="<?php echo $primaryImage; ?>"
                                                     alt="Image of the project">
                                            </div>

                                            <!--Load the next images -->
                                            <?php
                                            //Load the sub images
                                            $progress = 2;
                                            foreach (glob($directoryName . "/*") as $file) {
                                                //Don't show the primary image
                                                if ($file != $primaryImage && $fileCount > 1) {
                                                    ?>
                                                    <!-- Load the secondary image -->
                                                    <div class="<?php echo $slideshowID; ?>">
                                                        <div class="slideProgress">
                                                            <p><?php echo $progress . " / " . $fileCount; ?></p>
                                                        </div>
                                                        <img class="center roundAll" src="<?php echo $file; ?>"
                                                             alt="Image of the project">
                                                    </div>
                                                    <?php
                                                    //Increment the progress
                                                    $progress += 1;
                                                }
                                            }

                                            //Only show the next and previous buttons if there are images to be displayed
                                            if (!is_null($primaryImage) && $fileCount > 1) {
                                                ?>
                                                <!--navigation buttons for the sideshow -->
                                                <a class="prev" onclick="plusDivs(-1, <?php echo $count - 1; ?>)">&#10094;</a>
                                                <a class="next" onclick="plusDivs(+1, <?php echo $count - 1; ?>)">&#10095;</a>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?
                                }
                                ?>
                                <p style="padding-top: 30px"><?php echo $examplesDescription; ?></p>
                            </div>

                            <div class="updateDelete-container examplesUpdateDelete">
                                <div class="delete" id="deleteExample<?php echo $uniqueKey; ?>">
                                    <!--Allow the user to delete the record-->
                                    <form method="post" action="process.php">
                                        <input type="hidden" name="uniqueKey" value="<?php echo $uniqueKey; ?>">
                                        <button type="submit" class="deleteButton" name="deleteExample"
                                                style="padding: 0;">
                                            <img src="images/bin.png" class="binImage">
                                        </button>
                                    </form>
                                </div>
                                <div class="update">
                                    <button id="updateExample<?php echo $uniqueKey; ?>button" class="updateButton"
                                            onclick="showUpdateDiv('updateExample<?php echo $uniqueKey; ?>', '<?php echo $uniqueKey; ?>', 'Update', 'Hide', 'deleteExample<?php echo $uniqueKey; ?>')">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- The div that is used to edit the example-->
                        <div id="updateExample<?php echo $uniqueKey; ?>"
                             style="background-color: <?php echo $colour; ?>; display: none">
                            <!--Form for updating the examples-->
                            <form method="post" action="process.php" autocomplete="off">
                                <div class="addExampleContainer">
                                    <!--Project title header-->
                                    <div class="nameTitle" style="text-align: center">
                                        <h2>Project Name</h2>
                                    </div>

                                    <!--The project title-->
                                    <div class="addName">
                                        <div class="autocomplete">
                                            <input id="updateExampleName<?php echo $uniqueKey; ?>" type="text"
                                                   name="exampleName" value="<?php echo $name; ?>" required>
                                        </div>
                                    </div>

                                    <!--The year header-->
                                    <div class="yearTitleNewExample" style="text-align: center">
                                        <h2>Year</h2>
                                    </div>

                                    <!--The year-->
                                    <div class="addYear">
                                        <div class="autocomplete">
                                            <input id="updateExampleYear<?php echo $uniqueKey; ?>" class="textInput"
                                                   type="number"
                                                   name="exampleYear" value="<?php echo $relevantYear; ?>" required>
                                        </div>
                                    </div>

                                    <!--Links header-->
                                    <div class="linksTitle" style="text-align: center">
                                        <h2>Links</h2>
                                    </div>

                                    <!--The link-->
                                    <div class="addLink">
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
                                        <div style="text-align: center">
                                            <input onchange="showUpdateLinkInput('updateExamplesLink<?php echo $uniqueKey; ?>')"
                                                   name="updateLinkInput" class="checkbox" type="checkbox"
                                                   id="updateExamplesLinkCheckbox<?php echo $uniqueKey; ?>" <?php echo $checked; ?>>
                                            <label for="updateExamplesLinkCheckbox<?php echo $uniqueKey; ?>">Link</label>
                                        </div>

                                        <!--Div that shows the link-->
                                        <div id="updateExamplesLink<?php echo $uniqueKey; ?>"
                                             style="display: <?php echo $displayLinkDiv; ?>">
                                            <input type="text" name="updateLinkEntry"
                                                   value="<?php echo $linkToDisplay; ?>"
                                                   placeholder="<?php echo $placeholder; ?>">
                                        </div>
                                    </div>

                                    <!--Github-->
                                    <div class="addGithub">
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
                                        <div style="text-align: center">
                                            <input onchange="showUpdateLinkInput('updateGithubLink<?php echo $uniqueKey; ?>')"
                                                   name="updateGithubInput" class="checkbox" type="checkbox"
                                                   id="updateGithubLinkCheckbox<?php echo $uniqueKey; ?>" <?php echo $checked ?>>
                                            <label for="updateGithubLinkCheckbox<?php echo $uniqueKey; ?>">Github</label>
                                        </div>

                                        <!--The div that shows the github link-->
                                        <div id="updateGithubLink<?php echo $uniqueKey; ?>"
                                             style="display: <?php echo $displayLinkDiv; ?>">
                                            <input name="updateGithubEntry" type="text"
                                                   value="<?php echo $linkToDisplay; ?>"
                                                   placeholder="<?php echo $placeholder; ?>">
                                        </div>
                                    </div>

                                    <!--Languages header-->
                                    <div class="languagesTitle" style="text-align: center">
                                        <h2>Languages</h2>
                                    </div>

                                    <!--The languages-->
                                    <div class="addLanguages">
                                        <?php
                                        //Create a checkbox for each language
                                        for ($i = 0; $i < sizeof($languageArray); $i++) {
                                            //Checking if the language matches one of the languages used in the example
                                            $checked = "";
                                            if ($languageArray[$i] == $langOne || $languageArray[$i] == $langTwo || $languageArray[$i] == $langThree ||
                                                $languageArray[$i] == $langFour || $languageArray[$i] == $langFive) {
                                                $checked = "checked";
                                            }

                                            ?>
                                            <div>
                                                <input type="checkbox" id="<?php echo $languageArray[$i]; ?>"
                                                       name="<?php echo $languageArray[$i]; ?>" class="checkbox"
                                                       value="<?php echo $languageArray[$i]; ?>" <?php echo $checked; ?>>
                                                <label for="<?php echo $languageArray[$i]; ?>"><?php echo $languageArray[$i] ?></label>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>

                                    <!--Option that allows the user to add their own code-->
                                    <div class="addNewLanguage">
                                        <input type="checkbox" class="checkbox" name="updateLanguageInput"
                                               id="newLanguage<?php echo $uniqueKey; ?>"
                                               onchange="showUpdateLinkInput('newLanguageDiv<?php echo $uniqueKey; ?>')">
                                        <label for="newLanguage<?php echo $uniqueKey; ?>">Add Another Language</label>

                                        <!--Input box for the new language-->
                                        <div id="newLanguageDiv<?php echo $uniqueKey; ?>" style="display:none;">
                                            <input type="text" name="updateLanguageEntry" placeholder="New Language">
                                        </div>
                                    </div>

                                    <!--Description header-->
                                    <div class="descriptionTitle" style="text-align: center">
                                        <h2>Description</h2>
                                    </div>

                                    <!--The description-->
                                    <div class="addDescription">
                                        <textarea name="exampleDescription" style="width: 100%; height: auto"
                                                  required> <?php echo $examplesDescription; ?> </textarea>
                                    </div>

                                    <!--Images header-->
                                    <div class="imagesTitle" style="text-align: center;">
                                        <h2>Images</h2>
                                    </div>

                                    <!--Image input-->
                                    <div class="addImages">
                                        <!--The image gallery. Only display if there are images-->
                                        <?php
                                        if ($fileCount != 0) {
                                            ?>
                                            <button class="indexButton imageButton" type="button"
                                                    id="editImages<?php echo $uniqueKey; ?>button"
                                                    onclick="showUniqueElement('editImages<?php echo $uniqueKey; ?>', 'editImages<?php echo $uniqueKey; ?>button', 'Edit Images', 'Hide Edit Images');
                                                            hideSubmit('submit<?php echo $uniqueKey; ?>', 'submitTitle<?php echo $uniqueKey; ?>');
                                                            document.getElementById('hideAddImages<?php echo $uniqueKey; ?>')">
                                                Edit Images
                                            </button>
                                            <?php
                                        } else {
                                            ?>
                                            <div style="text-align: center;">
                                                <p>There are no images to be edited. Click the button below to add some.</p>
                                            </div>
                                            <button class="indexButton imageButton" type="button"
                                                    id="addImages<?php echo $uniqueKey; ?>button"
                                                    onclick="showUniqueElement('addImages<?php echo $uniqueKey; ?>', 'addImages<?php echo $uniqueKey; ?>button', 'Add Images', 'Hide Add Images');
                                                            hideSubmit('submit<?php echo $uniqueKey; ?>', 'submitTitle<?php echo $uniqueKey; ?>')">
                                                Add Images
                                            </button>
                                            <?php
                                        }
                                        ?>

                                    </div>

                                    <!--Submit header-->
                                    <div class="submitTitle" style="text-align: center"
                                         id="submitTitle<?php echo $uniqueKey; ?>">
                                        <h2>Submit</h2>
                                    </div>

                                    <!--The submit button-->
                                    <div class="submit" id="submit<?php echo $uniqueKey; ?>">
                                        <input type="hidden" name="uniqueKey" value="<?php echo $uniqueKey; ?>">
                                        <input type="submit" name="submitExampleUpdate" value="Update"
                                               class="updateButton">
                                    </div>
                                </div>
                            </form>
                            <!--Allow the user to add new images-->
                            <div id="addImages<?php echo $uniqueKey; ?>"
                                 style="display: none; width: 90%; margin-left: 5%; margin-right: 5%; padding-bottom: 20px;">
                                <!--Hide the add images-->
                                <button class="imageButton" type="button"
                                        id="hideAddImages<?php echo $uniqueKey; ?>button"
                                        onclick="hideElement('addImages<?php echo $uniqueKey; ?>')"
                                        style="display: none">
                                    Hide Add Images
                                </button>
                                <div style="text-align: center">
                                    <!--Form that allows the user to add images-->
                                    <p><strong>Images that are not 1:1 (width and height the same) will be
                                            cropped!</strong></p>
                                    Select image to upload:
                                    <form action="process.php" method="post" enctype="multipart/form-data">
                                        <div style="text-align: center;">
                                            <input type="file" name="updateImages[]" id="" multiple=""
                                                   style="background: none; margin: 0 auto; width: auto">
                                        </div>
                                        <input type="hidden" name="uniqueKey" value="<?php echo $uniqueKey; ?>">
                                        <input type="submit" name="addImages" value="Submit Images"
                                               class="updateButton">
                                    </form>
                                </div>
                            </div>

                            <!--Allow the user to update existing images-->
                            <div id="editImages<?php echo $uniqueKey; ?>" style="display: none; padding-bottom: 20px;">
                                <!--Option that allows the user to add some more images-->
                                <div style="text-align: center">
                                    <button class="indexButton imageButton" type="button"
                                            id="addImages<?php echo $uniqueKey; ?>button"
                                            onclick="showUniqueElement('addImages<?php echo $uniqueKey; ?>', 'addImages<?php echo $uniqueKey; ?>button', 'Add Images', 'Hide Add Images')">
                                        Add Images
                                    </button>
                                </div>
                                <!--Form that allows the user to add images-->
                                <div class="gallery-container roundAll">
                                    <?php
                                    foreach (glob($directoryName . "/*") as $file) {
                                        ?>
                                        <div class="deleteImageContainer">
                                            <div class="displayImage">
                                                <img src="<?php echo $file; ?>" style="width: 250px">
                                            </div>

                                            <div class="displayDelete">
                                                <form method="post" action="process.php">
                                                    <input name="file" type="hidden" value="<?php echo $file; ?>">
                                                    <input name="uniqueKey" type="hidden"
                                                           value="<?php echo $uniqueKey; ?>">
                                                    <input type="submit" name="deleteImage" value="Delete"
                                                           class="deleteButton">
                                                </form>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                    $experienceQuery->close();
                }
                ?>
            </div>
            <?php
        } else {
            ?>

            <div style="text-align: center; padding-top: 20px;">
                <p>These are your records, you can do whatever you like with them!</p>
            </div>
            <!--Button for adding new items-->
            <!-- Trigger/Open The Modal -->
            <br>
            <div style="text-align: center;">
                <button onclick="showPopup('popup'); loadNewEducationRecordAutocomplete(); loadNewExampleRecordAutocomplete()"
                        class="newItemButton">Create New Item
                </button>
            </div>
            <br>

            <!--The div that contains the education edit. Shown by default-->
            <div id="editEducation" style="display: block">
                <?php
                $educationArray = $_SESSION['playAroundEducation'];

                $recordCount = sizeof($educationArray);
                $count = 0;

                //Get the first institution
                $currentInstitution = null;

                //Display a message if there are no records returned
                if (is_null($educationArray) || ($educationArray[0] == "" || is_null($educationArray))) {
                    ?>
                    <div style="text-align: center">
                        <h1>Nothing to see here.</h1>
                    </div>
                    <?php
                } else {
                    for ($i = 0; $i < sizeof($educationArray); $i++) {
                        //Calculates if any rounding of the examples div is required
                        $classHeader = "";
                        $classContent = "";

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

                        //Set up the variables
                        $uniqueKey = $educationArray[$i][0];
                        $institution = $educationArray[$i][1];
                        $subject = $educationArray[$i][2];
                        $code = $educationArray[$i][3];
                        $codeExtension = $educationArray[$i][4];
                        $grade = $educationArray[$i][5];
                        $credits = $educationArray[$i][6];
                        $subjectLevel = $educationArray[$i][7];
                        $relevantYear = $educationArray[$i][8];

                        //Checking for a new institution
                        if ($institution != $currentInstitution) {
                            //Reset the institution
                            $currentInstitution = $institution;
                            ?>
                            <div style="background-color: #D3D3D3; text-align: center" class="<?php echo $classHeader; ?>">
                                <h1><?php echo $institution; ?></h1>

                                <!--Display the column titles-->
                                <div class="education-Titles-Large-Edit">
                                    <div>
                                        <p class="alignTextLeft">Update/Delete</p>
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
                                <div class="updateDelete-container educationUpdateDelete">
                                    <div class="delete" id="deleteEducation<?php echo $uniqueKey; ?>">
                                        <!--Show the delete button-->
                                        <form method="post" action="process.php">
                                            <input type="hidden" value="<?php echo $uniqueKey; ?>" name="uniqueKey">
                                            <?php
                                            //If it is the last record round the lower left corner
                                            $round = "";
                                            if ($count == $recordCount) {
                                                $round = "roundBottomLeft";
                                            }
                                            ?>
                                            <button type="submit" class="deleteButton <?php echo $round; ?>"
                                                    name="deleteEducationRecord"
                                                    style="padding: 0; --bgColour: <?php echo $colour; ?>">
                                                <img src="images/bin.png" class="binImage <?php echo $round; ?>">
                                            </button>
                                        </form>
                                    </div>
                                    <div class="update">
                                        <!--Show the update div-->
                                        <button id="updateEducation<?php echo $uniqueKey; ?>button" class="updateButton"
                                                onclick="showUpdateDiv('updateEducation<?php echo $uniqueKey; ?>', '<?php echo $uniqueKey; ?>', 'Update', 'Hide', 'deleteEducation<?php echo $uniqueKey; ?>');
                                                        loadAutocompleteForEducationUpdate(<?php echo $uniqueKey; ?>)">
                                            Update
                                        </button>
                                    </div>
                                </div>
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
                                    <p class="alignTextLeft"> <?php echo $subject; ?></p>
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
                        <div id="updateEducation<?php echo $uniqueKey; ?>"
                             style="display:none; background-color: <?php echo $colour; ?>">
                            <form autocomplete="off" method="post" action="process.php">
                                <div class="addEducationContainer">
                                    <!--Institution header-->
                                    <div class="institutionTitle" style="text-align: center;">
                                        <h2>Institution</h2>
                                    </div>

                                    <!--Institution-->
                                    <div class="addInstitution">
                                        <div class="add-Institution autocomplete">
                                            <input id="updateEducationInstitution<?php echo $uniqueKey; ?>"
                                                   class="textInput"
                                                   type="text" name="institution" value="<?php echo $institution; ?>"
                                                   required>
                                        </div>
                                    </div>

                                    <!--Subject level header-->
                                    <div class="levelTitle" style="text-align: center">
                                        <h2>Level</h2>
                                    </div>

                                    <!--Subject Level-->
                                    <div class="addLevel">
                                        <div class="add-Subject-Level autocomplete">
                                            <input id="updateEducationSubjectLevel<?php echo $uniqueKey; ?>"
                                                   class="textInput"
                                                   type="text" name="subjectLevel" value="<?php echo $subjectLevel; ?>"
                                                   required>
                                        </div>
                                    </div>

                                    <!--Year header-->
                                    <div class="yearTitleNewEducation" style="text-align: center">
                                        <h2>Year</h2>
                                    </div>

                                    <!--The year-->
                                    <div class="addYear">
                                        <div class="add-Subject-Year autocomplete">
                                            <input id="updateEducationYear<?php echo $uniqueKey; ?>" class="textInput"
                                                   type="number" name="subjectYear" value="<?php echo $relevantYear; ?>"
                                                   required>
                                        </div>
                                    </div>

                                    <!--Subject header-->
                                    <div class="subjectTitleNew" style="text-align: center">
                                        <h2>Subject</h2>
                                    </div>

                                    <!--The subject-->
                                    <div class="addSubject">
                                        <div class="add-Subject autocomplete">
                                            <input id="updateEducationSubject<?php echo $uniqueKey; ?>"
                                                   class="textInput"
                                                   type="text" name="subject" value="<?php echo $subject; ?>" required>
                                        </div>
                                    </div>

                                    <!--Code header-->
                                    <div class="codeTitleNew" style="text-align: center;">
                                        <h2>Code</h2>
                                    </div>

                                    <!--The code-->
                                    <div class="addCode">
                                        <div class="add-Code autocomplete">
                                            <input id="updateEducationCode<?php echo $uniqueKey; ?>" class="textInput"
                                                   type="text" name="code" value="<?php echo $code; ?>" required>
                                        </div>
                                    </div>

                                    <!--Code extension header-->
                                    <div class="codeExtensionTitle" style="text-align: center">
                                        <h2>Code Extension</h2>
                                    </div>

                                    <!--Code extension-->
                                    <div class="addCodeExtension">
                                        <div class="add-Code-Extension autocomplete">
                                            <input id="updateEducationCodeExtension<?php echo $uniqueKey; ?>"
                                                   class="textInput"
                                                   type="text" name="codeExtension"
                                                   value="<?php echo $codeExtension; ?> " required>
                                        </div>
                                    </div>

                                    <!--Grade type header-->
                                    <div class="gradeTypeTitle" style="text-align: center">
                                        <h2>Grade Type</h2>
                                    </div>

                                    <!--Grade type-->
                                    <div class="addGradeType">
                                        <div class="add-Grade">
                                            <!--Allow the user to select the type of grade. Set to the current grade type by default-->
                                            <select onchange="showCreditsGpa(this.value)" class="textInput">
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

                                        </div>
                                    </div>

                                    <!--Grade header-->
                                    <div class="gradeTitleNew" style="text-align: center">
                                        <h2>Grade/Credits</h2>
                                    </div>

                                    <!--Grade-->
                                    <div class="addGrade">
                                        <div id="showUpdateEducationCreditsDiv<?php echo $uniqueKey; ?>"
                                             style="display: <?php echo $displayDivCredits; ?>">
                                            <input id="updateEducationCredits<?php echo $uniqueKey; ?>" class=textInput"
                                                   type="number" name="credits" placeholder="<?php echo $credits; ?>">
                                            <?php $isNumeric = false; ?>
                                        </div>
                                        <div id="showUpdateEducationGpaDiv<?php echo $uniqueKey; ?>"
                                             style="display: <?php echo $displayDivGpa; ?>">
                                            <div class="autocomplete">
                                                <input id="updateEducationGrade<?php echo $uniqueKey; ?>"
                                                       class=textInput"
                                                       type="text" name="gpa" placeholder="<?php echo $grade; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!--Submit header-->
                                    <div class="submitTitleNew" style="text-align: center">
                                        <h2>Submit</h2>
                                    </div>

                                    <!--Submit-->
                                    <div class="submit">
                                        <div class="save-Record">
                                            <input name="uniqueKey" value="<?php echo $uniqueKey; ?>" type="hidden">
                                            <input name="submitEducationUpdate" value="Submit" type="submit"
                                                   class="textInput updateButton">
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </form>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <!--The div that contains the projects edit-->
            <div id="editProjects" style="display: none">
                <?php

                $examplesArray = $_SESSION['playAroundExamples'];

                $count = 0;

                //Display a message if there are no records returned
                if ($recordCount == 0) {
                    ?>
                    <div style="text-align: center">
                        <h1>Nothing to see here.</h1>
                    </div>
                    <?php
                } else {
                    for ($i = 0; $i < sizeof($examplesArray); $i++) {
                        //Get the variables
                        $uniqueKey = $examplesArray[$i][0];
                        $name = $examplesArray[$i][2];
                        $link = $examplesArray[$i][4];
                        $github = $examplesArray[$i][5];
                        $relevantYear = $examplesArray[$i][7];
                        $examplesDescription = $examplesArray[$i][6];

                        //Check if there are images
                        if ($examplesArray[$i][1] == "" || is_null($examplesArray[$i][1]) || empty($examplesArray[$i][1])) {
                            $primaryImage = null;
                        } else {
                            $primaryImage = $examplesArray[$i][1][0];
                            $fileCount = sizeof($examplesArray[$i][1]);
                        }

                        //Calculates if any rounding of the examples div is required
                        $class = "";
                        if ($count == $recordCount - 1) {
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

                        //Create the slideshow id
                        $slideshowID = "ssID" . $count; ?>

                        <div style="background-color: <?php echo $colour; ?>;" class="<?php echo $class; ?>">
                            <div class="examples-name">
                                <h1><?php echo $name; ?></h1>
                            </div>
                            <div class="examples-year">
                                <p class="alignTextLeft">Year:<?php echo $relevantYear; ?></p>
                            </div>
                            <div class="examples-language">
                                <?php
                                $languages = "";

                                //Append the languages
                                for ($j = 0; $j < sizeof($examplesArray[$i][3]); $j++) {
                                    $languages = $languages . $examplesArray[$i][3][$j];

                                    if ($j != sizeof($examplesArray[$i][3]) - 1) {
                                        $languages = $languages . ", ";
                                    }
                                }
                                ?>
                                <p class="alignTextLeft">Languages: <?php echo $languages;?></p>
                            </div>
                            <div class="examples-link">
                                <p class="alignTextLeft">
                                    <?php
                                    //Only displays if there is a link to display
                                    if ($link != null) {
                                        ?>
                                        Link:
                                        <a class="pageLink" href="<?php echo $link; ?>"><?php echo $link; ?></a>
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
                                        <a class="pageLink" href="<?php echo $github; ?>"><?php echo $github; ?> </a>
                                        <?php
                                    } else {
                                        echo "Sorry, there is no github link.";
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="examples-description alignTextLeft">
                                <?php
                                //Only display images if there are images to display
                                if (!is_null($primaryImage)) {
                                    ?>
                                    <div style="padding-right: 10px; float: left">
                                        <div class="slideshowContainer" style="--width: 250px">
                                            <!-- Load the primary image -->
                                            <div class="<?php echo $slideshowID; ?>">
                                                <div class="slideProgress" style="align-content: center">
                                                    <p>
                                                        1 / <?php echo $fileCount; ?>
                                                    </p>
                                                </div>
                                                <img class="center roundAll" src="<?php echo $primaryImage; ?>"
                                                     alt="Image of the project">
                                            </div>

                                            <!--Load the next images -->
                                            <?php
                                            //Load the sub images
                                            $progress = 2;
                                            for ($j = 1; $j < sizeof($examplesArray[$i][1]); $j++) {
                                                //Don't show the primary image
                                                if ($examplesArray[$i][1][$j] != $primaryImage && $fileCount > 1) {
                                                    ?>
                                                    <!-- Load the secondary image -->
                                                    <div class="<?php echo $slideshowID; ?>">
                                                        <div class="slideProgress">
                                                            <p><?php echo $progress . " / " . $fileCount; ?></p>
                                                        </div>
                                                        <img class="center roundAll" src="<?php echo $examplesArray[$i][1][$j];?>"
                                                             alt="Image of the project">
                                                    </div>
                                                    <?php
                                                    //Increment the progress
                                                    $progress += 1;
                                                }
                                            }

                                            //Only show the next and previous buttons if there are images to be displayed
                                            if (!is_null($primaryImage) && $fileCount > 1) {
                                                ?>
                                                <!--navigation buttons for the sideshow -->
                                                <a class="prev" onclick="plusDivs(-1, <?php echo $count - 1; ?>)">&#10094;</a>
                                                <a class="next" onclick="plusDivs(+1, <?php echo $count - 1; ?>)">&#10095;</a>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?
                                }
                                ?>
                                <p style="padding-top: 30px"><?php echo $examplesDescription; ?></p>
                            </div>
                            <div class="updateDelete-container examplesUpdateDelete" style="margin-top: 10px;">
                                <div class="delete" id="deleteExample<?php echo $uniqueKey; ?>">
                                    <!--Allow the user to delete the record-->
                                    <form method="post" action="process.php">
                                        <input type="hidden" name="uniqueKey" value="<?php echo $uniqueKey; ?>">
                                        <button type="submit" class="deleteButton" name="deleteExample"
                                                style="padding: 0;">
                                            <img src="images/bin.png" class="binImage">
                                        </button>
                                    </form>
                                </div>
                                <div class="update">
                                    <button id="updateExample<?php echo $uniqueKey; ?>button" class="updateButton"
                                            onclick="showUpdateDiv('updateExample<?php echo $uniqueKey; ?>', '<?php echo $uniqueKey; ?>', 'Update', 'Hide', 'deleteExample<?php echo $uniqueKey; ?>')">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- The div that is used to edit the example-->
                        <div id="updateExample<?php echo $uniqueKey; ?>"style="background-color: <?php echo $colour; ?>; display: none">
                            <!--Form for updating the examples-->
                            <form method="post" action="process.php" autocomplete="off">
                                <div class="addExampleContainer">
                                    <!--Project title header-->
                                    <div class="nameTitle" style="text-align: center">
                                        <h2>Project Name</h2>
                                    </div>

                                    <!--The project title-->
                                    <div class="addName">
                                        <div class="autocomplete">
                                            <input id="updateExampleName<?php echo $uniqueKey; ?>" type="text"
                                                   name="exampleName" value="<?php echo $name; ?>" required>
                                        </div>
                                    </div>

                                    <!--The year header-->
                                    <div class="yearTitleNewExample" style="text-align: center">
                                        <h2>Year</h2>
                                    </div>

                                    <!--The year-->
                                    <div class="addYear">
                                        <div class="autocomplete">
                                            <input id="updateExampleYear<?php echo $uniqueKey; ?>" class="textInput"
                                                   type="number"
                                                   name="exampleYear" value="<?php echo $relevantYear; ?>" required>
                                        </div>
                                    </div>

                                    <!--Links header-->
                                    <div class="linksTitle" style="text-align: center">
                                        <h2>Links</h2>
                                    </div>

                                    <!--The link-->
                                    <div class="addLink">
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
                                        <div style="text-align: center">
                                            <input onchange="showUpdateLinkInput('updateExamplesLink<?php echo $uniqueKey; ?>')"
                                                   name="updateLinkInput" class="checkbox" type="checkbox"
                                                   id="updateExamplesLinkCheckbox<?php echo $uniqueKey; ?>" <?php echo $checked; ?>>
                                            <label for="updateExamplesLinkCheckbox<?php echo $uniqueKey; ?>">Link</label>
                                        </div>

                                        <!--Div that shows the link-->
                                        <div id="updateExamplesLink<?php echo $uniqueKey; ?>"
                                             style="display: <?php echo $displayLinkDiv; ?>">
                                            <input type="text" name="updateLinkEntry"
                                                   value="<?php echo $linkToDisplay; ?>"
                                                   placeholder="<?php echo $placeholder; ?>">
                                        </div>
                                    </div>

                                    <!--Github-->
                                    <div class="addGithub">
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
                                        <div style="text-align: center">
                                            <input onchange="showUpdateLinkInput('updateGithubLink<?php echo $uniqueKey; ?>')"
                                                   name="updateGithubInput" class="checkbox" type="checkbox"
                                                   id="updateGithubLinkCheckbox<?php echo $uniqueKey; ?>" <?php echo $checked ?>>
                                            <label for="updateGithubLinkCheckbox<?php echo $uniqueKey; ?>">Github</label>
                                        </div>

                                        <!--The div that shows the github link-->
                                        <div id="updateGithubLink<?php echo $uniqueKey; ?>"
                                             style="display: <?php echo $displayLinkDiv; ?>">
                                            <input name="updateGithubEntry" type="text"
                                                   value="<?php echo $linkToDisplay; ?>"
                                                   placeholder="<?php echo $placeholder; ?>">
                                        </div>
                                    </div>

                                    <!--Languages header-->
                                    <div class="languagesTitle" style="text-align: center">
                                        <h2>Languages</h2>
                                    </div>

                                    <!--The languages-->
                                    <div class="addLanguages">
                                        <?php
                                        //Create a checkbox for each language
                                        foreach ($languageArray as $language) {
                                            $checked = "";
                                            if (in_array($language, $examplesArray[$i][3])) {
                                                $checked = "checked";
                                            }

                                            ?>
                                            <div>
                                                <input type="checkbox" id="<?php echo $language; ?>"
                                                       name="<?php echo $language; ?>" class="checkbox"
                                                       value="<?php echo $language; ?>" <?php echo $checked; ?>>
                                                <label for="<?php echo $language; ?>"><?php echo $language;?></label>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>

                                    <!--Option that allows the user to add their own code-->
                                    <div class="addNewLanguage">
                                        <input type="checkbox" class="checkbox" name="updateLanguageInput"
                                               id="newLanguage<?php echo $uniqueKey; ?>"
                                               onchange="showUpdateLinkInput('newLanguageDiv<?php echo $uniqueKey; ?>')">
                                        <label for="newLanguage<?php echo $uniqueKey; ?>">Add Another Language</label>

                                        <!--Input box for the new language-->
                                        <div id="newLanguageDiv<?php echo $uniqueKey; ?>" style="display:none;">
                                            <input type="text" name="updateLanguageEntry" placeholder="New Language">
                                        </div>
                                    </div>

                                    <!--Description header-->
                                    <div class="descriptionTitle" style="text-align: center">
                                        <h2>Description</h2>
                                    </div>

                                    <!--The description-->
                                    <div class="addDescription">
                                        <textarea name="exampleDescription" style="width: 100%; height: auto"
                                                  required> <?php echo $examplesDescription; ?> </textarea>
                                    </div>

                                    <!--Images header-->
                                    <div class="imagesTitle" style="text-align: center;">
                                        <h2>Images</h2>
                                    </div>

                                    <!--Image input-->
                                    <div class="addImages">
                                        <!--The image gallery. Only display if there are images-->
                                        <?php
                                        if ($fileCount != 0) {
                                            ?>
                                            <button class="indexButton imageButton" type="button"
                                                    id="editImages<?php echo $uniqueKey; ?>button"
                                                    onclick="showUniqueElement('editImages<?php echo $uniqueKey; ?>', 'editImages<?php echo $uniqueKey; ?>button', 'Edit Images', 'Hide Edit Images');
                                                            hideSubmit('submit<?php echo $uniqueKey; ?>', 'submitTitle<?php echo $uniqueKey; ?>');
                                                            document.getElementById('hideAddImages<?php echo $uniqueKey; ?>')">
                                                Edit Images
                                            </button>
                                            <?php
                                        } else {
                                            ?>
                                            <div style="text-align: center;">
                                                <p>There are no images to be edited. Click the button below to add some.</p>
                                            </div>
                                            <button class="indexButton imageButton" type="button"
                                                    id="addImages<?php echo $uniqueKey; ?>button"
                                                    onclick="showUniqueElement('addImages<?php echo $uniqueKey; ?>', 'addImages<?php echo $uniqueKey; ?>button', 'Add Images', 'Hide Add Images');
                                                            hideSubmit('submit<?php echo $uniqueKey; ?>', 'submitTitle<?php echo $uniqueKey; ?>')">
                                                Add Images
                                            </button>
                                            <?php
                                        }
                                        ?>

                                    </div>

                                    <!--Submit header-->
                                    <div class="submitTitle" style="text-align: center"
                                         id="submitTitle<?php echo $uniqueKey; ?>">
                                        <h2>Submit</h2>
                                    </div>

                                    <!--The submit button-->
                                    <div class="submit" id="submit<?php echo $uniqueKey; ?>">
                                        <input type="hidden" name="uniqueKey" value="<?php echo $uniqueKey; ?>">
                                        <input type="submit" name="submitExampleUpdate" value="Update"
                                               class="updateButton">
                                    </div>
                                </div>
                            </form>
                            <!--Allow the user to add new images-->
                            <div id="addImages<?php echo $uniqueKey; ?>"
                                 style="display: none; width: 90%; margin-left: 5%; margin-right: 5%; padding-bottom: 20px;">
                                <!--Hide the add images-->
                                <button class="imageButton" type="button"
                                        id="hideAddImages<?php echo $uniqueKey; ?>button"
                                        onclick="hideElement('addImages<?php echo $uniqueKey; ?>')"
                                        style="display: none">
                                    Hide Add Images
                                </button>
                                <div style="text-align: center">
                                    <!--Form that allows the user to add images to their current selection-->
                                    <form action="process.php" method="post" enctype="multipart/form-data">
                                        <div class="gallery-container roundAll">
                                            <?php
                                            foreach ($imageArray as $image) {
                                                //only show the image if it is not already being used
                                                if (!in_array($image, $examplesArray[$i][1])) {
                                                    ?>
                                                    <div class="deleteImageContainer">
                                                        <div class="displayImage">
                                                            <img src="<?php echo $image;?>">
                                                        </div>
                                                        <div class="displayDelete" style="height: 30px; margin-bottom: 20px;">
                                                            <input type="checkbox" value="<?php echo $image;?>" name = "<?php echo $image;?>" id = "<?php echo $uniqueKey . $image;?>">
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <input type="hidden" name="uniqueKey" value="<?php echo $uniqueKey; ?>">
                                        <input type="submit" name="addImages" value="Submit Images" class="updateButton">
                                    </form>
                                </div>
                            </div>

                            <!--Allow the user to update existing images-->
                            <div id="editImages<?php echo $uniqueKey; ?>" style="display: none; padding-bottom: 20px;">
                                <!--Option that allows the user to add some more images-->
                                <div style="text-align: center">
                                    <button class="indexButton imageButton" type="button"
                                            id="addImages<?php echo $uniqueKey; ?>button"
                                            onclick="showUniqueElement('addImages<?php echo $uniqueKey; ?>', 'addImages<?php echo $uniqueKey; ?>button', 'Add Images', 'Hide Add Images')">
                                        Add Images
                                    </button>
                                </div>
                                <!--Form that allows the user to add images-->
                                <div class="gallery-container roundAll">
                                    <?php
                                    foreach ($examplesArray[$i][1] as $file) {
                                        ?>
                                        <div class="deleteImageContainer">
                                            <div class="displayImage">
                                                <img src="<?php echo $file; ?>" style="width: 250px">
                                            </div>

                                            <div class="displayDelete">
                                                <form method="post" action="process.php">
                                                    <input name="file" type="hidden" value="<?php echo $file; ?>">
                                                    <input name="uniqueKey" type="hidden"
                                                           value="<?php echo $uniqueKey; ?>">
                                                    <input type="submit" name="deleteImage" value="Delete"
                                                           class="deleteButton">
                                                </form>
                                            </div>
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
            <?php
        }
        ?>
    </div>
</div>
</body>

<div id="popup" class="popup-container roundAll" style="display: none; margin-bottom: 50px;">
    <div class="popup-close">
        <span onclick="hidePopup('popup')" class="closeEditAddModal">&times;</span>
    </div>
    <div class="popup-content roundAll" style="background-color: #eee">
        <!--Load tabs based on the record being entered-->
        <div class="edit-tabs">
            <div class="Education">
                <!--Make button grey by default-->
                <button id="addEducationTab" class="indexButton"
                        style="display: block; border: none; border-radius: 20px 0 0 0; background-color: #eee"
                        onclick="showElement('addEducation')">Education
                </button>
            </div>
            <div class="Projects">
                <button id="addProjectTab" class="indexButton"
                        style="display: block; border: none; border-radius: 0 20px 0 0; background-color: #d3d3d3"
                        onclick="showElement('addProject')">Projects
                </button>
            </div>
        </div>
        <br>
        <div id="addEducation" style="display: block">
            <form autocomplete="off" method="post" action="process.php">
                <div class="addEducationContainer">

                    <!--Institution header-->
                    <div class="institutionTitle" style="text-align: center;">
                        <h2>Institution</h2>
                    </div>

                    <!--Institution-->
                    <div class="addInstitution">
                        <div class="autocomplete">
                            <input id="newEducationRecordInstitution" class="textInput" type="text"
                                   name="newInstitution" placeholder="Institution: e.g. Tawa College" required>
                        </div>
                    </div>

                    <!--Level header-->
                    <div class="levelTitle" style="text-align: center;">
                        <h2>Level</h2>
                    </div>

                    <!--Level-->
                    <div class="addLevel">
                        <div class="autocomplete">
                            <input id="newEducationRecordSubjectLevel" class="textInput" type="text"
                                   name="newSubjectLevel" placeholder="Level: e.g. NCEA Level One" required>
                        </div>
                    </div>

                    <!--Year header-->
                    <div class="yearTitleNewEducation" style="text-align: center;">
                        <h2>Year</h2>
                    </div>

                    <!--Year-->
                    <div class="addYear">
                        <div class="autocomplete">
                            <input id="newEducationRecordYear" class="textInput" type="number" name="newSubjectYear"
                                   placeholder="Year: e.g. <?php echo date('Y'); ?>" required>
                        </div>
                    </div>

                    <!--Subject header-->
                    <div class="subjectTitleNew" style="text-align: center;">
                        <h2>Subject</h2>
                    </div>

                    <!--Subject-->
                    <div class="addSubject">
                        <div class="autocomplete">
                            <input id="newEducationRecordSubject" class="textInput" type="text" name="newSubject"
                                   placeholder="Subject: e.g. Science" " required>
                        </div>
                    </div>

                    <!--Code header-->
                    <div class="codeTitleNew" style="text-align: center;">
                        <h2>Code</h2>
                    </div>

                    <!--Code-->
                    <div class="addCode">
                        <div class="autocomplete">
                            <input id="newEducationRecordCode" class="textInput" type="text" name="newCode"
                                   placeholder="Code: e.g. COMP" required>
                        </div>
                    </div>

                    <!--Code extension header-->
                    <div class="codeExtensionTitle" style="text-align: center;">
                        <h2>Code Extension</h2>
                    </div>

                    <!--Code extension-->
                    <div class="addCodeExtension">
                        <div class="autocomplete">
                            <input id="newEducationRecordCodeExtension" class="textInput" type="text"
                                   name="newCodeExtension" placeholder="Code Extension: e.g. 101" required>
                        </div>
                    </div>

                    <!--Grade type header-->
                    <div class="gradeTypeTitle" style="text-align: center;">
                        <h2>Grade Type</h2>
                    </div>

                    <!--Grade Type-->
                    <div class="addGradeType">
                        <!--Change the input type based on the type of grade the user wishes to enter-->
                        <select onchange="showCreditsGpa(this.value)" class="textInput">
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
                    </div>

                    <!--Grade header-->
                    <div class="gradeTitleNew" style="text-align: center">
                        <h2>Grade/Credits</h2>
                    </div>

                    <!--Grade-->
                    <div class="addGrade">
                        <div id="showNewCreditsDiv" style="display: <?php echo $displayDivCredits; ?>">
                            <input id="newEducationRecordCredits" class=textInput" type="number" name="newCredits"
                                   placeholder="Credits: e.g. 22">
                            <?php
                            $isNumeric = false;
                            ?>
                        </div>
                        <div id="showNewGpaDiv" style="display: <?php echo $displayDivGpa; ?>">
                            <div class="autocomplete">
                                <input id="newEducationRecordGpa" class=textInput" type="text" name="newGpa"
                                       placeholder="Gpa: e.g. A-">
                            </div>
                        </div>
                    </div>

                    <!--Submit header-->
                    <div class="submitTitleNew" style="text-align: center;">
                        <h2>Submit (duh)</h2>
                    </div>

                    <!--Submit-->
                    <div class="submit">
                        <input name="newEducationRecord" value="Submit Record" type="submit"
                               class="textInput updateButton">
                    </div>
                </div>
            </form>

        </div>
        <div id="addProject" style="display: none">
            <form action="process.php" method="post" enctype="multipart/form-data">
                <div class="addExampleContainer">
                    <!--Name header-->
                    <div class="nameTitle" style="text-align: center;">
                        <h2>Project Name</h2>
                    </div>

                    <!--Year header-->
                    <div class="yearTitleNewExample" style="text-align: center">
                        <h2>Year</h2>
                    </div>

                    <!--The project title-->
                    <div class="addName">
                        <div class="autocomplete">
                            <input id="newExampleName" type="text" name="newExampleName"
                                   placeholder="Project Name: E.g. Tarzan" required>
                        </div>
                    </div>

                    <!--The year-->
                    <div class="addYear">
                        <div class="autocomplete">
                            <input id="newExampleYear" type="number"
                                   name="newExampleYear" placeholder="Year: E.g. <?php echo date('Y'); ?>" required>
                        </div>
                    </div>

                    <!--Links header-->
                    <div class="linksTitle" style="text-align: center">
                        <h2>
                            Links
                        </h2>
                    </div>

                    <!--The link-->
                    <div class="addLink">
                        <div style="text-align: center">
                            <input onchange="showUpdateLinkInput('newExamplesLink')" name="newLinkInput"
                                   type="checkbox" id="newExamplesLinkCheckbox" class="checkbox">
                            <label for="newExamplesLinkCheckbox" style="word-wrap: break-word;">Link </label>
                        </div>

                        <!--Div that shows the link-->
                        <div id="newExamplesLink" style="display: none">
                            <input type="text" name="newLinkEntry" placeholder="E.g. google.com">
                        </div>
                    </div>

                    <!--Github-->
                    <div class="addGithub">
                        <div style="text-align: center">
                            <input onchange="showUpdateLinkInput('newGithubLink')" type="checkbox"
                                   id="newGithubLinkCheckbox" name="newGithubInput" class="checkbox">
                            <label for="newGithubLinkCheckbox">Github</label>
                        </div>

                        <!--The div that shows the github link-->
                        <div id="newGithubLink" style="display: none">
                            <input name="newGithubEntry" type="text" placeholder="E.g. github.com">
                        </div>
                    </div>

                    <!--Languages header-->
                    <div class="languagesTitle" style="text-align: center">
                        <h2>
                            Languages
                        </h2>
                    </div>

                    <!--The languages-->
                    <div class="addLanguages">
                        <?php
                        //Get languages from the database
                        if ($loggedIn == true) {
                            for ($i = 0; $i < sizeof($languageArrayDB); $i++) {
                                ?>
                                <div>
                                    <input type="checkbox" id="<?php echo $languageArrayDB[$i]; ?>" class="checkbox" name="<?php echo $languageArrayDB[$i]; ?>" value="<?php echo $languageArrayDB[$i]; ?>">
                                    <label for="<?php echo $languageArrayDB[$i]; ?>"><?php echo $languageArrayDB[$i] ?></label>
                                </div>
                                <?php
                            }
                        } else {
                            //Create a checkbox for each language
                            for ($i = 0; $i < sizeof($languageArray); $i++) {
                                ?>
                                <div>
                                    <input type="checkbox" id="<?php echo $languageArray[$i]; ?>" class="checkbox"
                                           name="<?php echo $languageArray[$i]; ?>"
                                           value="<?php echo $languageArray[$i]; ?>">
                                    <label for="<?php echo $languageArray[$i]; ?>"><?php echo $languageArray[$i] ?></label>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>

                    <!--Option that allows the user to add their own code-->
                    <div class="addNewLanguage">
                        <input class="checkbox" type="checkbox" name="newLanguageInput" id="newLanguageInput"
                               onchange="showUpdateLinkInput('newLanguageInputDiv')">
                        <label for="newLanguageInput">Add a new language</label>

                        <!--Input box for the new language-->
                        <div id="newLanguageInputDiv" style="display:none;">
                            <input type="text" name="newLanguageEntry" placeholder="New Language">
                        </div>
                    </div>

                    <!--Description Header-->
                    <div class="descriptionTitle" style="text-align: center">
                        <h2>Description</h2>
                    </div>

                    <!--Description-->
                    <div class="addDescription">
                            <textarea name="newExampleDescription" style="width: 100%" placeholder="Enter a description"
                                      required></textarea>
                    </div>

                    <!--Images header-->
                    <div class="imagesTitle" style="text-align: center;">
                        <h2>Images</h2>
                    </div>

                    <!--Image input-->
                    <div class="addImages">
                        <?php
                            //Allow image upload if logged in
                            if ($loggedIn == true) {
                                ?>
                                <!--Image input-->
                                <div class="addImages">
                                    <div style="text-align: center">
                                        <p><strong>Images that are not 1:1 (width and height the same) will be cropped!</strong></p>
                                        <input type="file" name="addImages[]" id="" multiple="" style="width: 100%">
                                    </div>
                                </div>
                                <?php
                            } else {
                                    //Give the user a selection of images
                                    ?>
                                    <!--Allow the user to select which images they want-->
                                    <div class="gallery-container">
                                        <?php
                                        //Loop through all of the possible images
                                        foreach ($imageArray as $image) {
                                        ?>
                                        <div class="deleteImageContainer">
                                        <div class="displayImage">
                                            <img src="<?php echo $image;?>">
                                        </div>
                                        <div class="displayDelete">
                                            <input style="height: 40px;" type="checkbox" name="<?php echo $image;?>" value = "<?php echo $image;?>">
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                </div>
                                <?php
                            }
                        ?>
                    </div>

                    <!--Submit header-->
                    <div class="submitTitle" style="text-align: center;">
                        <h2>Submit (obviously)</h2>
                    </div>

                    <div class="submit">
                        <input type="submit" value="Submit" name="newExampleRecord" class="updateButton"
                               style="width: 100%; margin: 0 auto;">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

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
            slideIndex[no] = x.length;
        }
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        try {
            x[slideIndex[no] - 1].style.display = "block";
        } catch (e) {
            //
        }
    }

    //Shows and hides the relevant add image modal
    function showAddImageModal(key) {
        // Get the modal
        var modal = document.getElementById("addImageModal" + key);

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("closeAddImageModal" + key)[0];

        modal.style.display = "grid";

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

    }

    //Shows and hides the relevant edit image modal
    function showEditImageModal(key) {
        // Get the modal
        var modal = document.getElementById("editImageModal" + key);

        // Get the <span> element that closes the modal
        // var span = document.getElementsByClassName("closeEditImageModal" + key)[0];

        modal.style.display = "block";

        // When the user clicks on <span> (x), close the modal
        // span.onclick = function() {
        //     modal.style.display = "none";
        // }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
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
    function showUpdateDiv(divName, key, showText, hideText, deleteID) {
        var x = document.getElementById(divName);

        //The element id hidden. Display it
        if (x.style.display === "none") {
            x.style.display = "block";

            //hide delete button
            document.getElementById(deleteID).style.display = "none";

            //Change the button text to hide
            updateButton(divName + 'button', hideText)
        } else {
            x.style.display = "none";
            updateButton(divName + 'button', showText);

            //show delete button
            document.getElementById(deleteID).style.display = "block";
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

    //Hides the appropriate submit button and title
    function hideSubmit(submitId, titleId) {
        //If the button is already hidden call a function to show it
        if (document.getElementById(submitId).style.display != "none") {
            document.getElementById(submitId).style.display = "none";
            document.getElementById(titleId).style.display = "none";
        } else {
            showSubmit(submitId, titleId);
        }
    }

    //Shows the appropriate submit button
    function showSubmit(submitId, titleId) {
        document.getElementById(submitId).style.display = "block";
        document.getElementById(titleId).style.display = "block";
    }

    //Shows a single occurrence of an element that may occur several times on the page
    function showUniqueElement(element, button, showMsg, hideMsg) {
        //If the element is not visible
        if (document.getElementById(element).style.display == "none") {
            document.getElementById(element).style.display = "block";
            updateButton(button, hideMsg);
        } else {
            //Hide the element
            document.getElementById(element).style.display = "none";
            updateButton(button, showMsg);
        }

    }

    //Show elements that do not occur more than once on the page
    function showElement(show) {
        //Show the relevant element
        if (show === "addEducation") {
            document.getElementById("addProjectTab").style.backgroundColor = '#d3d3d3';
            document.getElementById("addEducationTab").style.backgroundColor = '#eee';
            hideElement('addProject');
        } else if (show === 'addProject') {
            document.getElementById("addEducationTab").style.backgroundColor = '#d3d3d3';
            document.getElementById("addProjectTab").style.backgroundColor = '#eee';
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

    //hide the popup if required
    function hidePopup(popupElement) {
        //hide the popup
        document.getElementById(popupElement).style.display = "none";

        //restore the opacity
        document.getElementById("pageGrid").style.opacity = "100%";
    }

    function showPopup(popupElement) {
        document.getElementById("pageGrid").style.opacity = "30%";

        document.getElementById(popupElement).style.display = "grid";

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
        the text field element and an array of possible auto completed values:*/
        var currentFocus;
        /*execute a function when someone writes in the text field:*/
        inp.addEventListener("input", function (e) {
            var a, b, i, val = this.value;
            /*close any already open lists of auto completed values*/
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
        autocomplete(document.getElementById("newExampleName"), exampleNames);
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
        autocomplete(document.getElementById("updateExampleName" + id), exampleNames);
        autocomplete(document.getElementById("updateExampleYear" + id), years);
    }

    //Convert array to string
    function convertArrayToString(array) {
        for (var i = 0; i < array.length; i++) {
            array[i] = String(array[i]);
        }
        return array;
    }

    //Set the error/success message timeout
    $(document).ready(function () {
        $('.alert').delay(10000).fadeOut(300);
    });
</script>
<!--Called last so that it renders at the top-->
<?php
require("header.php");;
//Pull information from the footer page
require("footer.php");

//Print error/success messages
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
