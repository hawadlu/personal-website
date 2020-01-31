<?php
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
        echo $_POST['institution'];
        echo $_POST['subject'];
        echo $_POST['subject-Year'];
        echo $_POST['subject-Level'];
        echo $_POST['code'];
        echo $_POST['code-Extension'];
        echo $_POST['uniqueKey'];


        //Todo check if the things that the user entered are in the database. Add if they are not
        //Todo add statement to add grades in the required manner. NCEA vs Uni
        //Todo add statement to return errors and success messages. use an error div to display them. Probably as a cookie that is displayed when the user is redirected.
        //Check if the entered parameters exist in the database. Grades do not need to be checked as they are selected from a dropdown
        $tablesToCheck = ['institution', 'subjectCode', 'codeExtension', 'subjectLevel', 'year', 'grade'];
        $valuesToCheck = [ $_POST['institution'],  $_POST['code'], $_POST['code-Extension'], $_POST['subject-Level'], $_POST['subject-Year'], $_POST['grade']];

        //Change grade to credits if required
        if (is_numeric($_POST['grade'])) {
            $tablesToCheck[5] = "credits";
            $valuesToCheck[5] = (int) $_POST['grade'];
        }

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
                echo "Inserting new statement: ";
                    ?><br><?php
                    $newRecord = "INSERT INTO " . $tablesToCheck[$i] . " (" . $tablesToCheck[$i] . "PK, " . $tablesToCheck[$i] . ") 
                    VALUES (NULL, '" . $valuesToCheck[$i] . "')";

                echo $newRecord;

                //execute the query
//                $newRecord = $con->prepare($newRecord);
//                $newRecord->execute();

            }

            //Run a second query to get the new primary key

        }
    }

}


//redirect back to the previous page
//header("Location: edit.php");
?>