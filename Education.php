<html>
<!--Pulls in the head and other required pages-->
<?php
require("Head.php");
require("Connect.php");
?>

<body class="background-img">
    <div class="page-grid-container">
        <?php
        //Setting a varibale so that the institution is only printed once
        $institution = '';

        //The query which shows the education history
        $EducationQuery = ("SELECT `Education`.`subjectFK`, `Subject`.`subject`, `Education`.`institutionFK`, `Institution`.`institution`, `Education`.`gradeFk`, `Grade`.`grade`, `Education`.`subjectLevelFK`, `subjectLevel`.`subjectLevel`, `Education`.`credits`, `Education`.`classYearFK`, `relevantYear`.`relevantYear`, `Education`.`subjectAbbreviationFK`, `subjectAbbreviation`.`subjectAbbreviation`
                        FROM `Education` 
                        LEFT JOIN `Subject` ON `Education`.`subjectFK` = `Subject`.`subjectPK` 
                        LEFT JOIN `Institution` ON `Education`.`institutionFK` = `Institution`.`institutionPK` 
                        LEFT JOIN `Grade` ON `Education`.`gradeFk` = `Grade`.`gradePK` 
                        LEFT JOIN `subjectLevel` ON `Education`.`subjectLevelFK` = `subjectLevel`.`subjectLevelPK` 
                        LEFT JOIN `relevantYear` ON `Education`.`classYearFK` = `relevantYear`.`relevantYearPK` 
                        LEFT JOIN `subjectAbbreviation` ON `Education`.`subjectAbbreviationFK` = `subjectAbbreviation`.`subjectAbbreviationPK` 
                        ORDER BY `Education`.`institutionFK` DESC, `Grade`.`grade` ASC, `relevantYear`.`relevantYear`DESC, `Education`.`credits` DESC");

        $EducationResult = mysqli_query($con, $EducationQuery);
        $institution = "";
        $recordCount = mysqli_num_rows($EducationResult);
        $count = 0;
        while ($EducationOutput = mysqli_fetch_array($EducationResult)) {
            //Calculates if any rounding of the examples div is required
            $classHeader = "";
            $classContent= "";
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
                <div style="background-color: #D3D3D3; text-align: center" class = "<?php echo $classHeader;?>">
                    <h1>
                        <?php
                            echo $EducationOutput['institution'];
                        ?>
                    </h1>
                </div>
                <?php
            }

            
            //Printing the education information
            ?>
        <div style = "background-color: <?php echo $colour;?>" class="<?php echo $classContent;?>">
            <div class="education-Subject">
                <div style="text-align: center;">
                    <p class="alignTextLeft">
                        <strong>
                            Subject:
                            <?php
                            echo '(' . $EducationOutput['subjectAbbreviation'] . ')';
                            ?>
                        </strong>
                        <?php
                        echo $EducationOutput['subject'];
                        ?>
                    </p>
                </div>
            </div>
            <div class="education-Grade">
                <div style="text-align: center;">
                    <p class="alignTextLeft">
                        <?php
                        //Checking if NCEA or uni results should be displayed
                        if ($EducationOutput['grade'] != null) {
                            echo "Grade: " . $EducationOutput['grade'];
                        } elseif ($EducationOutput['grade'] == null) {
                            echo "Credits: " . $EducationOutput['credits'];
                        }
                        ?>
                    </p>
                </div>
            </div>
            <div class="education-subjectLevel">
                <div style="text-align: center;">
                    <p class="alignTextLeft">
                        Subject Level:
                        <?php
                        echo $EducationOutput['subjectLevel'];
                        ?>
                    </p>
                </div>
            </div>
            <div class="education-Year">
                <div style="text-align: center;">
                    <p class="alignTextLeft">
                        Year:
                        <?php
                        echo $EducationOutput['relevantYear'];
                        ?>
                    </p>
                </div>
            </div>
        </div>
            <?php
        }
        ?>


    </div>
<body class="background-img">

<!--Called last so that it renders at the top-->
<?php
require("Header.php");;
?>

<!-- Footer -->
<?php
//Pull information from the footer page
require("Footer.php");//'Require is 100% needed for this site to run
?>
</html>
