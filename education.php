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
            $institution = '';

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
                    <div style = "text-align: center">
                        <h1>
                            Nothing to see here.
                        </h1>
                    </div>
                <?php
            } else {
                while ($EducationOutput = mysqli_fetch_array($educationResult)) {
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
                    if ($EducationOutput['institution'] != $institution) {
                        //Reset the institution
                        $institution = $EducationOutput['institution'];
                        ?>
                        <div style="background-color: #D3D3D3; text-align: center" class="<?php echo $classHeader; ?>">
                            <h1>
                                <?php
                                echo $EducationOutput['institution'];
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
                        <div class="education-Subject">
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
                        <div class="education-Credits">
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
                        <div class="education-Year">
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
