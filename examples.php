<html>
<!--Pulls in the head and other required pages-->
<?php
require("Head.php");
require("Connect.php")
?>
<div class="page-grid-container">
    <!--The first div of the page grid-->
    <div>
        <?php
        require("Header.php");
        require("Nav.php");
        ?>
    </div>

    <!--The second div of the page grid-->
    <div>

    </div>
    <?php
    //The query which shows the education history
    $ExpeienceQuery = ("SELECT `Examples`.`uniqueKey`, `Examples`.`name`, `Examples`.`exampleYearFK`, `relevantYear`.`relevantYear`, `Examples`.`examplesDescription`, `Examples`.`Link`, `Examples`.`github`, `Examples`.`privateRepo`
				FROM `Examples` 
				LEFT JOIN `relevantYear` ON `Examples`.`exampleYearFK` = `relevantYear`.`relevantYearPK`
				ORDER BY `relevantYear`.`relevantYear` DESC
				");

    $ExamplesResult = mysqli_query($con, $ExpeienceQuery);
    $count = 0;
    while ($ExamplesOutput = mysqli_fetch_array($ExamplesResult)) {
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
        ?>
        <div style="background-color: <?php echo $colour; ?>" class="experience-examples-grid-container">
            <div class="experience-examples-image">
                <div class="slideshowContainer">
                    <?php
                    //Load the filepath for the primary images
                    $primaryImage = $ExamplesOutput['name'];
                    $directoryName = "Images/Examples/" . $ExamplesOutput['name'];
                    $primaryImage = $primaryImage . ".png";
                    if (!file_exists($directoryName . "/" . $primaryImage)) {
                        //If the image does not exist, this is the default file path.
                        $primaryImage = "Images/Examples/No Image.png";
                    } else {
                        $primaryImage = $directoryName . "/" . $primaryImage;
                    }

                    //Get the number of other files in the directory
                    $fileCount = 0;
                    $files = glob($directoryName . "/*");
                    if ($files) {
                        $fileCount = count($files);
                    }
                    ?>

                    <!-- Load the primary image -->
                    <div class="imageHolder">
                        <div class="slideProgress" style="align-content: center">
                            <p>
                                1 / <?php echo $fileCount; ?>
                            </p>
                        </div>
                        <img src="<?php echo $primaryImage; ?>" class="center">
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
                            <div class="imageHolder">
                                <div class="slideProgress">
                                    <p>
                                        <?php echo $progress . " / " . $fileCount; ?>
                                    </p>
                                </div>
                                <img src="<?php echo $file; ?>" class="center">
                            </div>
                            <?php
                            //Increment the progress
                            $progress += 1;
                        }
                    }
                    ?>

                    <!--navigation buttons for the sideshow -->
                    <a class="prev" onclick="plusDivs(-1)">&#10094;</a>
                    <a class="next" onclick="plusDivs(+1)">&#10095;</a>


                    <!-- Javascript -->
                    <script>
                        var slideIndex = 1;
                        showDivs(slideIndex);

                        function plusDivs(n) {
                            showDivs(slideIndex += n);
                        }

                        function showDivs(n) {
                            var i;
                            var x = document.getElementsByClassName("imageHolder");
                            if (n > x.length) {slideIndex = 1}
                            if (n < 1) {slideIndex = x.length} ;
                            for (i = 0; i < x.length; i++) {
                                x[i].style.display = "none";
                            }
                            x[slideIndex-1].style.display = "block";
                        }
                    </script>
                </div>
            </div>
            <div class="experience-examples-name">
                <h1>
                    <?php
                    echo $ExamplesOutput['name'];
                    ?>
                </h1>
            </div>
            <div class="experience-examples-year">
                <p>
                    Year:
                    <?php
                    echo $ExamplesOutput['relevantYear'];
                    ?>
                </p>
            </div>
            <div class="experience-examples-language">
                <?php
                //Setting session variables for the uniqueKey

                $key = $ExamplesOutput['uniqueKey'];

                //Running queries to get the languages
                $LanguageOneQuery = ("SELECT Languages.language
							FROM Examples
							LEFT JOIN Languages ON Examples.languageOneFK = Languages.languagePK
							WHERE Examples.uniqueKey LIKE $key
							");

                $LanguageOneResult = mysqli_query($con, $LanguageOneQuery);
                $LanguageOneOutput = mysqli_fetch_row($LanguageOneResult);
                $LanguageOne = implode(" ", $LanguageOneOutput);

                $LanguageTwoQuery = ("SELECT Languages.language
							FROM Examples
							LEFT JOIN Languages ON Examples.languageTwoFK = Languages.languagePK
							WHERE Examples.uniqueKey LIKE $key
							");

                $LanguageTwoResult = mysqli_query($con, $LanguageTwoQuery);
                $LanguageTwoOutput = mysqli_fetch_row($LanguageTwoResult);
                $LanguageTwo = implode(" ", $LanguageTwoOutput);


                $LanguageThreeQuery = ("SELECT Languages.language
							FROM Examples
							LEFT JOIN Languages ON Examples.languageThreeFK = Languages.languagePK
							WHERE Examples.uniqueKey LIKE $key
							");

                $LanguageThreeResult = mysqli_query($con, $LanguageThreeQuery);
                $LanguageThreeOutput = mysqli_fetch_row($LanguageThreeResult);
                $LanguageThree = implode(" ", $LanguageThreeOutput);


                $LanguageFourQuery = ("SELECT Languages.language
							FROM Examples
							LEFT JOIN Languages ON Examples.languageFourFK = Languages.languagePK
							WHERE Examples.uniqueKey LIKE $key
							");

                $LanguageFourResult = mysqli_query($con, $LanguageFourQuery);
                $LanguageFourOutput = mysqli_fetch_row($LanguageFourResult);
                $LanguageFour = implode(" ", $LanguageFourOutput);


                $LanguageFiveQuery = ("SELECT Languages.language
							FROM Examples
							LEFT JOIN Languages ON Examples.languageFiveFK = Languages.languagePK
							WHERE Examples.uniqueKey LIKE $key
							");

                $LanguageFiveResult = mysqli_query($con, $LanguageFiveQuery);
                $LanguageFiveOutput = mysqli_fetch_row($LanguageFiveResult);
                $LanguageFive = implode(" ", $LanguageFiveOutput);


                ?>
                <p>
                    language(s):
                    <?php
                    if ($LanguageOne != 'NA') {
                        echo $LanguageOne;
                    }
                    if ($LanguageTwo != 'NA') {
                        echo(', ' . $LanguageTwo);
                    }
                    if ($LanguageThree != 'NA') {
                        echo(', ' . $LanguageThree);
                    }
                    if ($LanguageFour != 'NA') {
                        echo(', ' . $LanguageFour);
                    }
                    if ($LanguageFive != 'NA') {
                        echo(', ' . $LanguageFive);
                    }
                    ?>
                </p>
            </div>
            <div class="experience-examples-link">
                <p>
                    <?php
                    //Only displays if there is a link to display
                    if ($ExamplesOutput['Link'] != '0') {
                        ?>
                        Link:
                        <a class="pageLink" href="<?php echo $ExamplesOutput['Link']; ?>">
                            <?php
                            echo $ExamplesOutput['Link'];
                            ?>
                        </a>
                        <?php
                    }
                    ?>

                </p>
                <p>
                    <?php
                    //Only displays if there is a link to display and the repo is no private
                    if ($ExamplesOutput['github'] != '0' || $ExamplesOutput['privateRepo'] == 0) {
                        ?>
                        GitHub:
                        <a class="pageLink" href="<?php echo $ExamplesOutput['github']; ?>">
                            <?php
                            echo $ExamplesOutput['github'];
                            ?>
                        </a>
                        <?php
                    }
                    ?>
                </p>
            </div>
            <div class="experience-examples-description">
                <p>
                    <?php
                    echo $ExamplesOutput['examplesDescription'];
                    ?>
                </p>
            </div>
        </div>
    <?php } ?>


</div>


</div>
<!-- Footer -->
<?php
//Pull information from the footer page
require("footer.php");//'Require is 100% needed for this site to run
?>
</html>
