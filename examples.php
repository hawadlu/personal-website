<html lang="English">
<!--Pulls in the head and other required pages-->
<?php
require("head.php");
require("connect.php");

//Setup the user based records
//Check to see if the playAroundExamples session exists
if (!isset($_SESSION['playAroundExamples'])) {
    //Create some default Records
    $defaultRec1 = [0, ['Images/userImages/cat.jpeg', 'Images/userImages/eraser.jpeg', 'Images/userImages/phone.jpeg'], "How I Saved To World Twice", ["CSS", "PHP", "JavaScript", "HTML"],
        "http://luke.dx.am", "http://github.com/hawadlu", "Whoops, I forgot to write a description here.", 2019];
    $defaultRec2 = [1, ['Images/userImages/fork.jpeg', 'Images/userImages/nailClippers.jpeg', 'Images/userImages/phone.jpeg', 'Images/userImages/spring.jpeg'],
        "What I Ate For Breakfast", ["CSS", "PHP", "RUBY", "Perl"], 'https://breakfast.co.nz', "https://github.com/PushyPixels/BreakfastWithUnity",
        "Contrary to the opinions of many people. I did not eat a fork, nail clippers, a phone and a spring for breakfast. I ate cornflakes instead. 
        I know that it's disappointing and a little anticlimactic.", 2020];
    $_SESSION['playAroundExamples'] = array($defaultRec1, $defaultRec2);
}

if (isset($_SESSION['playAroundExamples'])) {
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
        $path = 'Images/userImages';
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


?>
<body class=background-img>
<div class="page-grid-container">
    <!--Display a message to the user-->
    <div style="background-color: white; opacity: 80%; height: 50px; line-height: 50px; text-align: center; margin-bottom: 20px" class="roundAll">
        <p>These are your records. You can edit them by clicking on the play around tab!</p>
    </div>
    <!--Show the user their own records-->
    <?php
    $examplesArray = $_SESSION['playAroundExamples'];
    $recordCount = sizeof($examplesArray);
    $count = 0;

    for ($i = 0; $i < sizeof($examplesArray); $i++) {

        //Get the variables
        $uniqueKey = $examplesArray[$i][0];
        $name = $examplesArray[$i][2];
        $link = $examplesArray[$i][4];
        $github = $examplesArray[$i][5];
        $relevantYear = $examplesArray[$i][7];
        $examplesDescription = $examplesArray[$i][6];

        //Check if there are images
        if ($examplesArray[$i][1] == "" || is_null($examplesArray[$i][1])) {
            $primaryImage = null;
        } else {
            $primaryImage = $examplesArray[$i][1][0];
            $fileCount = sizeof($examplesArray[$i][1]);
        }

        //Calculates if any rounding of the examples div is required
        $class = "";
        if ($count == $recordCount - 1) {
            $class = "user examples-grid-container roundBottom";
        } else if ($count == 0) {
            $class = "user examples-grid-container roundTop";
        } else {
            $class = "user examples-grid-container";
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
        $slideshowID = "ssIDuser" . $count;
        ?>

        <div style="background-color: <?php echo $colour; ?>;" class="<?php echo $class; ?>">
            <div class="examples-name">
                <h1><?php echo $name; ?></h1>
            </div>
            <div class="examples-year">
                <p class="alignTextLeft">Year:<?php echo $relevantYear; ?></p>
            </div>
            <div class="examples-language">
                <?php


                ?>
                <p class="alignTextLeft">
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
                                <a class="prev" onclick="plusDivsUser(-1, <?php echo $count - 1; ?>)">&#10094;</a>
                                <a class="next" onclick="plusDivsUser(+1, <?php echo $count - 1; ?>)">&#10095;</a>
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
        </div>
        <?php
    }


    //Show the user my records
    ?>
    <!--Display a message to the user-->
    <div style="background-color: white; opacity: 80%; height: 50px; line-height: 50px; text-align: center; margin-bottom: 20px; margin-top: 20px" class="roundAll">
        <p>These are my records. You should not be able to edit them. If you do figure it out, please let me know.</p>
    </div>
    <?
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
            <h1>
                Nothing to see here.
            </h1>
        </div>
        <?php
    } else {
        while ($row = $experienceQuery->fetch()) {
            //Calculates if any rounding of the examples div is required
            $class = "";
            if ($count == $recordCount - 1) {
                $class = "db examples-grid-container roundBottom";
            } else if ($count == 0) {
                $class = "db examples-grid-container roundTop";
            } else {
                $class = "db examples-grid-container";
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
            $slideshowID = "ssIDdb" . $count;
            ?>

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
                    <!--Only show the images if there are images to display-->
                    <?php
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
                                if ($primaryImage != "images/examples/no image.png" && $fileCount > 1) {
                                    ?>
                                    <!--navigation buttons for the sideshow -->
                                    <a class="prev" onclick="plusDivsDatabase(-1, <?php echo $count - 1; ?>)">&#10094;</a>
                                    <a class="next" onclick="plusDivsDatabase(+1, <?php echo $count - 1; ?>)">&#10095;</a>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <p style="padding-top: 30px"><?php echo $examplesDescription; ?></p>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>

<!-- Javascript -->
<script>
    //Controls for sideshows on records controlled by the user
    const slideIndexUser = [];

    //Pre-populate the list. Dynamically adjusts based on the number of instances of the grid class
    const slideIdUser = [];
    for (let i = 0; i < document.querySelectorAll(".user").length; i++) {
        slideIdUser.push("ssIDuser" + (i + 1));
        slideIndexUser.push(1);
        showDivsUser(1, i);
    }

    function plusDivsUser(n, no) {
        showDivsUser(slideIndexUser[no] += n, no);
    }

    function showDivsUser(n, no) {
        let i;
        const x = document.getElementsByClassName(slideIdUser[no]);
        if (n > x.length) {
            slideIndexUser[no] = 1
        }
        if (n < 1) {
            slideIndexUser[no] = x.length
        }
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        x[slideIndexUser[no] - 1].style.display = "block";
    }

    //Controls for sideshows on records controlled by the database
    const slideIndexDatabase = [];

    //Pre-populate the list. Dynamically adjusts based on the number of instances of the grid class
    const slideIdDatabase = [];
    for (let i = 0; i < document.querySelectorAll(".db").length; i++) {
        slideIdDatabase.push("ssIDdb" + (i + 1));
        slideIndexDatabase.push(1);
        showDivsDatabase(1, i);
    }

    function plusDivsDatabase(n, no) {
        showDivsDatabase(slideIndexDatabase[no] += n, no);
    }

    function showDivsDatabase(n, no) {
        let i;
        const x = document.getElementsByClassName(slideIdDatabase[no]);
        if (n > x.length) {
            slideIndexDatabase[no] = 1
        }
        if (n < 1) {
            slideIndexDatabase[no] = x.length
        }
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        x[slideIndexDatabase[no] - 1].style.display = "block";
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
