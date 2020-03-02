<?php
require("head.php");
require("connect.php");
require("functions.php");

//Check to see if the playAroundEducation session exists and the user is not logged in
if (!isset($_SESSION['playAroundEducation'])) {
    //Create some default records
    setupEducationSession();
}
if (isset($_SESSION['playAroundEducation'])) {
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

/**
 * @param $count
 * @param $recordCount
 * @return array
 */
function calculateClassHeader($count, $recordCount)
{
    //Calculates if any rounding of the examples div is required
    if ($count == 0) {
        $classHeader = "roundTop";
    } else {
        $classHeader = "";
    }

    if ($count == $recordCount - 1) {
        $classContent = "education-grid-container roundBottom";
    } else {
        $classContent = "education-grid-container";
    }
    return array($classHeader, $classContent);
}

?>
<html lang="English">
<body class="background-img">
<div class="page-grid-container">

    <!--Display a message to the user-->
    <div class="roundAll editMessage">
        <p>These are your records. You can edit them by clicking on the play around tab!</p>
        <button id="showUserEducation" style="display: block; height: auto; padding: 0;" class="hidePrivacy" onclick="showElementWithButton('userEducation', 'showUserEducation', 'Show me what I can mess with.', 'Hide the stuff that I can mess around with.')">
            Show me what I can play with.
        </button>
    </div>
    <div id="userEducation" style="display: none; margin-bottom: 20px;">
        <!--Show the records that the user can edit-->
        <?php
        //Setting a variable so that the institution is only printed once
        $currentInstitution = '';
        $educationArray = $_SESSION['playAroundEducation'];
        $count = 0;
        $recordCount = sizeof($educationArray);

        for ($i = 0; $i < sizeof($_SESSION['playAroundEducation']); $i++) {
            $institution = $educationArray[$i][1];
            $grade = $educationArray[$i][5];
            $code = $educationArray[$i][3];
            $codeExtension = $educationArray[$i][4];
            $subject = $educationArray[$i][2];
            $subjectLevel = $educationArray[$i][7];
            $credits = $educationArray[$i][6];
            $relevantYear = $educationArray[$i][8];
            list($classHeader, $classContent) = calculateClassHeader($count, $recordCount);

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
                <div style="background-color: #D3D3D3; text-align: center; padding-top: 10px;" class="<?php echo $classHeader; ?>">
                    <h1 style="padding-bottom: 10px;">
                        <?php
                        echo $institution;
                        ?>
                    </h1>

                    <!--Display the column titles-->
                    <div class="education-Titles-Large">
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
                            <p>Grade</p>
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
                <!--Display the title on a small screen-->
                <div class="codeTitle">
                    <p class="alignTextLeft">
                        Code:
                    </p>
                </div>
                <div class="education-Code">
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
                <div class="education-Subject">
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
                            echo "grade:";
                        }
                        ?>
                    </p>
                </div>
                <div class="education-Credits">
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
                                echo "NA";
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
                <div class="education-Year">
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
            <?php
        }

        ?>
    </div>
    <!--Display a message to the user-->
    <div class="roundAll editMessage">
        <p>These are my records. You should not be able to edit them. If you do figure it out, please let me know.</p>
    </div>
    <?php

    //SHOW MY RECORDS FROM THE DATABASE
    //Setting a variable so that the institution is only printed once
    $currentInstitution = '';

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
            list($classHeader, $classContent) = calculateClassHeader($count, $recordCount);

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
                <div style="background-color: #D3D3D3; text-align: center; padding-top: 10px;" class="<?php echo $classHeader; ?>">
                    <h1 style="padding-bottom: 10px;">
                        <?php
                        echo $institution;
                        ?>
                    </h1>

                    <!--Display the column titles-->
                    <div class="education-Titles-Large">
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
                <!--Display the title on a small screen-->
                <div class="codeTitle">
                    <p class="alignTextLeft">
                        Code:
                    </p>
                </div>
                <div class="education-Code">
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
                <div class="education-Subject">
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
                            echo "grade:";
                        }
                        ?>
                    </p>
                </div>
                <div class="education-Credits">
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
                                echo "NA";
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
                <div class="education-Year">
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
            <?php
        }
    }

    //Close the statement
    $educationQuery->close();
    ?>
</div>
</body>
<script src="js/functions.js"></script>

<!--Called last so that it renders at the top-->
<?php
require("header.php");
//Pull information from the footer page
require("footer.php");//'Require is 100% needed for this site to run
?>
</html>
