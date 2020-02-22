<?php
//todo when deleting/renaming records ensure that the file paths are handled appropriately


//Check if the user is logged in
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

require("connect.php");

//Table names from the DB
$tablesToCheckEducation = ['institution', 'subjectLevel', 'year', 'subjectCode', 'codeExtension', 'grade'];
$tablesToCheckExamples = ['year', 'languages'];

//Used when looking for duplicate records
$tableColumnsEducation = ['institutionFk', 'subject', 'gradeFK', 'subjectLevelFK', 'yearFK', 'subjectCodeFK', 'creditsFk', 'codeExtensionFK'];
$tableColumnsExamples = ['name', 'yearFk', 'description', 'languageOneFK', 'languageTwoFK', 'languageThreeFK', 'languageFourFK', 'languageFiveFK', 'link', 'github'];

//Updating education records
if (isset($_POST['submitEducationUpdate'])) {
    //Variables for each posted value. Spaces stripped appropriately
    $postedInstitution = $_POST['institution'];
    $postedSubject = $_POST['subject'];
    $postedSubjectYear = stripSpaces($_POST['subjectYear']);
    $postedSubjectLevel = $_POST['subjectLevel'];
    $postedCode = stripSpaces($_POST['code']);
    $postedCodeExtension = stripSpaces($_POST['codeExtension']);
    $postedCredits = stripSpaces($_POST['credits']);
    $postedGrade = stripSpaces($_POST['gpa']);
    $uniqueKey = $_POST['uniqueKey'];

    //echo "Update record.";

    //Error check all the values. Include each value, the field name, a flag of the expected type in the array and the max length.
    findInvalid([[$postedInstitution, 'institution', 'string', 40, false],
            [$postedSubject, 'subject', 'string', 100, false],
            [$postedSubjectYear, 'subjectYear', 'int', 4, false],
            [$postedSubjectLevel, 'subjectLevel', 'string', 20, false],
            [$postedCode, 'subjectCode', 'string', 10, false],
            [$postedCodeExtension, 'codeExtension', 'int', 4, false],
            [$postedCredits, 'credits', 'int', 2, false],
            [$postedGrade, 'grade', 'string', 2, false]]);

    //2d array of the fields, values and their linked tables (if required) to be inserted
    $toInsert = [['institutionFK', $postedInstitution, 'institution'],
        ['subject', $postedSubject],
        ['gradeFK', $postedGrade, 'grade'],
        ['subjectLevelFK', $postedSubjectLevel, 'subjectLevel'],
        ['yearFK', $postedSubjectYear, 'year'],
        ['subjectCodeFK', $postedCode, 'subjectCode'],
        ['creditsFK', $postedCredits, 'credits'],
        ['codeExtensionFK', $postedCodeExtension, 'codeExtension']];

    for ($i = 0; $i < sizeof($toInsert); $i++) {
        if (empty($toInsert[$i][1])) {
            $toInsert[$i][1] = "NULL";
        }
    }

    //Call function to update the values
    updateValues($toInsert, $con, 'education', $uniqueKey);

    //Redirect the user
    redirectWithSuccess("Record has been updated!", 'edit.php');
}

//Creating new education records
if (isset($_POST["newEducationRecord"])) {
    //echo "New Record";
    ?><br><?php

    //Set variables for the posted values
    $postedNewInstitution = $_POST['newInstitution'];
    $postedNewSubject = $_POST['newSubject'];
    $postedNewSubjectYear = stripSpaces($_POST['newSubjectYear']);
    $postedNewSubjectLevel = $_POST['newSubjectLevel'];
    $postedNewSubjectCode = stripSpaces($_POST['newCode']);
    $postedNewCodeExtension = stripSpaces($_POST['newCodeExtension']);
    $postedNewCredits = stripSpaces($_POST['newCredits']);
    $postedNewGrade = stripSpaces($_POST['newGpa']);

    //Error check all the values. Include each value, the field name, a flag of the expected type in the array,
    //the max length and a flag to signify if it is ok for the field to be empty.
    findInvalid([[$postedNewInstitution, 'institution', 'string', 40, false],
            [$postedNewSubject, 'subject', 'string', 100, false],
            [$postedNewSubjectYear, 'subjectYear', 'int', 4, false],
            [$postedNewSubjectLevel, 'subjectLevel', 'string', 20, false],
            [$postedNewSubjectCode, 'subjectCode', 'string', 10, false],
            [$postedNewCodeExtension, 'codeExtension', 'int', 4, false],
            [$postedNewCredits, 'credits', 'int', 2, false],
            [$postedNewGrade, 'grade', 'string', 2, false]]);


    //Values that will be inserted
    $toInsert = [['institutionFK', $postedNewInstitution, 'institution'],
        ['subject', $postedNewSubject],
        ['gradeFK', $postedNewGrade, 'grade'],
        ['subjectLevelFK', $postedNewSubjectLevel, 'subjectLevel'],
        ['yearFK', $postedNewSubjectYear, 'year'],
        ['subjectCodeFK', $postedNewSubjectCode, 'subjectCode'],
        ['creditsFK', $postedNewCredits, 'credits'],
        ['codeExtensionFK', $postedNewCodeExtension, 'codeExtension']];

    for ($i = 0; $i < sizeof($toInsert); $i++) {
        echo "Empty: " . empty($toInsert[$i][1]);
        if (empty($toInsert[$i][1])) {
            $toInsert[$i][1] = 'NULL';
        }
    }

    echo print_r($toInsert);

    //Run function to insert the record
    insertValues($toInsert, $con, 'education');

    //Redirect the user
    redirectWithSuccess("Record has been created!", 'edit.php');

}

//Deleting education records
if (isset($_POST['deleteEducationRecord'])) {
    //echo "Delete record";

    //Perform a query to get the record to be deleted. This allows the foreign keys to be gathered so that their corresponding values can also be deleted if required
    $getRecordQuery = "SELECT * 
    FROM education
    WHERE education.uniqueKey = " . $_POST['uniqueKey'];

    ?><br><?php
    //echo "Query: " . $getRecordQuery;

    $getRecordQuery = $con->prepare($getRecordQuery);
    $getRecordQuery->execute();
    $getRecordQuery->bind_result($uniqueKey, $institutionFK, $subject, $gradeFK, $subjectLevelFK, $yearFk, $subjectCodeFK, $creditsFK, $codeExtensionFK);
    $getRecordQuery->store_result();

    while ($row = $getRecordQuery->fetch()) {
        //Add all the values to an array for easy iteration
        $tablesToCheckEducation = ['institution', 'grade', 'subjectLevel', 'year', 'subjectCode', 'credits', 'codeExtension'];
        $keysToCheck = [$institutionFK, $gradeFK, $subjectLevelFK, $yearFk, $subjectCodeFK, $creditsFK, $codeExtensionFK];
    }

    //Check to see if the foreign keys are used anywhere else
    for ($i = 0; $i < sizeof($keysToCheck); $i++) {
        ?><br><?php
        //echo "Query: " . "SELECT * FROM education WHERE education." . $tablesToCheckEducation[$i] . "FK = " . $keysToCheck[$i];
        ?><br><?php
        $recordCount = numTimesFkUsedEducation("SELECT * FROM education WHERE education." . $tablesToCheckEducation[$i] . "FK = " . $keysToCheck[$i], $con);
        //echo "Record count: " . $recordCount;

        //If the record count is one it is safe to delete from the linked table
        if ($recordCount == 1) {
            ?><br><?php
            runQuery("DELETE FROM " . $tablesToCheckEducation[$i] . " WHERE " . $tablesToCheckEducation[$i] . "PK = " . $keysToCheck[$i], $con);
        }
    }

    //Delete the item in the education table
    runQuery("DELETE FROM education WHERE education.uniqueKey = " . $uniqueKey, $con);

    //Redirect the user
    redirectWithSuccess("Record has been deleted!", 'edit.php');

}

//Updates examples
if (isset($_POST['submitExampleUpdate'])) {
    echo "Updating example";
    echo "<br>";
    echo var_dump($_POST);
    $value = null;
    $foreignKeys = [];

    //looking for a checked update language and an empty update language entry
    if (!empty($_POST['updateLanguageInput']) && empty($_POST['updateLanguageEntry'])) {
        ?><br><?php
        die("You checked 'other' but did not enter a update language");

        //Looking for a update language entry and an unchecked update language checkbox
    } else if (empty($_POST['updateLanguageInput']) && !empty($_POST['updateLanguageEntry'])) {
        ?><br><?php
        die("You entered a language but did not check 'other'");

        //Looking for a checked github and no github link
    } else if (!empty($_POST['updateGithubInput']) && empty($_POST['updateGithubEntry'])) {
        ?><br><?php
        die("You checked 'github' but did not enter a link");

        //Looking for a github link and no checked github
    } else if (empty($_POST['updateGithubInput']) && !empty($_POST['updateGithubEntry'])) {
        ?><br><?php
        die("You entered a github link but did not check 'github'");

        //Looking for a checked link but no link provided
    } else if (!empty($_POST['updateLinkInput']) && empty($_POST['updateLinkEntry'])) {
        ?><br><?php
        die("You checked 'link' and did not enter a link");

        //Looking for link and no check link
    } else if (empty($_POST['updateLinkInput']) && !empty($_POST['updateLinkEntry'])) {
        ?><br><?php
        die("You entered a link but did not check 'link'");
    }

    //Get all the posted variables
    $postedExampleName = $_POST['exampleName'];
    $postedExampleYear = $_POST['exampleYear'];
    $postedExampleLink = $_POST['updateLinkEntry'];
    echo "Empty: " . empty($_POST['updateLinkEntry']);
    $postedExampleGithub = $_POST['updateGithubEntry'];
    $postedExampleDescription = $_POST['exampleDescription'];
    $uniqueKey = $_POST['uniqueKey'];

    //Add the posted values to an array that will be checked for invalids once the languages have been processed
    //Error check all the values. Include each value, the field name, a flag of the expected type in the array,
    //the max length and a flag to signify if it is ok for the field to be empty.
    $invalidArray = [[$postedExampleName, 'exampleName', 'string', 100, false],
        [$postedExampleYear, 'exampleYear', 'int', 4, false],
        [$postedExampleLink, 'exampleLink', 'string', 100, true],
        [$postedExampleGithub, 'exampleGithub', 'string', 100, true],
        [$postedExampleDescription, 'exampleDescription', 'string', 1000, false]];


    //Get all the languages currently stored in the database
    $value = null;
    $query = $con->prepare("SELECT languages.languages FROM languages WHERE languages != ''");
    $query->execute();
    $query->bind_result($value);
    $query->store_result();

    $languageArray = [];

    while ($row = $query->fetch()) {
        array_push($languageArray, $value);
    }
    ?><br><?php
    echo sizeof($languageArray) . " languages were found in the database.";

    $languagesUsed = []; //Array of languages that the user wants to add

    if ($_POST['updateLanguageEntry'] != "") {
        //Start the language counter at 1
        $languageCount = 1;

        //Add the language to the language array
        array_push($languagesUsed, $_POST['newLanguage']);
    } else {
        $languageCount = 0;
    }

    //Checking to see which of the languages ued in the database are requested by the user
    for ($i = 0; $i < sizeof($languageArray); $i++) {
        if (isset($_POST[str_replace(' ', '_', $languageArray[$i])]) && $_POST[str_replace(' ', '_', $languageArray[$i])] == $languageArray[$i]) {
            //Increment the counter and add the language to the used array
            $languageCount++;
            array_push($languagesUsed, $languageArray[$i]);
            ?><br><?php
            echo $languageArray[$i] . " found";

            //Add each language to the array to be tested. This to make sure that no html inputs have been tampered with
            array_push($invalidArray, [$_POST[str_replace(' ', '_', $languageArray[$i])], $languageArray[$i], 'string', 20]);

        } else if (isset($_POST[str_replace(' ', '_', $languageArray[$i])])) {
            //Report the value as invalid
            redirectWithError($_POST[str_replace(' ', '_', $languageArray[$i])] . " is not a valid language. ", 'edit.php');
        }

        //return an error if there are too many languages
        if ($languageCount > 5) {
            redirectWithError('You cannot add more than five languages per project.', 'edit.php');
        }
    }

    //User must enter at least one language
    if ($languageCount == 0) {
        redirectWithError('You must enter at least one language', 'edit.php');
    }


    //error check
    findInvalid($invalidArray);

    //Check the database to see if the name already exists.
    if (findDuplicate("SELECT * FROM examples WHERE name = '" . $postedExampleName . "' AND examples.uniqueKey != " .$uniqueKey, $con)) {
        redirectWithError('Duplicate record', 'edit.php');
    }

    //2d array of the fields, values and their linked tables (if required) to be inserted
    $toInsert = [['name', $postedExampleName], ['yearFK', $postedExampleYear, 'year'], ['description', $postedExampleDescription], ['link', $postedExampleLink], ['github', $postedExampleGithub]];


    //Add the languages to the insert array
    for ($i = 0; $i < sizeof($languagesUsed); $i++) {
        //Set the language number
        if ($i == 0) {
            $langNum = "One";
        } else if ($i == 1) {
            $langNum = "Two";
        } else if ($i == 2) {
            $langNum = "Three";
        } else if ($i == 3) {
            $langNum = "Four";
        } else {
            $langNum = "Five";
        }

        //Add to the to insert array
        array_push($toInsert, ['language' . $langNum . 'FK', $languagesUsed[$i], 'languages']);
    }

    //Replace empty values with null
    for ($i = 0; $i < sizeof($toInsert); $i++) {
        if (empty($toInsert[$i][1])) {
            $toInsert[$i][1] = "NULL";
        }
    }

    ?><br><?php
    echo $languageCount . " languages have been used.";
    ?><br><?php
    echo "Insert array";
    //Print for debugging
//    for ($i = 0; $i < sizeof($toInsert); $i++) {
//        ?><!--<br>--><?php
//        echo $toInsert[$i][0] . ", " . $toInsert[$i][1];
//    }

    //Run a function to update all of the necessary values
    updateValues($toInsert, $con, 'examples', $uniqueKey);

    //Redirect the user
    redirectWithSuccess("Record has been updated!", 'edit.php');

    //todo add ability to rename the directory to the current example name
}

//Delete examples
if (isset($_POST['deleteExample'])) {
    //Create a 2d array of the FK column's and their respective keys
    $uniqueKey = $name = $yearFk = $description = $languageOneFK = $languageTwoFK =$languageThreeFK = $languageFourFK = $languageFiveFK = $link = $github = null;
    $query = "SELECT * FROM examples WHERE examples.uniqueKey = " . $_POST['uniqueKey'];
    ?><br><?php
    echo $query;
    $query = runAndReturn($query, $con);
    $query->bind_result($uniqueKey, $name, $yearFk, $description, $languageOneFK, $languageTwoFK, $languageThreeFK, $languageFourFK, $languageFiveFK, $link, $github);
    $query->store_result();

    //The tables that use this FK, the table that stores the FK, the column where the FK can be found, the value
    $toDelete = [[['examples', 'education'], 'year', 'yearFK', $yearFk],
        [['examples'], 'languages', 'languageOneFK', $languageOneFK],
        [['examples'], 'languages', 'languageTwoFK', $languageTwoFK],
        [['examples'], 'languages', 'languageThreeFK', $languageThreeFK],
        [['examples'], 'languages', 'languageFourFK', $languageFourFK],
        [['examples'], 'languages', 'languageFiveFK', $languageFiveFK]];

    //Delete the record
    ?><br><?php
    $query = "DELETE FROM examples WHERE examples.uniqueKey = " . $_POST['uniqueKey'];
    echo $query;
    //runQuery($query, $con);

    cleanUpFK($toDelete);
}

//Deletes images
if (isset($_POST['deleteImage'])) {
    echo "Delete image: " . $_POST['file'];
}

//Creates new examples
if (isset($_POST['newExampleRecord'])) {
    //todo ensure that both the query and the images are valid before uploading/submitting either;
    echo "Updating example";
    echo "<br>";
    echo var_dump($_POST);
    $value = null;
    $foreignKeys = [];

    //looking for a checked new language and an empty new language entry
    if (!empty($_POST['newLanguageInput']) && empty($_POST['newLanguageEntry'])) {
        ?><br><?php
        die("You checked 'other' but did not enter a new language");

        //Looking for a new language entry and an unchecked new language checkbox
    } else if (empty($_POST['newLanguageInput']) && !empty($_POST['newLanguageEntry'])) {
        ?><br><?php
        die("You entered a new language but did not check 'other'");

        //Looking for a checked github and no github link
    } else if (!empty($_POST['newGithubInput']) && empty($_POST['newGithubEntry'])) {
        ?><br><?php
        die("You checked 'github' but did not enter a link");

        //Looking for a github link and no checked github
    } else if (empty($_POST['newGithubInput']) && !empty($_POST['newGithubEntry'])) {
        ?><br><?php
        die("You entered a new github link but did not check 'github'");

        //Looking for a checked link but no link provided
    } else if (!empty($_POST['newLinkInput']) && empty($_POST['newLinkEntry'])) {
        ?><br><?php
        die("You checked 'link' and did not enter a link");

        //Looking for link and no check link
    } else if (empty($_POST['newLinkInput']) && !empty($_POST['newLinkEntry'])) {
        ?><br><?php
        die("You entered a new link but did not check 'link'");
    }

    //Get all the posted variables
    $postedNewExampleName = $_POST['newExampleName'];
    $postedNewExampleYear = $_POST['newExampleYear'];
    $postedNewExampleLink = $_POST['newLinkEntry'];
    $postedNewExampleGithub = $_POST['newGithubEntry'];
    $postedNewExampleDescription = $_POST['newExampleDescription'];

    //Add the posted values to an array that will be checked for invalids once the languages have been processed
    //Error check all the values. Include each value, the field name, a flag of the expected type in the array,
    //the max length and a flag to signify if it is ok for the field to be empty.
    $invalidArray = [[$postedNewExampleName, 'exampleName', 'string', 100, false],
        [$postedNewExampleYear, 'exampleYear', 'int', 4, false],
        [$postedNewExampleLink, 'exampleLink', 'string', 100, true],
        [$postedNewExampleGithub, 'exampleGithub', 'string', 100, true],
        [$postedNewExampleDescription, 'exampleDescription', 'string', 1000, false]];

    //Check the database to see if the name already exists.
    if (findDuplicate("SELECT * FROM examples WHERE name = '" . $postedNewExampleName . "'", $con)) {
        redirectWithError('Duplicate record', 'edit.php');
    }


    //Get all the languages currently stored in the database
    $value = null;
    $query = $con->prepare("SELECT languages.languages FROM languages WHERE languages != ''");
    $query->execute();
    $query->bind_result($value);
    $query->store_result();

    $languageArray = [];

    while ($row = $query->fetch()) {
        array_push($languageArray, $value);
    }
    ?><br><?php
    echo sizeof($languageArray) . " languages were found in the database.";

    $languagesUsed = []; //Array of languages that the user wants to add

    if ($_POST['newLanguageEntry'] != "") {
        //Start the language counter at 1
        $languageCount = 1;

        //Add the language to the language array
        array_push($languagesUsed, $_POST['newLanguage']);
    } else {
        $languageCount = 0;
    }

    //Checking to see which of the languages ued in the database are requested by the user
    for ($i = 0; $i < sizeof($languageArray); $i++) {
        if (isset($_POST[str_replace(' ', '_', $languageArray[$i])]) && $_POST[str_replace(' ', '_', $languageArray[$i])] == $languageArray[$i]) {
            //Increment the counter and add the language to the used array
            $languageCount++;
            array_push($languagesUsed, $languageArray[$i]);
            ?><br><?php
            echo $languageArray[$i] . " found";

            //Add each language to the array to be tested. This to make sure that no html inputs have been tampered with
            array_push($invalidArray, [$_POST[str_replace(' ', '_', $languageArray[$i])], $languageArray[$i], 'string', 20]);

        } else if (isset($_POST[str_replace(' ', '_', $languageArray[$i])])) {
            //Report the value as invalid
            redirectWithError($_POST[str_replace(' ', '_', $languageArray[$i])] . " is not a valid language. ", 'edit.php');
        }

        //return an error if there are too many languages
        if ($languageCount > 5) {
            redirectWithError('You cannot add more than five languages per project.', 'edit.php');
        }
    }

    //User must enter at least one language
    if ($languageCount == 0) {
        redirectWithError('You must enter at least one language', 'edit.php');
    }

    //error check
    findInvalid($invalidArray);

    //2d array of the fields, values and their linked tables (if required) to be inserted
    $toInsert = [['name', $postedNewExampleName],
        ['yearFK', $postedNewExampleYear, 'year'],
        ['description', $postedNewExampleDescription],
        ['link', $postedNewExampleLink],
        ['github', $postedNewExampleGithub]];

    //If the language array is < 5 add default values
    ?><br><?php
    echo "Language array size: " . sizeof($languagesUsed);
    if (sizeof($languagesUsed) < 5) {
        for ($i = sizeof($languagesUsed); $i < 5; $i++) {
            //push the default null value to the array
            array_push($languagesUsed, "NULL");
            }
        }


    ?><br><?php
    echo "Languages used array: " . print_r($languagesUsed);

    //Add the languages to the insert array
    for ($i = 0; $i < sizeof($languagesUsed); $i++) {
        //Set the language number
        if ($i == 0) {
            $langNum = "One";
        } else if ($i == 1) {
            $langNum = "Two";
        } else if ($i == 2) {
            $langNum = "Three";
        } else if ($i == 3) {
            $langNum = "Four";
        } else {
            $langNum = "Five";
        }

        //Add to the to insert array
        array_push($toInsert, ['language' . $langNum . 'FK', $languagesUsed[$i], 'languages']);
    }

    //Replace empty values with null
    for ($i = 0; $i < sizeof($toInsert); $i++) {
        if (empty($toInsert[$i][1])) {
            $toInsert[$i][1] = "NULL";
        }
    }

    ?><br><?php
    echo $languageCount . " languages have been used.";
    ?><br><?php
    echo "Insert array";
    //Print for debugging
    for ($i = 0; $i < sizeof($toInsert); $i++) {
        ?><br><?php
        echo $toInsert[$i][0] . ", " . $toInsert[$i][1];
    }

    ?><br><?php
    ?><br><?php
    ?><br><?php
    //echo print_r($toInsert);

    //Insert the record
    insertValues($toInsert, $con, 'examples');

    //A flag used to explain that the record was created but something went wrong with the images
    $recordCreated = "The record was created but... ";

    ?><br><?php
    ?><br><?php
    ?><br><?php
    ?><br><?php
    ?><br><?php
    echo "First Image Name: " . $_FILES['userFiles']['name'][0];
    ?><br><?php
    //die(var_dump($_FILES['userFiles']));

    //Handle file uploads
    //Check a file has been uploaded in the form
    if (isset($_FILES['userFiles']) && $_FILES['userFiles']['name'][0] != "") {
        //useful functions and variables. Credit to "Clever Techie. https://www.youtube.com/watch?v=KXyMpRp4d2Q"
        //Array of possible file upload errors
        $phpFileUploadErrors = array(
            0 => "The file uploaded successfully",
            1 => "The file exceeds the maximum file size defined in php.ini",
            2 => "The file exceeds the maximum file size defines in the HTML form",
            3 => "The uploaded file was only partially uploaded",
            4 => "No file was uploaded",
            6 => "Missing a temporary folder",
            7 => "Filed to write file to the disc",
            8 => "A php extension stopped the file from uploading"
        );

        $file_array = reArrayFiles($_FILES['userFiles']);
        //pre_r($file_array);
        //die();

        //The directory where the images will be stored.
        $directory = "images/examples/" . stripSpaces($postedNewExampleName) . "/";

        //Create the directory if it does not exist
        if (!is_dir($directory)) {
            mkdir($directory);
        }

        for ($i = 0; $i < count($file_array); $i++) {
            //Check for errors
            if ($file_array[$i]['error']) {
                redirectWithError($recordCreated . " " . $file_array[$i]['name'] . " " . $phpFileUploadErrors[$file_array[$i]['error']], 'edit.php');

                //Check for extensions errors
            } else {
                //Allowable file types
                $extensions = array("jpg", "png", "gif", "jpeg");
                $file_ext = explode(".", $file_array[$i]["name"]);
                $file_ext = end($file_ext);

                //Check if the extension is acceptable
                if (!in_array($file_ext, $extensions)) {
                    redirectWithError($recordCreated . " " . $file_array[$i]["name"] . " Invalid file extension!", 'edit.php');
                } else {
                    //File uploaded successfully
                    //Check if the file already exists in the directory
                    if (!file_exists("images/" . $file_array[$i]["name"])) {
                        //Move the file from the temporary directory to the intended directory. Resize at the same time
                        move_uploaded_file($file_array[$i]["tmp_name"], $directory . $file_array[$i]["name"]);

                    } else {
                        //Print message stating that the file already exists
                        redirectWithError($recordCreated . " " . $file_array[$i]["name"] . " already exists", 'edit.php');
                    }
                }
            }

            //Resize the image
            $file = $directory . $file_array[$i]["name"];
            $image = resize_image($directory . $file_array[$i]['name'], 250, 250);

            //Save the image
            if (strpos($file, '.jpeg')) {
                imagejpeg($image, $file);
            } else if (strpos($file, '.png')) {
                imagepng($image, $file);
            } else if (strpos($file, '.gif')) {
                imagegif($image, $file);
            } else if (strpos($file, '.jpg')) {
                imagejpeg($image, $file);
            }

        }

        //Print a success message
        redirectWithSuccess("The record was created and images uploaded", 'edit.php');

    }
}

//Takes a 2d array of foreign keys and cleans any that are no longer needed
//ToClean = The tables that use this FK, the table that stores the FK, the column where the FK can be found, the value
function cleanUpFK($toClean) {
    //print for debugging
    ?><br><?php
    for ($i = 0; $i < sizeof($toClean); $i ++) {
        echo print_r($toClean[$i]);
        ?><br><?php
    }

    //Go through each foreign key
    for ($i = 0; $i < sizeof($toClean); $i++) {
        //Check to see if the foreign key is 0
        if ($toClean[$i][3] != 0) {
            //Go through each of the tables
            for ($j = 0; $j < sizeof($toClean[$i][0]); $j++) {
                $query = "SELECT * FROM " . $toClean[$i][0][$j] . " WHERE " . $toClean[$i][0][$j] . $toClean[$i][2] . " = " . $toClean[$i][3];
                ?><br><?php
                echo $query;
            }
        } else {
            ?><br><?php
            echo "Skipped this one";
        }
    }
}

//Looks for any invalid values that the user may have entered. Takes education/project and an array of all the values
function findInvalid($values) {
    echo print_r($values);
    $gradeValid = false; //flag used to determine if the grade is valid
    ?><br><?php
    //echo "Check invalid for type: " . $type;
    ?><br><?php
    //echo "Check for values of array size: " . sizeof($values);
    ?><br><?php
    for ($i = 0; $i < sizeof($values); $i++) {
        ?><br><?php
//        echo $i . ": " . $values[$i][0] . ", " . $values[$i][1] . ", " . $values[$i][2] . ", " . $values[$i][3] . ", " . $values[$i][4];

        //Array used when checking the length of the string. Declared here to avoid multiple function calls
        $checkLenArray = checkLength($values[$i][0], $values[$i][3]);

        //Look for illegal characters
        if (containsIllegalCharacter($values[$i][0]) != null) {
            ?><br><?php

            //Set the cookie and redirect
            redirectWithError("Illegal character (" . containsIllegalCharacter($values[$i][0]) . "). For value: " . $values[$i][0], 'edit.php');

            //Check to see if the string is shorter that the maximum length allowed
        } else if ($checkLenArray[0] == false) {
            redirectWithError("Input too large. For value: " . $checkLenArray[2] . "(" . $checkLenArray[1] . "). Max length for field " . $values[$i][1] . " is " . $values[$i][3], 'edit.php');

            //Check to see if credits and grade are valid
        } else if (($values[$i][1] == 'credits' || $values[$i][1] == 'grade') && !$gradeValid) {
            ?><br><?php
            //echo "Looking at: " . $values[$i][1];
            ?><br><?php

            //Validating the grade and credits
            //First check to see if both fields are empty
            $creditsPos = findIn2dArray($values, 'credits');
            $gradePos = findIn2dArray($values, 'grade');

            //echo "Credits at: " . $creditsPos . " empty = " . isEmpty($values[$creditsPos][0]);
            ?><br><?php
            //echo "Grades at: " . $gradePos . " empty = " . isEmpty($values[$gradePos][0]);
            ?><br><?php

            //No grades have been entered
            if (isEmpty($values[$creditsPos][0]) && isEmpty($values[$gradePos][0])) {
                ?><br><?php
                redirectWithError("You must fill in at least one type of grade", 'edit.php');
                //die("Grade cannot be empty!");

                //Both grades have been entered
            } else if (!isEmpty($values[$creditsPos][0]) && !isEmpty($values[$gradePos][0])) {
                ?><br><?php
                redirectWithError("You can only enter one credits/grade value. For credits: " . $values[$creditsPos][0] . " and grade: " . $values[$gradePos][0], 'edit.php');
                //die("You can only enter one grade!");

                //Check to see if the entered grades match the specified types
            } else {
                if ($values[$i][1] == 'credits' && !isEmpty($values[$i][0])) {
                    //Check that the credits are numeric.
                    if (!isType($values[$i][2], $values[$i][0])) {
                        ?><br><?php
                        redirectWithError("Credits must be numeric. For credits: " . $values[$creditsPos][0], 'edit.php');
                        //die("Credits must be numeric");

                        //Check for any decimal points
                    } else if (strpos($values[$i][0], '.') !== false) {
                        ?><br><?php
                        redirectWithError("Credits must be a whole number: For credits: " . $values[$creditsPos][0], 'edit.php');
                        //die("Credits must be a whole number");

                        //Look for negative numbers
                    } else if ((int)$values[$i][0] < 0) {
                        ?><br><?php
                        redirectWithError("Credits cannot be negative. For credits: " . $values[$creditsPos][0], 'edit.php');
                        //die("Credits cannot be negative");

                        //Look for numbers that are too high
                    } else if ((int)$values[$i][0] > 50) {
                        ?><br><?php
                        redirectWithError("Credits cannot be greater than 50. For value: " . $values[$creditsPos][0], 'edit.php');
                        //die("That's too many credits!");
                    } else {
                        $gradeValid = true;
                    }
                } else if ($values[$i][1] == 'grade' && !isEmpty($values[$i][0])) {
                    $gpaGrades = ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'E', 'F'];
                    //echo "Checking grade";
                    //Check that the grades are not numeric
                    if (!isType($values[$i][2], $values[$i][0])) {
                        ?><br><?php
                        redirectWithError("GPA cannot be numeric. For value: " . $values[$gradePos][0], 'edit.php');
                        //die("Grades cannot be numeric");
                    } else if (!isMemberOf($values[$i][0], $gpaGrades)) {
                        ?><br><?php
                        //Compile the error message
                        $errorMsg = $gpaGrades[0];
                        for ($i = 1; $i < sizeof($gpaGrades); $i++) {
                            $errorMsg = $errorMsg . ", " . $gpaGrades[$i];
                        }
                        redirectWithError("Invalid GPA. For value: " . $values[$gradePos][0] . ". GPA grades must be one of the following... " . $errorMsg, 'edit.php');
                        //die("Gpa grades must be one of the following values: " . $errorMsg);
                    } else {
                        $gradeValid = true;
                    }
                }
            }

            //Test to see if the value is empty ignore if a valid grade has been entered
        } else if (isEmpty($values[$i][0]) && !$gradeValid && $values[$i][4] == false) {
            ?><br><?php
            redirectWithError("Value cannot be empty. For field: " . $values[$i][1],  'edit.php');
            //die("You cannot have an empty value!");

            //Look for values that do not match their specified type
        } else if (!isType($values[$i][2], $values[$i][0])) {
            ?><br><?php
            echo "Failed at: " . $i;
            ?><br><?php
            redirectWithError("Invalid type. For value: " . $values[$i][0] . ". " . $values[$i][1] . " should be of type " . $values[$i][2],  'edit.php');
            //die($values[$i][0] . ", " . $values[$i][1] . ", " . $values[$i][2] . ", " . $values[$i][3] . ", " . $values[$i][4]);
            //Error checking the year
        } else if ($values[$i][1] == 'subjectYear' || $values[$i][1] == 'newSubjectYear' || $values[$i][1] == 'exampleYear') {
            $currentYear = date('Y');
            $enteredYear = $values[$i][0];
            $yearDiff = abs($currentYear - $enteredYear);
            //Check for years in the future
            if ($enteredYear > $currentYear) {
                redirectWithError("Can you see the future? The year " . $enteredYear . " is " . $yearDiff . " year(s) from now.", 'edit.php');

                //Check for years too far into the past
            } else if ($enteredYear < ($currentYear - 100)) {
                redirectWithError("More than a lifetime ago. The year " . $enteredYear . " occurred " . $yearDiff . " year(s) ago. Are you a time traveler?", 'edit.php');
            }
        }
    }

    echo "No errors were found!";
}

//Update records for education and examples. Takes an array of values and uses them to insert a record.
function insertValues(array $toInsert, mysqli $con, $tableToUpdate)
{

//Loop through the array updating the values so that they can easily be used to generate SQL
    for ($i = 0; $i < sizeof($toInsert); $i++) {
        ?><br><?php
        ?><br><?php
        //If the value is null and an FK set a default value
        if ($toInsert[$i][1] == 'NULL' && strpos($toInsert[$i][0], 'FK')) {
            $toInsert[$i][1] = 0;

            //Check to see if the field should contain a foreign key.
        } else if (strpos($toInsert[$i][0], 'FK')) {
//            ?><!--<br>--><?php
//            echo "Get FK for field: " . $toInsert[$i][0] . " value: " . $toInsert[$i][1];

            //Run a query to check if the value already exists in a linked table. Look for null value if necessary
            $selectQuery = "SELECT * FROM " . $toInsert[$i][2] . " WHERE " . $toInsert[$i][2] . "." . $toInsert[$i][2] . " = '" . $toInsert[$i][1] . "'";
//            ?><!--<br>--><?php
//            echo "Select Query: " . $selectQuery;

            if (recordCount($selectQuery, $con) == 0) {
                //The record does not exist. Create it
                ?><br><?php
                //When inserting the FK has to be converted to PK

                echo "Insert Query: " . "INSERT INTO " . $toInsert[$i][2] . " (" . $toInsert[$i][2] . "PK, " . $toInsert[$i][2] . ") VALUES (NULL , '" . $toInsert[$i][1] . "')";
                runQuery("INSERT INTO " . $toInsert[$i][2] . " (" . $toInsert[$i][2] . "PK, " . $toInsert[$i][2] . ") VALUES (NULL , '" . $toInsert[$i][1] . "')", $con);
            }
            //Run a query to get the primary key of the value
            $key = getSingleVal($selectQuery, $toInsert[$i][2] . "PK", $con);

            //If the key is empty set it to default to 0
            if (empty($key)) {
                $key = 0;
            }
            echo "The key is: " . $key;

            //Update the value in the insert array
            $toInsert[$i][1] = $key;
        }
    }

    ?><br><?php
    ?><br><?php
    ?><br><?php
    echo "Insert array";
    //Print for debugging
//    for ($i = 0; $i < sizeof($toInsert); $i++) {
//        ?><!--<br>--><?php
//        echo $toInsert[$i][0] . ", " . $toInsert[$i][1];
//    }

    //Generate the insert statement
   $insert = "INSERT INTO " . $tableToUpdate . " (uniqueKey, ";
   $values = "VALUES (NULL, ";
   $duplicateSelect = "SELECT * FROM " . $tableToUpdate;
   $duplicateWhere = " WHERE ";

    echo "Size: " . sizeof($toInsert);

    for ($i = 0; $i < sizeof($toInsert); $i++) {
       //Generate the fields
       $insert = $insert . $toInsert[$i][0];
       if ($toInsert[$i][1] == "NULL") {
           $values = $values  . $toInsert[$i][1];
           $duplicateWhere = $duplicateWhere . $toInsert[$i][0] . " = " . $toInsert[$i][1];
       } else {
           $values = $values . "'" . $toInsert[$i][1] . "'";
           $duplicateWhere = $duplicateWhere . $toInsert[$i][0] . " = '" . $toInsert[$i][1] . "'";
       }
       if ($i != sizeof($toInsert) - 1) {
           $insert = $insert . ", ";
           $values = $values . ", ";

           $duplicateWhere = $duplicateWhere . " AND ";
       } else {
           $insert = $insert . ") ";
           $values = $values . ") ";
       }
   }

    //Look for duplicates
    $duplicateQuery = $duplicateSelect . $duplicateWhere;
    if (findDuplicate($duplicateQuery, $con) != 0) {
        redirectWithError("Record already exists", 'edit.php');
    }

    $query = $insert . $values;

    //Execute the query
    runQuery($query, $con);
}

//Update records for education and examples. Takes an array of values and uses them to update the appropriate record
function updateValues(array $toInsert, mysqli $con, $tableToUpdate, $uniqueKey)
{
//Loop through the array updating the values so that they can easily be used to generate SQL
    for ($i = 0; $i < sizeof($toInsert); $i++) {
        ?><br><?php
        ?><br><?php
        //Check to see if the field should contain a foreign key
        if (strpos($toInsert[$i][0], 'FK')) {
//            ?><!--<br>--><?php
//            echo "Get FK for field: " . $toInsert[$i][0] . " value: " . $toInsert[$i][1];

            //Run a query to check if the value already exists in a linked table
            $selectQuery = "SELECT * FROM " . $toInsert[$i][2] . " WHERE " . $toInsert[$i][2] . "." . $toInsert[$i][2] . " = '" . $toInsert[$i][1] . "'";
//            ?><!--<br>--><?php
//            echo "Select Query: " . $selectQuery;

            if (recordCount($selectQuery, $con) == 0) {
                //The record does not exist. Create it
                ?><br><?php
                //When inserting the FK has to be converted to PK

                echo "Insert Query: " . "INSERT INTO " . $toInsert[$i][2] . " (" . $toInsert[$i][2] . "PK, " . $toInsert[$i][2] . ") VALUES (NULL , '" . $toInsert[$i][1] . "')";
                runQuery("INSERT INTO " . $toInsert[$i][2] . " (" . $toInsert[$i][2] . "PK, " . $toInsert[$i][2] . ") VALUES (NULL , '" . $toInsert[$i][1] . "')", $con);
            }
            //Run a query to get the primary key of the value
            $key = getSingleVal($selectQuery, $toInsert[$i][2] . "PK", $con);
            //echo "The key is: " . $key;

            //Update the value in the insert array
            $toInsert[$i][1] = $key;
        } else {
            //Fields that are not foreign keys may be duplicates. Check the database and alert the user
            $query = "SELECT * FROM " . $tableToUpdate . " WHERE " . $toInsert[$i][0] . " = '" . $toInsert[$i][1] . "' AND " . $tableToUpdate . ".uniqueKey != " . $uniqueKey;

            if (findDuplicate($query, $con) != 0) {
                ?><br><?php
                redirectWithError("The value '" . $toInsert[$i][1] . "' already exists for field '" . $toInsert[$i][0] . "'", 'edit.php');
            }
        }
    }

    ?><br><?php
    ?><br><?php
    ?><br><?php
    echo "Insert array";
    //Print for debugging
//    for ($i = 0; $i < sizeof($toInsert); $i++) {
//        ?><!--<br>--><?php
//        echo $toInsert[$i][0] . ", " . $toInsert[$i][1];
//    }

    //Generate the update statement
    $query = "UPDATE " . $tableToUpdate . " SET ";
    for ($i = 0; $i < sizeof($toInsert); $i++) {
        //do not add quotes for null
        if ($toInsert[$i][1] == "NULL") {
            $query = $query . $toInsert[$i][0] . " = " . $toInsert[$i][1];
        } else {
            $query = $query . $toInsert[$i][0] . " = '" . $toInsert[$i][1] . "'";
        }

        //Add a comma if not the last value
        if ($i != sizeof($toInsert) - 1) {
            $query = $query . ", ";
        }
    }

    //Add the where clause
    $query = $query . " WHERE " . $tableToUpdate . ".uniqueKey = " . $uniqueKey;

//    ?><!--<br>--><?php
//    echo "Update Query: " . $query;

    //Execute the query
    runQuery($query, $con);
}

//Converts $_FILES to a cleaner array when uploading multiple files
function reArrayFiles($file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return$file_ary;
}

//Same as print_r surrounded with <pre></pre> HTML tags for better array readability
function pre_r($array) {
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

//Takes a query and returns a single value
function getSingleVal($query, $colName, $con) {
    $primary = $val = null;
//    echo "Single val query: " . $query;
    $query = $con->prepare($query);
    $query->execute();
    $query->bind_result($primary, $val);
    $query->store_result();
    $key = "";
    while ($row = $query->fetch()) {
        ?><br><?php
        $key = $primary;
    }
    return $key;
}

//Redirects the user to the specified page with an error cookie set.
function redirectWithError($cookieValue, $redirectTo) {
    //Add a flag to the front of the cookie describing what it is
    $cookieValue = "ERROR: " . $cookieValue;

    //echo "called redirect";

    //Set the cookie
    setcookie('errorMsg', $cookieValue);

    //Redirect
    header("location: " . $redirectTo);

    //Stop all execution
    exit();
}

function redirectWithSuccess($cookieValue, $redirectTo) {
    //Add a flag to the front of the cookie describing what it is
    $cookieValue = "Success: " . $cookieValue;

    //Set the cookie
    setcookie('successMsg', $cookieValue);

    //Redirect
    header("location: " . $redirectTo);

    //Stop all execution
    exit();
}

//Checks the maximum length of a value. returns an array with a true/false flag, the values length and a shortened version of the value
function checkLength($value, $maxLen) {
    if (strlen($value) > $maxLen) {
        return [false, strlen($value), trimString($value, $maxLen)];
    }
    return [true];
}

//shortens a value to 40 chars including (...). Returns the result.
function trimString($value) {
    return substr($value, 0, 37) . "...";
}

//Checks to see if a value is a member of the supplied values. returns true if it is.
function isMemberOf($value, $parameters) {
    for ($i = 0; $i < sizeof($parameters); $i++) {
        if (strpos($parameters[$i], $value) !== false) {
            return true;
        }
    }

    return false;
}

//Checks to see if a value is of the specified type. Returns true if it is, false otherwise
function isType($type, $value) {
    ?><br><?php
    //echo "Checking type: " . $type;

    //Checking for strings that should be an int
    if ($type == 'int') {
        //Remove any spaces
        if (is_numeric($value)) {
            return true;
        }

        //checking for strings that should not be an int
    } else if ($type == 'string') {
        if (!is_numeric($value)) {
            return true;
        }
    }
    return false;
}

//Takes a value and checks if it contains any illegal characters mark of any kind. Returns the illegal character if it does, true if it does not
function containsIllegalCharacter($value) {
    ////echo " Checking value " . $value . " for illegal characters";
    $characters = ['“', '”', '"', '"', '‘', '’', "'", "'", '«', '»', '「', '」'];
    for ($i = 0; $i < sizeof($characters); $i++) {
        //Check if the character is contained in the value
        if (strpos($value, $characters[$i]) !== false) {
            ////echo " contains: " . $characters[$i];
            return $characters[$i];
        }
    }

    //Return false
    return null;
}

//Takes a value and checks to see if it is empty. Return true if it is
function isEmpty($value) {
    if (sizeof($value) == 0 || $value == "") {
        return true;
    }
    return false;
}

//Takes a 2d array and returns the position of a specified value
function findIn2dArray($array, $value) {
    //Iterate over the primary array
    for ($i = 0; $i < sizeof($array); $i++) {
        //Iterate over the secondary array
        for ($j = 0; $j < sizeof($array[$i]); $j++) {
            if ($array[$i][$j] == $value) {
                return $i;
            }
        }
    }
    return false;
}

//Updates the specified value in the specified table
function updateTableValue($table, $column, $value, $conditionalColumn, $key, $con)
{
    $query = "UPDATE " . $table . " SET " . $column . " = '" . $value . "' WHERE " . $conditionalColumn . " = " . $key;
    //echo "Update column query: " . $query;

    $query = $con->prepare($query);
    $query->execute();
}

//Run a specified query
function runQuery($query, $con)
{
    ?><br><?php
    echo "Run query";
    ?><br><?php
    echo $query;

    //execute the query
    $newRecordQuery = $con->prepare($query);
    $newRecordQuery->execute();
}

//Executes and returns a query so that the variables can be stored and bound as required
function runAndReturn($query, $con) {
    $query = $con->prepare($query);
    $query->execute();
    return $query;
}

//Get the number of times that a foreign key is used in the education table
function numTimesFkUsedEducation($query, $con) {
    //Declare values
    $uniqueKey = $institutionFK = $subject = $gradeFK = $subjectLevelFK = $yearFk = $subjectCodeFK = $creditsFK = $creditsFK = $codeExtensionFK = null;

    $query = runAndReturn($query, $con);
    $query->bind_result($uniqueKey, $institutionFK, $subject, $gradeFK, $subjectLevelFK, $yearFk, $subjectCodeFK, $creditsFK, $codeExtensionFK);
    $query->store_result();
    return $query->num_rows();
}

//Checks if a record exists in a linked table. Returns the number of records
function recordCount($query, $con)
{
    ?><br><?php
    //echo $query;
    $key = "";
    $result = "";
    $query = $con->prepare($query);
    $query->execute();
    $query->bind_result($key, $result);
    $query->store_result();
    $recordCount = $query->num_rows();
    return $recordCount;
}

//Looks for duplicate records in the given table. Takes a the table name. An array of fields and and array of values.
//Returns true if a duplicate is found
function findDuplicate($query, $con)
{
//    ?><!--<br>--><?php
//    echo "Duplicate query: " . $query;

    //Run the query and get the number of rows
    $recordCount = 0;
    foreach ($con->query($query) as $row) {
        $recordCount++;
    }
//    ?><!--<br>--><?php
//    echo "Number of rows: " . $recordCount;
    return $recordCount;
}


//Function that executes a query and returns the foreign key
function getFK($table, $value, $con)
{
    //echo "Value: " . $value;
    ?><br><?php
    $primary = null;
    $val = null;
    $foreignKeyQuery = "SELECT * FROM " . $table . " WHERE " . $table . "." . $table . " = '" . $value . "'";
    echo "FK QUERY: " . $foreignKeyQuery;
    $foreignKeyQuery = $con->prepare($foreignKeyQuery);
    $foreignKeyQuery->execute();
    $foreignKeyQuery->bind_result($primary, $val);
    $foreignKeyQuery->store_result();
    $key = "";
    while ($row = $foreignKeyQuery->fetch()) {
        ?><br><?php
        $key = $primary;
    }
    ?><br><?php
    //echo "FK: " . $key;
    ?><br><?php
    return $key;
}

//Takes a value, strips the spaces and returns it
function stripSpaces($value) {
    return str_replace(' ', '', $value);
}

//Takes an image file and adjusts the size
function resize_image($file, $w, $h, $crop=true) {
    ?><br><?php
    echo "resizing: " . $file;
    list($width, $height) = getimagesize($file);
    ?><br><?php
    echo "W & H: " . print_r(getimagesize($file));
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }

    //Set the image type
    $src = null;
    if (strpos($file, '.jpeg')) {
        $src = imagecreatefromjpeg($file);
    } else if (strpos($file, '.png')) {
        $src = imagecreatefrompng($file);
    } else if (strpos($file, '.gif')) {
        $src = imagecreatefromgif($file);
    } else if (strpos($file, '.jpg')) {
        $src = imagecreatefromjpeg($file);
    }

    ?><br><?php
    echo "SRC: " . $src;

    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    return $dst;
}

//Manual redirect.
//todo add an auto redirect if the user enters the url manually
?>
    <p>
        Click <a href="edit.php">here</a> to go back to the edit page.
    </p>
<?php

//redirect back to the previous page
//header("Location: edit.php");
?>