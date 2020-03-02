<?php /** @noinspection ALL */

//Check if the user is logged in
require("functions.php");
if (!isset($_SESSION['loggedin'])) {
    //Set the logged in flag
    $loggedIn = false;

} else {
    $loggedIn = true;
    require("connect.php");
}

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

    //Error check all the values. Include each value, the field name, a flag of the expected type in the array and the max length.
    findInvalid([[$postedInstitution, 'institution', 'string', 40, false],
        [$postedSubject, 'subject', 'string', 100, false],
        [$postedSubjectYear, 'subjectYear', 'int', 4, false],
        [$postedSubjectLevel, 'subjectLevel', 'string', 20, false],
        [$postedCode, 'subjectCode', 'string', 10, false],
        [$postedCodeExtension, 'codeExtension', 'int', 4, false],
        [$postedCredits, 'credits', 'int', 2, false],
        [$postedGrade, 'grade', 'string', 2, false]]);

    //Pass in the linked table, column and value
    $toInsert = [['institution', 'institutionFK', $postedInstitution],
        [null, 'subject', $postedSubject],
        ['grade', 'gradeFK', $postedGrade],
        ['subjectLevel', 'subjectLevelFK', $postedSubjectLevel],
        ['year', 'yearFK', $postedSubjectYear],
        ['subjectCode', 'subjectCodeFK', $postedCode],
        ['credits', 'creditsFK', $postedCredits],
        ['codeExtension', 'codeExtensionFK', $postedCodeExtension]];

    //Set any empty values to null
    for ($i = 0; $i < sizeof($toInsert); $i++) {
        if (isEmpty($toInsert[$i][2])) {
            $toInsert[$i][2] = Null;
        }
    }

    //Update the database if the user is logged in

    if ($loggedIn == true) {
        $toInsert = updateValues($toInsert, $con);


        //Build a select query to check for duplicates
        $query = "SELECT * FROM education WHERE ";
        for ($i = 0; $i < sizeof($toInsert); $i++) {
            $toAppend = "education." . $toInsert[$i][1] . " = '" . $toInsert[$i][2] . "'";
            if ($i != sizeof($toInsert) - 1) {
                $toAppend = $toAppend . " AND ";
            }
            $query = $query . $toAppend;
        }

        //Run the query
        $query = runAndReturn($query, $con);
        $recordCount = $query->num_rows();
        $query->close();

        //If there is a row returned. Throw duplicate error
        if ($recordCount > 0) {
            redirectWithError('Duplicate record', 'edit.php');
        }

        //Build the cleanup array
        $toClean = setCleanupEducation($uniqueKey, $con);

        //Generate the insert statement
        $insert = "UPDATE education SET ";


        for ($i = 0; $i < sizeof($toInsert); $i++) {

            if ($i != sizeof($toInsert) - 1) {
                $insert = $insert . $toInsert[$i][1] . " = '" . $toInsert[$i][2] . "', ";
            } else {
                $insert = $insert . $toInsert[$i][1] . " = '" . $toInsert[$i][2] . "'";
            }
        }

        $query = $insert . " WHERE education.uniqueKey = " . $uniqueKey;

        //Execute the query
        runQuery($query, $con);

        //Clean the database
        cleanUpFK($toClean, $con);
    } else {
        $location = findPlayAroundKeyLocation($uniqueKey, $_SESSION['playAroundEducation']);

        //Update the value in the session array
        $_SESSION['playAroundEducation'][$location] = [$uniqueKey, $postedInstitution, $postedSubject, $postedCode, $postedCodeExtension, $postedGrade, $postedCredits, $postedSubjectLevel, $postedSubjectYear];
    }

    //Redirect the user
    redirectWithSuccess("Record has been updated!", 'edit.php');
}

//Creating new education records
if (isset($_POST["newEducationRecord"])) {

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
        if (empty($toInsert[$i][1])) {
            $toInsert[$i][1] = 'NULL';
        }
    }


    if ($loggedIn == true) {
        //Run function to insert the record into the database
        insertValues($toInsert, $con, 'education');

    } else {
        //Generate a new unique key
        $uniqueKey = generatePlayAroundUniqueKey($_SESSION['playAroundEducation']);

        //Return error if null
        if (is_null($uniqueKey)) {
            redirectWithError('Failed to create record.', 'edit.php');
        }

        //Add the record to the session array
        $newArray = [$uniqueKey, $postedNewInstitution, $postedNewSubject, $postedNewSubjectCode, $postedNewCodeExtension, $postedNewGrade, $postedNewCredits, $postedNewSubjectLevel, $postedNewSubjectYear];
        array_push($_SESSION['playAroundEducation'], $newArray);
    }

    //Redirect the user
    redirectWithSuccess("Record has been created!", 'edit.php');

}

//Deleting education records
if (isset($_POST['deleteEducationRecord'])) {
    $uniqueKey = $_POST['uniqueKey'];

    //Delete from the database if the user is logged in
    if ($loggedIn == true) {
        //Perform a query to get the record to be deleted. This allows the foreign keys to be gathered so that their corresponding values can also be deleted if required
        $getRecordQuery = "SELECT * 
    FROM education
    WHERE education.uniqueKey = " . $_POST['uniqueKey'];


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
            $recordCount = numTimesFkUsedEducation("SELECT * FROM education WHERE education." . $tablesToCheckEducation[$i] . "FK = " . $keysToCheck[$i], $con);

            //If the record count is one it is safe to delete from the linked table
            if ($recordCount == 1) {
                runQuery("DELETE FROM " . $tablesToCheckEducation[$i] . " WHERE " . $tablesToCheckEducation[$i] . "PK = " . $keysToCheck[$i], $con);
            }
        }

        $getRecordQuery->close();

        //Delete the item in the education table
        runQuery("DELETE FROM education WHERE education.uniqueKey = " . $uniqueKey, $con);
    } else {
        $location = findPlayAroundKeyLocation($uniqueKey, $_SESSION['playAroundEducation']);
        array_splice($_SESSION['playAroundEducation'], $location, 1);
    }

    //Redirect the user
    redirectWithSuccess("Record has been deleted!", 'edit.php');

}

//Updates examples
if (isset($_POST['submitExampleUpdate'])) {
    $value = null;
    $foreignKeys = [];

    //looking for a checked update language and an empty update language entry
    if (!empty($_POST['updateLanguageInput']) && empty($_POST['updateLanguageEntry'])) {
        die("You checked 'other' but did not enter a update language");

        //Looking for a update language entry and an unchecked update language checkbox
    } else if (empty($_POST['updateLanguageInput']) && !empty($_POST['updateLanguageEntry'])) {
        die("You entered a language but did not check 'other'");

        //Looking for a checked github and no github link
    } else if (!empty($_POST['updateGithubInput']) && empty($_POST['updateGithubEntry'])) {
        die("You checked 'github' but did not enter a link");

        //Looking for a github link and no checked github
    } else if (empty($_POST['updateGithubInput']) && !empty($_POST['updateGithubEntry'])) {
        die("You entered a github link but did not check 'github'");

        //Looking for a checked link but no link provided
    } else if (!empty($_POST['updateLinkInput']) && empty($_POST['updateLinkEntry'])) {
        die("You checked 'link' and did not enter a link");

        //Looking for link and no check link
    } else if (empty($_POST['updateLinkInput']) && !empty($_POST['updateLinkEntry'])) {
        die("You entered a link but did not check 'link'");
    }

    //Validate the links
    validateLink($_POST['updateLinkEntry']);
    validateLink($_POST['updateGithubEntry']);

    //Get all the posted variables
    $postedExampleName = $_POST['exampleName'];
    $postedExampleYear = $_POST['exampleYear'];
    $postedExampleLink = $_POST['updateLinkEntry'];
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

    $languageArray = [];


    if ($loggedIn == true) {
        //Get all the languages currently stored in the database
        $value = null;
        $query = $con->prepare("SELECT languages.languages FROM languages WHERE languages != ''");
        $query->execute();
        $query->bind_result($value);
        $query->store_result();

        while ($row = $query->fetch()) {
            array_push($languageArray, $value);
        }

        $query->close();
    } else {
        $languageArray = $_SESSION['sessionLanguages'];
    }

    $languagesUsed = []; //Array of languages that the user wants to add

    if ($_POST['updateLanguageEntry'] != "") {
        //Start the language counter at 1
        $languageCount = 1;

        //Add the language to the language array
        array_push($languagesUsed, $_POST['updateLanguageEntry']);
    } else {
        $languageCount = 0;
    }

    //Checking to see which of the languages ued in the database are requested by the user
    for ($i = 0; $i < sizeof($languageArray); $i++) {
        if (isset($_POST[str_replace(' ', '_', $languageArray[$i])]) && $_POST[str_replace(' ', '_', $languageArray[$i])] == $languageArray[$i]) {
            //Increment the counter and add the language to the used array
            $languageCount++;
            array_push($languagesUsed, $languageArray[$i]);

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

    if ($loggedIn == true) {

        //Check the database to see if the name already exists.
        if (findDuplicate("SELECT * FROM examples WHERE name = '" . $postedExampleName . "' AND examples.uniqueKey != " . $uniqueKey, $con)) {
            redirectWithError('Duplicate record', 'edit.php');
        }

        $originalDirectory = getExampleDirectory($uniqueKey, $con);

        //2d array of the fields, values and their linked tables (if required) to be inserted
        $toInsert = [[null, 'name', $postedExampleName],
            ['year', 'yearFK', $postedExampleYear],
            [null, 'description', $postedExampleDescription],
            [null, 'link', $postedExampleLink],
            [null, 'github', $postedExampleGithub]];


        //Add the languages to the insert array
        for ($i = 0; $i < 5; $i++) {
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

            if ($i < sizeof($languagesUsed)) {
                //Add to the to insert array
                array_push($toInsert, ['languages', 'language' . $langNum . 'FK', $languagesUsed[$i]]);
            } else {
                array_push($toInsert, ['languages', 'language' . $langNum . 'FK', null]);
            }
        }

        //Set any empty values to null
        for ($i = 0; $i < sizeof($toInsert); $i++) {
            if (isEmpty($toInsert[$i][2])) {
                $toInsert[$i][2] = Null;
            }
        }

        $toInsert = updateValues($toInsert, $con);

        //Generate the insert statement
        $insert = "UPDATE examples SET ";


        for ($i = 0; $i < sizeof($toInsert); $i++) {

            if ($i != sizeof($toInsert) - 1) {
                $insert = $insert . $toInsert[$i][1] . " = '" . $toInsert[$i][2] . "', ";
            } else {
                $insert = $insert . $toInsert[$i][1] . " = '" . $toInsert[$i][2] . "'";
            }
        }

        $query = $insert . " WHERE examples.uniqueKey = " . $uniqueKey;

        //Execute the query
        runQuery($query, $con);

        //Build the cleanup array
        $toClean = setCleanupExamples($uniqueKey, $con);

        //Clean the database
        cleanUpFK($toClean, $con);

        //Rename the directory where the images are stored. Done here so that any errors are caught before the directory is renamed
        if (!is_null($originalDirectory)) {
            //Calculate the new directory
            $newDirectory = stripSpaces($postedExampleName);
            $newDirectory = 'images/examples/' . $newDirectory;

            //If the old directory exists rename it
            if (is_dir($originalDirectory)) {
                rename($originalDirectory, $newDirectory);
            } else {
                //make a new directory
                mkdir($newDirectory);
            }
        }
    } else {
        $location = findPlayAroundKeyLocation($uniqueKey, $_SESSION['playAroundExamples']);

        //Update the value in the session array (reference the previous version of this record because the images do not change here)
        $_SESSION['playAroundExamples'][$location] = [$uniqueKey, $_SESSION['playAroundExamples'][$location][1], $postedExampleName, $languagesUsed, $postedExampleLink,
            $postedExampleGithub, $postedExampleDescription, $postedExampleYear];
    }

    //Redirect the user
    redirectWithSuccess("Record has been updated!", 'edit.php');

}

//Delete examples
if (isset($_POST['deleteExample'])) {
    $uniqueKey = $_POST['uniqueKey'];

    if ($loggedIn == true) {
        //Get the path to where the images are stored
        $originalDirectory = getExampleDirectory($uniqueKey, $con);

        //Delete the images
        if (!is_null($originalDirectory)) {
            deleteDirectory($originalDirectory);
        }

        //Delete the record
        $query = "DELETE FROM examples WHERE examples.uniqueKey = " . $uniqueKey;
        runQuery($query, $con);

        //Setup the array that will be used when cleaning the database
        $toClean = setCleanupExamples($uniqueKey, $con);

        cleanUpFK($toClean, $con);
    } else {
        //Get the location
        $location = findPlayAroundKeyLocation($uniqueKey, $_SESSION['playAroundExamples']);

        //Remove from the array
        array_splice($_SESSION['playAroundExamples'], $location, 1);
    }
    redirectWithSuccess('Record deleted!', 'edit.php');
}

//Adds images
if (isset($_POST['addImages'])) {
    $uniqueKey = $_POST['uniqueKey'];

    if ($loggedIn == true) {
        //Handle file uploads
        //Check a file has been uploaded in the form
        if (isset($_FILES['updateImages']) && $_FILES['updateImages']['name'][0] != "") {
            //Calculate the directory
            $directory = getExampleDirectory($uniqueKey, $con);

            $imageUpload = uploadImages($directory, $_FILES['updateImages']);

            //Return the error if necessary
            if ($imageUpload != true) {
                redirectWithError($imageUpload, 'edit.php');
            }
        }
    } else {
        //Get all of the possible images
        $path    = 'Images/userImages';
        $files = scandir($path);
        $files = array_diff(scandir($path), array('.', '..'));

        //Add to the images array
        foreach ($files as $image) {
            if (strpos($image, '.jpeg')) {
                array_push($_SESSION['sessionImages'], "Images/userImages/" . $image);
            }
        }

        //Check which images have been submitted
        foreach ($files as $image) {
            $image = "Images/userImages/" . $image;
            if (isset($_POST[str_replace('.', '_', $image)])) {
                $location = findPlayAroundKeyLocation($uniqueKey, $_SESSION['playAroundExamples']);

                //Push to the examples array
                array_push($_SESSION['playAroundExamples'][$location][1], str_replace('_', '.', $image));
            }
        }
    }
    redirectWithSuccess('Images have been uploaded.', 'edit.php');

}

//Deletes single images
if (isset($_POST['deleteImage'])) {
    //Get the expected filepath. This is used to help verify that the user has not tampered with the file to be deleted
    $originalDirectory = getExampleDirectory($_POST['uniqueKey'], $con);

    //Make sure that the image path is valid
    if (strpos($_POST['file'], $originalDirectory) !== false && isImage($_POST['file'])) {
        //Delete the image
        unlink($_POST['file']);
    } else {
        //The filepath is invalid
        redirectWithError('Invalid filepath!', 'edit.php');
    }

    redirectWithSuccess('Image deleted.', 'edit.php');
}

//Creates new examples
if (isset($_POST['newExampleRecord'])) {
    $value = null;
    $foreignKeys = [];

    //looking for a checked new language and an empty new language entry
    if (!empty($_POST['newLanguageInput']) && empty($_POST['newLanguageEntry'])) {
        redirectWithError("You checked 'other' but did not enter a new language", 'edit.php');

        //Looking for a new language entry and an unchecked new language checkbox
    } else if (empty($_POST['newLanguageInput']) && !empty($_POST['newLanguageEntry'])) {
        redirectWithError("You entered a new language but did not check 'other'", 'edit.php');

        //Looking for a checked github and no github link
    } else if (!empty($_POST['newGithubInput']) && empty($_POST['newGithubEntry'])) {
        redirectWithError("You checked 'github' but did not enter a link", 'edit.php');

        //Looking for a github link and no checked github
    } else if (empty($_POST['newGithubInput']) && !empty($_POST['newGithubEntry'])) {
        redirectWithError("You entered a new github link but did not check 'github'", 'edit.php');

        //Looking for a checked link but no link provided
    } else if (!empty($_POST['newLinkInput']) && empty($_POST['newLinkEntry'])) {
        redirectWithError("You checked 'link' and did not enter a link", 'edit.php');

        //Looking for link and no check link
    } else if (empty($_POST['newLinkInput']) && !empty($_POST['newLinkEntry'])) {
        redirectWithError("You entered a new link but did not check 'link'", 'edit.php');
    }

    //Validate the links
    validateLink($_POST['newLinkEntry']);
    validateLink($_POST['newGithubEntry']);


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

    $languagesUsed = []; //Array of languages that the user wants to add

    //Perform language checks in the database
    if ($loggedIn == true) {
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

        $query->close();

    } else {
        $languageArray = $_SESSION['sessionLanguages'];
    }

    if ($_POST['newLanguageEntry'] != "") {
        //Start the language counter at 1
        $languageCount = 1;

        //Add the language to the language array
        array_push($languagesUsed, $_POST['newLanguageEntry']);
    } else {
        $languageCount = 0;
    }

    //Checking to see which of the languages ued in the database are requested by the user
    for ($i = 0; $i < sizeof($languageArray); $i++) {
        if (isset($_POST[str_replace(' ', '_', $languageArray[$i])]) && $_POST[str_replace(' ', '_', $languageArray[$i])] == $languageArray[$i]) {
            //Increment the counter and add the language to the used array
            $languageCount++;
            array_push($languagesUsed, $languageArray[$i]);

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

    //Add to the database if the user is logged in
    if ($loggedIn == true) {
        //2d array of the fields, values and their linked tables (if required) to be inserted
        $toInsert = [['name', $postedNewExampleName],
            ['yearFK', $postedNewExampleYear, 'year'],
            ['description', $postedNewExampleDescription],
            ['link', $postedNewExampleLink],
            ['github', $postedNewExampleGithub]];

        //If the language array is < 5 add default values
        if (sizeof($languagesUsed) < 5) {
            for ($i = sizeof($languagesUsed); $i < 5; $i++) {
                //push the default null value to the array
                array_push($languagesUsed, "NULL");
            }
        }


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

        //Insert the record
        insertValues($toInsert, $con, 'examples');
    }

    //A flag used to explain that the record was created but something went wrong with the images
    $recordCreated = "The record was created but... ";
    //die(var_dump($_FILES['userFiles']));

    //Handle file uploads if the user is logged if
    if ($loggedIn == true) {
        //Check a file has been uploaded in the form
        if (isset($_FILES['addImages']) && $_FILES['addImages']['name'][0] != "") {
            //Calculate the directory
            $directory = "images/examples/" . stripSpaces($postedNewExampleName);

            $imageUpload = uploadImages($directory, $_FILES['addImages']);

            //Return the error if necessary
            if ($imageUpload != true) {
                redirectWithError($recordCreated . " " . $imageUpload, 'edit.php');
            }
        }
    } else {
        $imageArray = []; // Array of images that the user has selected

        //Get all of the possible images
        $path    = 'Images/userImages';
        $files = scandir($path);
        $files = array_diff(scandir($path), array('.', '..'));

        //Add to the images array
        foreach ($files as $image) {
            if (strpos($image, '.jpeg')) {
                array_push($_SESSION['sessionImages'], "Images/userImages/" . $image);
            }
        }

        //Check which images have been submitted
        foreach ($files as $image) {
            $image = "Images/userImages/" . $image;
            if (isset($_POST[str_replace('.', '_', $image)])) {
                array_push($imageArray, $image);
            }
        }

        //Set the new value
        $key = generatePlayAroundUniqueKey($_SESSION['playAroundExamples']);

        //Return error if null
        if (is_null($uniqueKey)) {
            redirectWithError('Failed to create record', 'edit.php');
        }

        $newArray = [$key, $imageArray, $postedNewExampleName, $languagesUsed, $postedNewExampleLink, $postedNewExampleGithub, $postedNewExampleDescription, $postedNewExampleYear];
        array_push($_SESSION['playAroundExamples'], $newArray);
    }

    //Print a success message
    redirectWithSuccess("The record was created and images uploaded (if any).", 'edit.php');
}

//Manual redirect.
header("refresh:5;url=index.php");
?>
<p>You entered the url manually. You should be redirected to the home page in 5 seconds. Otherwise you can click the
    link below</p>

<p>
    Click <a href="edit.php">here</a> to go back to the edit page.
</p>
