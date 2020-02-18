<?php
//TODO make sure that adding grades works properly in all cases.
//TODO add delete ability. When deleting records delete all Foreign keys that are not used by another record

//Check if the user is logged in
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

require("connect.php");

//Table names from the DB
$tablesToCheck = ['institution', 'subjectLevel', 'year', 'subjectCode', 'codeExtension', 'grade'];

//Used when looking for duplicate records
$tableColumns = ['institutionFk', 'subject', 'gradeFK', 'subjectLevelFK', 'yearFK', 'subjectCodeFK', 'creditsFk', 'codeExtensionFK'];

//Updating education records
echo var_dump($_POST);
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

    echo "Update record.";

    //Error check all the values. Include each value, the field name, a flag of the expected type in the array and the max length.
    findInvalid('education',
        [[$postedInstitution, 'institution', 'string', 40],
            [$postedSubject, 'subject', 'string', 100],
            [$postedSubjectYear, 'subjectYear', 'int', 4],
            [$postedSubjectLevel, 'subjectLevel', 'string', 20],
            [$postedCode, 'subjectCode', 'string', 10],
            [$postedCodeExtension, 'codeExtension', 'int', 4],
            [$postedCredits, 'credits', 'int', 2],
            [$postedGrade, 'grade', 'string', 2]]);


    //Todo add statement to return errors and success messages. use an error div to display them. Probably as a cookie that is displayed when the user is redirected.

    //Values that will be inserted into the DB
    $valuesToCheck = [$postedInstitution, $postedSubjectLevel, $postedSubjectYear, $postedCode, $postedCodeExtension, $postedGrade];

    //Change grade to credits if required
    //todo check if credits and grade have been posted. Auto choose which one to use.
    echo "Grade: " . $postedGrade;
    echo "Credits: " . $postedCredits;

    //Change the grade to be updated in required
    if ($postedGrade != "") {
        $tablesToCheck[5] = "grade";
    } else if ($postedCredits != "") {
        $tablesToCheck[5] = "credits";
        $valuesToCheck[5] = $postedCredits;
    } else {
        die("There was an error. Invalid grade input");
    }


    ?><br><?php

    echo "Tables to check: " . $tablesToCheck[0] . " " . $tablesToCheck[1] . " " .
        $tablesToCheck[2] . " " . $tablesToCheck[3] . " " . $tablesToCheck[4] . " " . $tablesToCheck[5];

    $gradeUpdate = false; //Tracks if a grade has been updated

    //Variables used when looking for duplicate records
    $foreignKeys = []; //Store the foreign keys. Used when looking for duplicates
    $creditsFK = 0;
    $gradeFK = 0;


    //Iterate over each field to see if it already exists in the database
    for ($i = 0; $i < sizeof($tablesToCheck); $i++) {
        //perform a query to check if the record exists
        ?><br><?php
        echo "Looking for records";
        ?><br><?php
        $recordCount = recordExists("SELECT * FROM " . $tablesToCheck[$i] . " WHERE " . $tablesToCheck[$i] . " = '" . $valuesToCheck[$i] . "'", $con);

        //Insert a new record if required
        if ($recordCount == 0) {
            //record if a grade is being updated
            if ($tablesToCheck[$i] == "grade" || $tablesToCheck[$i] == "credits") {
                $gradeUpdate = true;
            }

            echo "Inserting new statement: ";
            ?><br><?php
            newRecord("INSERT INTO " . $tablesToCheck[$i] . " (" . $tablesToCheck[$i] . "PK, " . $tablesToCheck[$i] . ") 
                    VALUES (NULL, '" . $valuesToCheck[$i] . "')", $con);

            ?><br><?php
            echo "Getting FK for table value: " . $i . " Value: " . $valuesToCheck[$i];
            ?><br><?php
            $key = getFK($tablesToCheck[$i], $valuesToCheck[$i], $con);
            array_push($foreignKeys, $key);

            //Insert the new foreign key
            updateFK("UPDATE education SET education." . $tablesToCheck[$i] . "FK = " . $key . " WHERE education.uniqueKey = " . $_POST['uniqueKey'], $con);

        } else {
            //Updating the the record to the new values
            ?><br><?php
            echo "Record already exists. Updating fields for " . $tablesToCheck[$i];
            ?><br><?php

            //Get the new foreign key
            echo "Getting FK for table value: " . $i . " Value: " . $tablesToCheck[$i];
            ?><br><?php
            $key = getFK($tablesToCheck[$i], $valuesToCheck[$i], $con);
            array_push($foreignKeys, $key);

            //Update the foreign key in the primary table
            updateFK("UPDATE education SET education." . $tablesToCheck[$i] . "FK = " . $key . " WHERE education.uniqueKey = " . $_POST['uniqueKey'], $con);

            //Update the grade/credits foreign keys
            if ($tablesToCheck[$i] == "credits") {
                echo "Hello credits";
                updateGrade("UPDATE education SET education.gradeFK = 0 WHERE education.uniqueKey = " . $_POST['uniqueKey'], $con);
                $creditsFK = $key;
            } else if ($tablesToCheck[$i] == "grade") {
                echo "Hello grade";
                updateGrade("UPDATE education SET education.creditsFK = 0 WHERE education.uniqueKey = " . $_POST['uniqueKey'], $con);
                $gradeFK = $key;
            }

        }
    }

    //Look for duplicates
    $values = [$foreignKeys[0], $postedSubject, $gradeFK, $foreignKeys[1], $foreignKeys[2], $foreignKeys[3], $creditsFK, $foreignKeys[4]];
    if (!findDuplicate('education', $tableColumns, $values, $con)) {
        echo "No duplicates found";

        //Update the record name
        ?><br><?php
        updateTableValue('education', 'subject', $postedSubject, 'uniqueKey', $_POST['uniqueKey'], $con);
    } else {
        echo "Duplicates found!";

        //todo clean all previous FK's that may have been created
        redirectWithError('Cannot enter duplicate record', 'edit.php');
    }

    //The new record has successfully been updated. Send a success message
    redirectWithSuccess('Record updated', 'edit.php');

}

//Creating new education records
if (isset($_POST["newEducationRecord"])) {
    echo "New Record";
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

    echo $postedNewInstitution; ?><br><?php
    echo $postedNewSubject; ?><br><?php
    echo $postedNewSubjectYear; ?><br><?php
    echo $postedNewSubjectLevel; ?><br><?php
    echo $postedNewSubjectCode; ?><br><?php
    echo $postedNewCodeExtension; ?><br><?php
    echo $postedNewCredits; ?><br><?php
    echo $postedNewGrade; ?><br><?php

    //Error check all the values. Include each value, the field name, a flag of the expected type in the array and the max length.
    findInvalid('education',
        [[$postedNewInstitution, 'institution', 'string', 40],
            [$postedNewSubject, 'subject', 'string', 100],
            [$postedNewSubjectYear, 'subjectYear', 'int', 4],
            [$postedNewSubjectLevel, 'subjectLevel', 'string', 20],
            [$postedNewSubjectCode, 'subjectCode', 'string', 10],
            [$postedNewCodeExtension, 'codeExtension', 'int', 4],
            [$postedNewCredits, 'credits', 'int', 2],
            [$postedNewGrade, 'grade', 'string', 2]]);

    //Values that will be inserted into the database
    $valuesToCheck = [$postedNewInstitution, $postedNewSubjectLevel, $postedNewSubjectYear, $postedNewSubjectCode, $postedNewCodeExtension, $postedNewGrade];
    $foreignKeys = [];

    //Used when inserting the grade and credits foreign keys
    $gradeFK = 0;
    $creditsFK = 0;

    //Update the grade to be inserted if required
    if ($postedNewGrade != "") {
        $tablesToCheck[5] = "grade";
    } else if ($postedNewCredits != "") {
        $tablesToCheck[5] = "credits";
        $valuesToCheck[5] = $postedNewCredits;
    } else {
        die("There was an error. Invalid grade input");
    }

    ?><br><?php
    echo print_r($valuesToCheck);
    ?><br><?php
    echo print_r($tablesToCheck);
    ?><br><?php


    //This code iterates over each table looking for the corresponding foreign key. If it cannot find it is added to the DB
    for ($i = 0; $i < sizeof($tablesToCheck); $i++) {
        //perform a query to check if the record exists
        ?><br><?php
        echo "Looking for records";
        ?><br><?php
        $recordCount = recordExists("SELECT * FROM " . $tablesToCheck[$i] . " WHERE " . $tablesToCheck[$i] . " = '" . $valuesToCheck[$i] . "'", $con);

        //The record does not exist. Create a new record
        if ($recordCount == 0) {
            ?><br><?php
            echo "New Record required for value " . $valuesToCheck[$i];
            ?><br><?php

            //Create the new record
            echo "Inserting new statement: ";
            ?><br><?php
            newRecord("INSERT INTO " . $tablesToCheck[$i] . " (" . $tablesToCheck[$i] . "PK, " . $tablesToCheck[$i] . ") VALUES (NULL, '" . $valuesToCheck[$i] . "')", $con);


            ?><br><?php
            echo "Getting FK for table value: " . $i . " Value: " . $valuesToCheck[$i];
            ?><br><?php

            //Save the foreign key
            $key = getFK($tablesToCheck[$i], $valuesToCheck[$i], $con);
            array_push($foreignKeys, $key);

            //Update the grade/credits FK if required
            if ($tablesToCheck[$i] == "grade") {
                $gradeFK = $key;
            } else if ($tablesToCheck[$i] == "credits") {
                $creditsFK = $key;
            }

            //The record already exists
        } else {
            ?><br><?php
            echo "Record already exists. Updating fields for " . $tablesToCheck[$i];
            ?><br><?php

            //Get the new foreign key
            echo "Getting FK for table value: " . $i . " Value: " . $tablesToCheck[$i];
            ?><br><?php

            //Save the foreign key
            $key = getFK($tablesToCheck[$i], $valuesToCheck[$i], $con);
            array_push($foreignKeys, $key);

            //Update the grade/credits FK if required
            if ($tablesToCheck[$i] == "grade") {
                $gradeFK = $key;
            } else if ($tablesToCheck[$i] == "credits") {
                $creditsFK = $key;
            }
        }
    }

    ?><br><?php
    echo "Foreign keys: " . print_r($foreignKeys);

    //Create the new record
    echo "grade: " . $gradeFK;
    ?><br><?php

    //Check to see if a duplicate record exists
    $values = [$foreignKeys[0], $postedNewSubject, $gradeFK, $foreignKeys[1], $foreignKeys[2], $foreignKeys[3], $creditsFK, $foreignKeys[4]];
    if (!findDuplicate('education', $tableColumns, $values, $con)) {
        echo "No duplicates found";

        //Create the new record
        newRecord("INSERT INTO education (uniqueKey, institutionFK, subject, gradeFk, subjectLevelFK, yearFK, subjectCodeFK, creditsFK, codeExtensionFK)
        VALUES (NULL, " . $foreignKeys[0] . ", '" . $postedNewSubject . "', " . $gradeFK . ", " . $foreignKeys[1] . ",
    " . $foreignKeys[2] . ", " . $foreignKeys[3] . ", " . $creditsFK . ", " . $foreignKeys[4] . ")", $con);
    } else {
        echo "Duplicates found!";

        //todo clean all previous FK's that may have been created
        redirectWithError('Cannot enter duplicate record', 'edit.php');
    }

    //The new record has successfully been created. Send a success message
    redirectWithSuccess('New record created', 'edit.php');

}

//

//Looks for any invalid values that the user may have entered. Takes education/project and an array of all the values
function findInvalid($type, $values) {
    $gradeValid = false; //flag used to determine if the grade is valid
    ?><br><?php
    echo "Check invalid for type: " . $type;
    ?><br><?php
    echo "Check for values of array size: " . sizeof($values);
    ?><br><?php
    for ($i = 0; $i < sizeof($values); $i++) {
        ?><br><?php
        echo $values[$i][0] . ", " . $values[$i][1] . ", " . $values[$i][2];

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
            echo "Looking at: " . $values[$i][1];
            ?><br><?php

            //Validating the grade and credits
            //First check to see if both fields are empty
            $creditsPos = findIn2dArray($values, 'credits');
            $gradePos = findIn2dArray($values, 'grade');

            echo "Credits at: " . $creditsPos . " empty = " . isEmpty($values[$creditsPos][0]);
            ?><br><?php
            echo "Grades at: " . $gradePos . " empty = " . isEmpty($values[$gradePos][0]);
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
                    echo "Checking grade";
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

            //Error checking the year
        } else if ($values[$i][1] == 'subjectYear' || $values[$i][1] == 'newSubjectYear') {
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

            //Test to see if the value is empty ignore if a valid grade has been entered
        } else if (isEmpty($values[$i][0]) && !$gradeValid) {
            ?><br><?php
            redirectWithError("Value cannot be empty. For field: " . $values[$i][1],  'edit.php');
            //die("You cannot have an empty value!");

            //Look for values that do not match their specified type
        } else if (!isType($values[$i][2], $values[$i][0])) {
            ?><br><?php
            redirectWithError("Invalid type. For value: " . $values[$i][0] . ". This value should be of type " . $values[$i][2],  'edit.php');
            die($values[$i][1] . " should be of type " . $values[$i][2]);
        }
    }
}

//Redirects the user to the specified page with an error cookie set.
function redirectWithError($cookieValue, $redirectTo) {
    //Add a flag to the front of the cookie describing what it is
    $cookieValue = "ERROR: " . $cookieValue;

    echo "called redirect";

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
    echo "Checking type: " . $type;

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
    //echo " Checking value " . $value . " for illegal characters";
    $characters = ['“', '”', '"', '"', '‘', '’', "'", "'", '«', '»', '「', '」'];
    for ($i = 0; $i < sizeof($characters); $i++) {
        //Check if the character is contained in the value
        if (strpos($value, $characters[$i]) !== false) {
            //echo " contains: " . $characters[$i];
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
    echo "Update column query: " . $query;

    $query = $con->prepare($query);
    $query->execute();
}

//function to update the grades
function updateGrade($query, $con)
{
    ?><br><?php
    echo $query;
    $query = $con->prepare($query);
    $query->execute();
}

//Creates a new records
function newRecord($query, $con)
{
    echo $query;

    //execute the query
    $newRecordQuery = $con->prepare($query);
    $newRecordQuery->execute();
}

//Checks if a record exists in a linked table. Returns the number of records
function recordExists($query, $con)
{
    echo $query;
    $key = "";
    $result = "";
    $query = $con->prepare($query);
    $query->execute();
    $query->bind_result($key, $result);
    $query->store_result();
    $recordCount = $query->num_rows();

    //Print for debugging
    ?><br><?php
    echo "Count: " . $recordCount;
    while ($row = $query->fetch()) {
        ?><br><?php
        echo $result;
    }
    return $recordCount;
}

//Updates the specified foreign key
function updateFK($query, $con)
{
    echo $query;
    $query = $con->prepare($query);
    $query->execute();
    ?><br><?php
}

//Looks for duplicate records in the given table. Takes a the table name. An array of fields and and array of values.
//Returns true if a duplicate is found
function findDuplicate($table, $fields, $values, $con)
{
    //Build the query
    $query = "SELECT * FROM " . $table;

    for ($i = 0; $i < sizeof($fields); $i++) {
        //Add where for the first value
        if ($i == 0) {
            $query = $query . " WHERE ";
        }

        //Insert with quotation marks if the value is not numeric
        if (!is_numeric($values[$i])) {
            $query = $query . $fields[$i] . " = '" . $values[$i] . "'";
        } else {
            $query = $query . $fields[$i] . " = " . $values[$i];
        }

        //Add an and if it is not the last value
        if ($i < sizeof($fields) - 1) {
            $query = $query . " AND ";
        }
    }

    ?><br><?php
    echo "Duplicates Query: " . $query;
    ?><br><?php

    //Execute the query
    $query = $con->prepare($query);
    $query->execute();
    $query->store_result();
    $recordCount = $query->num_rows();

    if ($recordCount > 0) {
        return true;
    }

    //No duplicate records found. Return false.
    return false;
}


//Function that executes a query and returns the foreign key
function getFK($table, $value, $con)
{
    echo "Value: " . $value;
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
    echo "FK: " . $key;
    ?><br><?php
    return $key;
}

//Takes a value, strips the spaces and returns it
function stripSpaces($value) {
    return str_replace(' ', '', $value);
}

//Manual redirect.
?>
    <p>
        Click <a href="edit.php">here</a> to go back to the edit page.
    </p>
<?php

//redirect back to the previous page
//header("Location: edit.php");
?>