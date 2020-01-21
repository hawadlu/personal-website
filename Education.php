<html>
<!--Pulls in the head and other required pages-->
<?php
require("Head.php");
require("Connect.php");
?>

<body class="background-img">
    <div class="page-grid-container">
        <!--The second div of the page grid-->
        <div>
            <?php
            //Setting a varibale so that the institution is only printed once
            $institution = '';

            //Variabke that allows alternating background colours
            $count = 0;

            //The query which shows the education history
            $EducationQuery = ("SELECT `Education`.`subjectFK`, `Subject`.`subject`, `Education`.`institutionFK`, `Institution`.`institution`, `Education`.`gradeFk`, `Grade`.`grade`, `Education`.`subjectLevelFK`, `subjectLevel`.`subjectLevel`, `Education`.`credits`, `Education`.`classYearFK`, `relevantYear`.`relevantYear`, `Education`.`subjectAbbreviationFK`, `subjectAbbreviation`.`subjectAbbreviation`, `Education`.`endorsementFK`, `Endorsement`.`endorsement`
                    FROM `Education` 
                    LEFT JOIN `Subject` ON `Education`.`subjectFK` = `Subject`.`subjectPK` 
                    LEFT JOIN `Institution` ON `Education`.`institutionFK` = `Institution`.`institutionPK` 
                    LEFT JOIN `Grade` ON `Education`.`gradeFk` = `Grade`.`gradePK` 
                    LEFT JOIN `subjectLevel` ON `Education`.`subjectLevelFK` = `subjectLevel`.`subjectLevelPK` 
                    LEFT JOIN `relevantYear` ON `Education`.`classYearFK` = `relevantYear`.`relevantYearPK` 
                    LEFT JOIN `subjectAbbreviation` ON `Education`.`subjectAbbreviationFK` = `subjectAbbreviation`.`subjectAbbreviationPK` 
                    LEFT JOIN `Endorsement` ON `Education`.`endorsementFK` = `Endorsement`.`endorsementPK`
                    ORDER BY `relevantYear`.`relevantYear` DESC;
                    ");

            $EducationResult = mysqli_query($con, $EducationQuery);
            while ($EducationOutput = mysqli_fetch_array($EducationResult)) {
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

                //checks for university than college
                if (($EducationOutput['institutionFK']) == '0') {


                    ?>

                    <div style="text-align: center;">
                        <h1>
                            <?php
                            if (($EducationOutput['institution']) != ($institution)) {
                                echo $EducationOutput['institution'];
                                $institution = $EducationOutput['institution'];

                            }

                            ?>
                        </h1>
                    </div>
                    <div style="background-color: <?php echo $colour; ?>" class="education-grid-container-uni">
                        <div class="education-Subject-uni">
                            <div style="text-align: center;">
                                <p>
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
                        <div class="education-Grade-uni">
                            <div style="text-align: center;">
                                <p>
                                    Grade:
                                    <?php
                                    echo $EducationOutput['grade'];
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="education-subjectLevel-uni">
                            <div style="text-align: center;">
                                <p>
                                    Subject Level:
                                    <?php
                                    echo $EducationOutput['subjectLevel'];
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="education-Year-uni">
                            <div style="text-align: center;">
                                <p>
                                    Year:
                                    <?php
                                    echo $EducationOutput['relevantYear'];
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    //printing results for tawa college

                    ?>
                    <div style="text-align: center;">
                        <h1>
                            <?php
                            if (($EducationOutput['institution']) != ($institution)) {
                                echo $EducationOutput['institution'];
                                $institution = $EducationOutput['institution'];

                            }

                            ?>
                        </h1>
                    </div>
                    <div style="background-color: <?php echo $colour; ?>" class="education-grid-container-col">
                        <div class="education-Subject-col">
                            <div style="text-align: center;">
                                <p>
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
                        <div class="education-Endorsement-col">
                            <div style="text-align: center;">
                                <p>
                                    Endorsement:
                                    <?php
                                    echo $EducationOutput['endorsement'];
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="education-Credits-col">
                            <div style="text-align: center;">
                                <p>
                                    Credits:
                                    <?php
                                    echo $EducationOutput['credits'];
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="education-subjectLevel-col">
                            <div style="text-align: center;">
                                <p>
                                    Subject Level:
                                    <?php
                                    echo $EducationOutput['subjectLevel'];
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="education-Year-col">
                            <div style="text-align: center;">
                                <p>
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
            }
            ?>

        </div>


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
