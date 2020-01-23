<html lang="English">
    <!--Pulls in the head and other required pages-->
    <?php
        require("head.php");
        require("connect.php");
    ?>
    <body class=background-img>
        <div class="page-grid-container">
            <?php
            //The query which shows the education history
            $experienceQuery = ("SELECT `Examples`.`uniqueKey`, `Examples`.`name`, `Examples`.`exampleYearFK`, `relevantYear`.`relevantYear`, `Examples`.`examplesDescription`, `Examples`.`Link`, `Examples`.`github`, `Examples`.`privateRepo`
            FROM `Examples` 
            LEFT JOIN `relevantYear` ON `Examples`.`exampleYearFK` = `relevantYear`.`relevantYearPK`
            ORDER BY `relevantYear`.`relevantYear` DESC
            ");

            $examplesResult = mysqli_query($con, $experienceQuery);
            $recordCount = mysqli_num_rows($examplesResult);
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
                while ($ExamplesOutput = mysqli_fetch_array($examplesResult)) {
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

                    //Load the filepath for the primary images
                    $primaryImage = $ExamplesOutput['name'];
                    $directoryName = "images/examples/" . $ExamplesOutput['name'];
                    $primaryImage = $primaryImage . ".png";

                    if (!file_exists($directoryName . "/" . $primaryImage)) {
                        //If the image does not exist, this is the default file path.
                        $primaryImage = "images/examples/no image.png";
                    } else {
                        $primaryImage = $directoryName . "/" . $primaryImage;
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
                                    <img class="center rounded" src="<?php echo $primaryImage; ?>"
                                         alt="Image of the project">
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
                                                <p>
                                                    <?php echo $progress . " / " . $fileCount; ?>
                                                </p>
                                            </div>
                                            <img class="center rounded" src="<?php echo $file; ?>"
                                                 alt="Image of the project">
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
                            <h1>
                                <?php
                                echo $ExamplesOutput['name'];
                                ?>
                            </h1>
                        </div>
                        <div class="examples-year">
                            <p class="alignTextLeft">
                                Year:
                                <?php
                                echo $ExamplesOutput['relevantYear'];
                                ?>
                            </p>
                        </div>
                        <div class="examples-language">
                            <?php
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
                            <p class="alignTextLeft">
                                Language(s):
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
                        <div class="examples-link">
                            <p class="alignTextLeft">
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
                            <p class="alignTextLeft">
                                <?php
                                //Only displays if there is a link to display and the repo is no private
                                if ($ExamplesOutput['github'] != '0') {
                                    ?>
                                    GitHub:
                                    <?php
                                    if ($ExamplesOutput['privateRepo'] != 0) {
                                        ?>
                                        <a class="pageLink" href="<?php echo $ExamplesOutput['github']; ?>">
                                            <?php
                                            echo $ExamplesOutput['github'];
                                            ?>
                                        </a>
                                        <?php
                                    } else {
                                        ?>
                                        Sorry. This one has to be kept secret.
                                        <?php
                                    }
                                }
                                ?>
                            </p>
                        </div>
                        <div class="examples-description alignTextLeft">
                            <p>
                                <?php
                                echo $ExamplesOutput['examplesDescription'];
                                ?>
                            </p>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

        <!-- Javascript -->
        <script>
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
        </script>
    </body>
    <!--Load last so that it displays on top-->
    <?php
        require("header.php");
        //Pull information from the footer page
        require("footer.php");
    ?>
</html>
