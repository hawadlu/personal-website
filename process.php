<?php
//TODO make sure that adding grades works properly in all cases.





//Check if the user is logged in
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

require("connect.php");

//Check which type of query should be executed
echo var_dump($_POST);
if (isset($_POST['submitUpdate'])) {
    echo "Update record.";

    //Get the query type
    $qryType = $_POST['qryType'];
    $query = "";

    //Create the update education query
    if ($qryType == "educationUpdate") {
        //Get the variables
//        echo $_POST['institution'];
//        echo $_POST['subject'];
//        echo $_POST['subject-Year'];
//        echo $_POST['subject-Level'];
//        echo $_POST['code'];
//        echo $_POST['code-Extension'];
//        echo $_POST['uniqueKey'];
//        echo "Grade type: " . $_POST['gradeType'];


        //Todo check if the things that the user entered are in the database. Add if they are not
        //Todo add statement to add grades in the required manner. NCEA vs Uni
        //Todo add statement to return errors and success messages. use an error div to display them. Probably as a cookie that is displayed when the user is redirected.
        //Check if the entered parameters exist in the database. Grades do not need to be checked as they are selected from a dropdown
        $tablesToCheck = ['institution', 'subjectCode', 'codeExtension', 'subjectLevel', 'year', 'grade'];
        $valuesToCheck = [ $_POST['institution'],  $_POST['code'], $_POST['code-Extension'], $_POST['subject-Level'], $_POST['subject-Year'], $_POST['grade']];

        //Change grade to credits if required
        if (is_numeric($_POST['grade']) && $tablesToCheck[5] == 'grade') {
            $tablesToCheck[5] = "credits";
            $valuesToCheck[5] = (int) $_POST['grade'];
            //Updates from credits to grade
        } elseif (!is_numeric($_POST['grade']) && $tablesToCheck[5] == 'credits') {
            $tablesToCheck[5] = "grade";
        }

        $gradeUpdate = false; //Tracks if a grade has been updated

        for ($i = 0; $i < sizeof($tablesToCheck); $i++) {
            //perform a query to check if the record exists
            ?><br><br><?php
            $queryString = "SELECT * FROM " . $tablesToCheck[$i] . " WHERE " . $tablesToCheck[$i] . " = '" . $valuesToCheck[$i] . "'";
            echo $queryString;
            $query = $con->prepare($queryString);
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

            //Insert a new record if required
            if ($recordCount == 0) {
                //record if a grade is being updated
                if ($tablesToCheck[$i] == "grade" || $tablesToCheck[$i] == "credits") {
                    $gradeUpdate = true;
                }

                echo "Inserting new statement: ";
                    ?><br><?php
                    $newRecordQuery = "INSERT INTO " . $tablesToCheck[$i] . " (" . $tablesToCheck[$i] . "PK, " . $tablesToCheck[$i] . ") 
                    VALUES (NULL, '" . $valuesToCheck[$i] . "')";

                echo $newRecordQuery;

                //execute the query
                $newRecordQuery = $con->prepare($newRecordQuery);
                $newRecordQuery->execute();

                ?><br><?php
                //Get the new foreign key
                $foreignKeyQuery = "SELECT * FROM " . $tablesToCheck[$i] . " WHERE " . $tablesToCheck[$i] . "." . $tablesToCheck[$i] . " = '" . $valuesToCheck[$i] . "'";
                echo $foreignKeyQuery;
                $foreignKeyQuery = $con->prepare($foreignKeyQuery);
                $foreignKeyQuery->execute();
                $foreignKeyQuery->bind_result($primary, $val);
                $foreignKeyQuery->store_result();
                $key = "";
                while ($row=$foreignKeyQuery->fetch()) {
                    ?><br><?php
                    $key = $primary;
                }
                echo $key;
                ?><br><?php

                //Insert the new foreign key
                $updateFKQuery = "UPDATE education SET education." . $tablesToCheck[$i] . "FK = " . $key . " WHERE education.uniqueKey = " .  $_POST['uniqueKey'];
                echo $updateFKQuery;
                $updateFKQuery = $con->prepare($updateFKQuery);
                $updateFKQuery->execute();
                ?><br><?php

            }
            //Resetting the grade/credits only runs when grades are updates
            if ($tablesToCheck[$i] == "grade" || $tablesToCheck[$i] == "credits" && $gradeUpdate) {
                $gradeUpdateQuery = "";
                //NOTE 'gradeType' refers to the previously stored grade. Not the updated one
                //Convert to numeric
                echo "Type: " . $_POST['gradeType'] . " Stored val: " . $tablesToCheck[5];
                if ($_POST['gradeType'] == "false" && $tablesToCheck[5] == "credits") {
                    echo "Apply conversion. Non numeric to numeric";
                    $gradeUpdateQuery = "UPDATE education SET education.gradeFK = 0 WHERE education.uniqueKey = " . $_POST['uniqueKey'];
                    echo $gradeUpdateQuery;
                    //Convert to non numeric
                } else if ($_POST['gradeType'] == "true" && $tablesToCheck[5] == "grade") {
                    echo "Apply conversion. Numeric to non numeric";
                    $gradeUpdateQuery = "UPDATE education SET education.creditsFK = 0 WHERE education.uniqueKey = " . $_POST['uniqueKey'];
                    echo $gradeUpdateQuery;
                }

                $gradeUpdateQuery = $con->prepare($gradeUpdateQuery);
                $gradeUpdateQuery->execute();
            }


        }
    }

}


//redirect back to the previous page
//header("Location: edit.php");
?>