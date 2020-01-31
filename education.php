<html lang="English">
<!--TODO add play around functionality with reset-->
<!--Pulls in the head and other required pages-->
<?php
require("head.php");
require("connect.php");
?>

<body class="background-img">
<div class="page-grid-container">
    <?php
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
    LEFT JOIN subjectCode ON education.codeFK = subjectCode.subjectCodePK
     LEFT JOIN subjectLevel ON education.subjectLevelFK = subjectLevel.subjectLevelPK 
     ORDER BY education.institutionFK DESC, year.year DESC, credits.credits DESC, grade.grade ASC,subjectCode.subjectCode ASC");
    $educationQuery -> execute();
    $educationQuery->bind_result($uniqueKey, $subject, $codeExtension, $credits, $grade, $institution, $relevantYear, $code, $subjectLevel);
    $educationQuery->store_result();
    $recordCount = $educationQuery->num_rows();
    $currentInstitution = "";

        $count = 0;
    //Display a message if there are no records returned
    if ($recordCount == 0) {
        ?>
        <div style = "text-align: center">
            <h1>
                Nothing to see here.
            </h1>
        </div>
        <?php
    } else {
        while ($row=$educationQuery->fetch()) {
            //Calculates if any rounding of the examples div is required
            $classHeader = "";
            $classContent = "";
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
                    <h1>
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
                            } else  {
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
                <div class="education-Year">
                    <div style="text-align: center;">
                        <p class="alignTextLeft">
                            <?php
                            if ($relevantYear!= null) {
                                echo $relevantYear;
                            } else {
                                echo "Not applicable";
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>
</body>>

<!--Called last so that it renders at the top-->
<?php
require("header.php");
//Pull information from the footer page
require("footer.php");//'Require is 100% needed for this site to run
?>
</html>
