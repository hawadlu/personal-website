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


    //Call function to update the values
    updateValues($toInsert, $con, 'education', $uniqueKey);
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
            [$postedNewGrade, 'grade', 'string', 2], false]);

    //Values that will be inserted into the database
    $valuesToCheck = [$postedNewInstitution, $postedNewSubjectLevel, $postedNewSubjectYear, $postedNewSubjectCode, $postedNewCodeExtension, $postedNewGrade];
    $foreignKeys = [];

    //Used when inserting the grade and credits foreign keys
    $gradeFK = 0;
    $creditsFK = 0;

    //Update the grade to be inserted if required
    if ($postedNewGrade != "") {
        $tablesToCheckEducation[5] = "grade";
    } else if ($postedNewCredits != "") {
        $tablesToCheckEducation[5] = "credits";
        $valuesToCheck[5] = $postedNewCredits;
    } else {
        die("There was an error. Invalid grade input");
    }

    ?><br><?php
    //echo print_r($valuesToCheck);
    ?><br><?php
    //echo print_r($tablesToCheckEducation);
    ?><br><?php


    //This code iterates over each table looking for the corresponding foreign key. If it cannot find it is added to the DB
    for ($i = 0; $i < sizeof($tablesToCheckEducation); $i++) {
        //perform a query to check if the record exists
        ?><br><?php
        //echo "Looking for records";
        ?><br><?php
        $recordCount = recordExistsLinked("SELECT * FROM " . $tablesToCheckEducation[$i] . " WHERE " . $tablesToCheckEducation[$i] . " = '" . $valuesToCheck[$i] . "'", $con);

        //The record does not exist. Create a new record
        if ($recordCount == 0) {
            ?><br><?php
            //echo "New Record required for value " . $valuesToCheck[$i];
            ?><br><?php

            //Create the new record
            //echo "Inserting new statement: ";
            ?><br><?php
            runQuery("INSERT INTO " . $tablesToCheckEducation[$i] . " (" . $tablesToCheckEducation[$i] . "PK, " . $tablesToCheckEducation[$i] . ") VALUES (NULL, '" . $valuesToCheck[$i] . "')", $con);


            ?><br><?php
            //echo "Getting FK for table value: " . $i . " Value: " . $valuesToCheck[$i];
            ?><br><?php

            //Save the foreign key
            $key = getFK($tablesToCheckEducation[$i], $valuesToCheck[$i], $con);
            array_push($foreignKeys, $key);

            //Update the grade/credits FK if required
            if ($tablesToCheckEducation[$i] == "grade") {
                $gradeFK = $key;
            } else if ($tablesToCheckEducation[$i] == "credits") {
                $creditsFK = $key;
            }

            //The record already exists
        } else {
            ?><br><?php
            //echo "Record already exists. Updating fields for " . $tablesToCheckEducation[$i];
            ?><br><?php

            //Get the new foreign key
            //echo "Getting FK for table value: " . $i . " Value: " . $tablesToCheckEducation[$i];
            ?><br><?php

            //Save the foreign key
            $key = getFK($tablesToCheckEducation[$i], $valuesToCheck[$i], $con);
            array_push($foreignKeys, $key);

            //Update the grade/credits FK if required
            if ($tablesToCheckEducation[$i] == "grade") {
                $gradeFK = $key;
            } else if ($tablesToCheckEducation[$i] == "credits") {
                $creditsFK = $key;
            }
        }
    }

    ?><br><?php
    //echo "Foreign keys: " . print_r($foreignKeys);

    //Create the new record
    //echo "grade: " . $gradeFK;
    ?><br><?php

    //Check to see if a duplicate record exists
    $values = [$foreignKeys[0], $postedNewSubject, $gradeFK, $foreignKeys[1], $foreignKeys[2], $foreignKeys[3], $creditsFK, $foreignKeys[4]];
    if (!findDuplicate('education', $tableColumnsEducation, $values, $con)) {
        //echo "No duplicates found";

        //Create the new record
        runQuery("INSERT INTO education (uniqueKey, institutionFK, subject, gradeFk, subjectLevelFK, yearFK, subjectCodeFK, creditsFK, codeExtensionFK)
        VALUES (NULL, " . $foreignKeys[0] . ", '" . $postedNewSubject . "', " . $gradeFK . ", " . $foreignKeys[1] . ",
    " . $foreignKeys[2] . ", " . $foreignKeys[3] . ", " . $creditsFK . ", " . $foreignKeys[4] . ")", $con);
    } else {
        //echo "Duplicates found!";

        //todo clean all previous FK's that may have been created
        redirectWithError('Cannot enter duplicate record', 'edit.php');
    }

    //The new record has successfully been created. Send a success message
    redirectWithSuccess('New record created', 'edit.php');

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

}

if (isset($_POST['submitExampleUpdate'])) {
    echo "Updating example";
    echo "<br>";
    echo var_dump($_POST);
    $value = null;
    $foreignKeys = [];

    //Get all the posted variables
    $postedExampleName = $_POST['exampleName'];
    $postedExampleYear = $_POST['exampleYear'];
    $postedExampleLink = $_POST['exampleLink'];
    echo "Empty: " . empty($_POST['exampleLink']);
    $postedExampleGithub = $_POST['exampleGithub'];
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

    if ($_POST['newLanguage'] != "") {
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

    //error check
    findInvalid($invalidArray);

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
    for ($i = 0; $i < sizeof($toInsert); $i++) {
        ?><br><?php
        echo $toInsert[$i][0] . ", " . $toInsert[$i][1];
    }

    //Run a function to update all of the necessary values
    updateValues($toInsert, $con, 'examples', $uniqueKey);
}

//Deletes images
if (isset($_POST['deleteImage'])) {
    echo "Delete image: " . $_POST['file'];
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
        echo $i . ": " . $values[$i][0] . ", " . $values[$i][1] . ", " . $values[$i][2] . ", " . $values[$i][3] . ", " . $values[$i][4];

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
//            ?><!--<br>--><?php
            //echo "Failed at: " . $i;
//            ?><!--<br>--><?php
            redirectWithError("Invalid type. For value: " . $values[$i][0] . ". " . $values[$i][1] . " should be of type " . $values[$i][2],  'edit.php');
            e($values[$i][0] . ", " . $values[$i][1] . ", " . $values[$i][2] . ", " . $values[$i][3] . ", " . $values[$i][4]);
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

//Update examples records
function updateValues(array $toInsert, mysqli $con, $tableToUpdate, $uniqueKey)
{
//Loop through the array updating the values so that they can easily be used to generate SQL
    for ($i = 0; $i < sizeof($toInsert); $i++) {
        ?><br><?php
        ?><br><?php
        //Check to see if the field should contain a foreign key
        if (strpos($toInsert[$i][0], 'FK')) {
            ?><br><?php
            echo "Get FK for field: " . $toInsert[$i][0] . " value: " . $toInsert[$i][1];

            //Run a query to check if the value already exists in a linked table
            $selectQuery = "SELECT * FROM " . $toInsert[$i][2] . " WHERE " . $toInsert[$i][2] . "." . $toInsert[$i][2] . " = '" . $toInsert[$i][1] . "'";
            ?><br><?php
            echo "Select Query: " . $selectQuery;

            if (recordExistsLinked($selectQuery, $con) == 0) {
                //The record does not exist. Create it
                ?><br><?php
                //When inserting the FK has to be converted to PK

                echo "Insert Query: " . "INSERT INTO " . $toInsert[$i][2] . " (" . $toInsert[$i][2] . "PK, " . $toInsert[$i][2] . ") VALUES (NULL , '" . $toInsert[$i][1] . "')";
                runQuery("INSERT INTO " . $toInsert[$i][2] . " (" . $toInsert[$i][2] . "PK, " . $toInsert[$i][2] . ") VALUES (NULL , '" . $toInsert[$i][1] . "')", $con);
            }
            //Run a query to get the primary key of the value
            $key = getSingleVal($selectQuery, $toInsert[$i][2] . "PK", $con);
            echo "The key is: " . $key;

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

    //Redirect the user
    redirectWithSuccess("Record has been updated!", 'edit.php');
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
    //echo $query;

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
function recordExistsLinked($query, $con)
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
    ?><br><?php
    echo "Duplicate query: " . $query;

    //Run the query and get the number of rows
    $recordCount = 0;
    foreach ($con->query($query) as $row) {
        $recordCount++;
    }
    ?><br><?php
    echo "Number of rows: " . $recordCount;
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