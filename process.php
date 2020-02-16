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

//Check which type of query should be executed
echo var_dump($_POST);
if (isset($_POST['submitEducationUpdate'])) {
    echo "Update record.";

    //Get the query type
    $qryType = $_POST['qryType'];
    $query = "";

    //Create the update education query
    if ($qryType == "educationUpdate") {
        //Todo check if the things that the user entered are in the database. Add if they are not
        //Todo add statement to return errors and success messages. use an error div to display them. Probably as a cookie that is displayed when the user is redirected.

        //Values that will be inserted into the DB
        $valuesToCheck = [ $_POST['institution'],  $_POST['code'], $_POST['code-Extension'], $_POST['subject-Level'], $_POST['subject-Year'], $_POST['grade']];


        //Change grade to credits if required
        //todo check if credits and grade have been posted. Auto choose which one to use.
        echo "Grade: " . $_POST['grade'];
        echo "Credits: " . $_POST['credits'];

        //Change the grade to be updated in required
        if ($_POST['grade'] != "") {
            $tablesToCheck[5] = "grade";
        } else if ($_POST['credits'] != "") {
            $tablesToCheck[5] = "credits";
            $valuesToCheck[5] = $_POST['credits'];
        } else {
            die("There was an error. Invalid grade input");
        }


        ?><br><?php

        echo "Tables to check: " . $tablesToCheck[0] . " " . $tablesToCheck[1] . " " .
            $tablesToCheck[2] . " " . $tablesToCheck[3] . " " . $tablesToCheck[4]. " " . $tablesToCheck[5];

        $gradeUpdate = false; //Tracks if a grade has been updated

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

                //Insert the new foreign key
                updateFK("UPDATE education SET education." . $tablesToCheck[$i] . "FK = " . $key . " WHERE education.uniqueKey = " .  $_POST['uniqueKey'], $con);

            } else {
                //Updating the the record to the new values
                ?><br><?php
                echo "Record already exists. Updating fields for " . $tablesToCheck[$i];
                ?><br><?php

                //Get the new foreign key
                echo "Getting FK for table value: " . $i . " Value: " . $tablesToCheck[$i];
                ?><br><?php
                $key = getFK($tablesToCheck[$i], $valuesToCheck[$i], $con);

                //Update the foreign key in the primary table
                updateFK("UPDATE education SET education." . $tablesToCheck[$i] . "FK = " . $key . " WHERE education.uniqueKey = " .  $_POST['uniqueKey'], $con);

                //Update the grade/credits foreign keys
                if ($tablesToCheck[$i] == "credits") {
                    echo "Hello credits";
                    updateGrade("UPDATE education SET education.gradeFK = 0 WHERE education.uniqueKey = " . $_POST['uniqueKey'], $con);
                } else if ($tablesToCheck[$i] == "grade") {
                    echo "Hello grade";
                    updateGrade("UPDATE education SET education.creditsFK = 0 WHERE education.uniqueKey = " . $_POST['uniqueKey'], $con);
                }

            }
        }
    }

}

//Creating new records
//Todo make sure that users cannot create duplicate records.
if(isset($_POST["newEducationRecord"])) {
    echo "New Record";
    ?><br><?php
    echo $_POST['newInstitution'];?><br><?php
    echo $_POST['newSubject'];?><br><?php
    echo $_POST['newSubjectYear'];?><br><?php
    echo $_POST['newSubjectLevel'];?><br><?php
    echo $_POST['newCode'];?><br><?php
    echo $_POST['newCodeExtension'];?><br><?php
    echo $_POST['newCredits'];?><br><?php
    echo $_POST['newGrade'];?><br><?php

    //Values that will be inserted into the database
    $valuesToCheck = [ $_POST['newInstitution'], $_POST['newSubjectLevel'], $_POST['newSubjectYear'],  $_POST['newCode'], $_POST['newCodeExtension'], $_POST['newGrade']];
    $foreignKeys = [];

    //Used when inserting the grade and credits foreign keys
    $gradeFK = 0;
    $creditsFK = 0;

    //Update the grade to be inserted if required
    if ($_POST['newGrade'] != "") {
        $tablesToCheck[5] = "grade";
    } else if ($_POST['newCredits'] != "") {
        $tablesToCheck[5] = "credits";
        $valuesToCheck[5] = $_POST['newCredits'];
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


    newRecord("INSERT INTO education (uniqueKey, institutionFK, subject, gradeFk, subjectLevelFK, yearFK, subjectCodeFK, creditsFK, codeExtensionFK) 
                VALUES (NULL, " . $foreignKeys[0] . ", '" . $_POST['newSubject'] . "', " . $gradeFK . ", " . $foreignKeys[1] . ", 
" . $foreignKeys[2] . ", " . $foreignKeys[3] . ", " . $creditsFK . ", " . $foreignKeys[4] . ")", $con);



}

//function to update the grades
function updateGrade($query, $con) {
    ?><br><?php
    echo $query;
    $query = $con->prepare($query);
    $query->execute();
}

//Creates a new records
function newRecord($query, $con){
    echo $query;

    //execute the query
    $newRecordQuery = $con->prepare($query);
    $newRecordQuery->execute();
}

//Checks if a record exists in a linked table. Returns the number of records
function recordExists($query, $con) {
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
    while ($row=$query->fetch()) {
        ?><br><?php
        echo $result;
    }
    return $recordCount;
}

//Updates the specified foreign key
function updateFK($query, $con) {
    echo $query;
    $query = $con->prepare($query);
    $query->execute();
    ?><br><?php
}

//Function that executes a query and returns the foreign key
function getFK($table, $value, $con) {
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
    while ($row=$foreignKeyQuery->fetch()) {
        ?><br><?php
        $key = $primary;
    }
    ?><br><?php
    echo "FK: " . $key;
    ?><br><?php
    return $key;
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