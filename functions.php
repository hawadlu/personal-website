<?php
session_start();
//Includes all of the functions required by the different pages


//Redirects the user to the specified page with an error cookie set.
function redirectWithError($cookieValue, $redirectTo)
{
    //Add a flag to the front of the cookie describing what it is
    $cookieValue = "ERROR: " . $cookieValue;

    //Set the cookie
    setcookie('errorMsg', $cookieValue);

    //Redirect
    header("location: " . $redirectTo);

    //Stop all execution
    exit();
}

//Redirect the user to a specific page with a success message
function redirectWithSuccess($cookieValue, $redirectTo)
{
    //Add a flag to the front of the cookie describing what it is
    $cookieValue = "Success: " . $cookieValue;

    //Set the cookie
    setcookie('successMsg', $cookieValue);

    //Redirect
    header("location: " . $redirectTo);

    //Stop all execution
    exit();
}

//Gets an array of values from the database. Used for autocomplete
function getArray($query, $con)
{
    $value = null;
    $query = $con->prepare($query);
    $query->execute();
    $query->bind_result($value);
    $query->store_result();

    $array = [];

    while ($row = $query->fetch()) {
        array_push($array, $value);
    }
    return $array;
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

//Finds the unique key of a value in one of the play around arrays. Returns it's location, false if it was not found
function findPlayAroundKeyLocation($key, $array)
{
    for ($i = 0; $i < sizeof($array); $i++) {
        if ($array[$i][0] == $key) {
            return $i;
        }
    }
    return false;
}

//Setup the education session
function setupEducationSession() {
    //Create some default records
    $defaultRec1 = [0, "Tawa College", "Computer Science", "COMP", 301, null, 22, "NCEA Level Three", 2018];
    $defaultRec2 = [1, "Tawa College", "Data Science", "DATS", 312, null, 18, "NCEA Level Two", 2017];
    $defaultRec3 = [2, "A Test School", "Computer Graphics", "CGRA", 151, "A", null, "Year One University", 2019];
    //Create the session
    $_SESSION['playAroundEducation'] = [$defaultRec1, $defaultRec3, $defaultRec2];
}

//Set up the examples session
function setupExampleSession() {
    //Create some default Records
    $defaultRec1 = [0, ['images/userImages/cat.jpeg', 'images/userImages/eraser.jpeg', 'images/userImages/phone.jpeg'], "How I Saved To World Twice", ["CSS", "PHP", "JavaScript", "HTML"],
        "http://luke.dx.am", "http://github.com/hawadlu", "Whoops, I forgot to write a description here.", 2019];
    $defaultRec2 = [1, ['images/userImages/fork.jpeg', 'images/userImages/nailClippers.jpeg', 'images/userImages/phone.jpeg', 'images/userImages/spring.jpeg'],
        "What I Ate For Breakfast", ["CSS", "PHP", "RUBY", "Perl"], 'https://breakfast.co.nz', "https://github.com/PushyPixels/BreakfastWithUnity",
        "Contrary to the opinions of many people. I did not eat a fork, nail clippers, a phone and a spring for breakfast. I ate cornflakes instead. 
        I know that it's disappointing and a little anticlimactic.", 2020];
    $_SESSION['playAroundExamples'] = array($defaultRec1, $defaultRec2);
}

//Set up the languages session
function setupLanguagesSession() {
    $_SESSION['sessionLanguages'] = ['CSS', 'HTML', 'JavaScript', 'Java', 'PHP', 'Perl', 'python', 'Ruby', 'C++', 'C', 'C#'];
}

//Set up the image session
function setupImageSession() {
    $_SESSION['sessionImages'] = [];

    //Get all of the possible images
    $path    = 'images/userImages';
    $files = scandir($path);
    $files = array_diff(scandir($path), array('.', '..'));

    //Add to the images array
    foreach ($files as $image) {
        if (strpos($image, '.jpeg') !== false) {
            array_push($_SESSION['sessionImages'], "images/userImages/" . $image);
        }
    }
}

//function to get all the unique values for the session variables. Takes an array and the position of the elements to look at
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

//Find the next available unique key in one of the session arrays
function generatePlayAroundUniqueKey($array)
{
    //Get a list of all the keys already in use
    $keysInUse = [];
    for ($i = 0; $i < sizeof($array); $i++) {
        array_push($keysInUse, $array[$i][0]);
    }

    //Find a key that is not being used
    $key = 0;
    while (true) {
        if (!in_array($key, $keysInUse)) {
            return $key;
        } else {
            $key++;
        }
    }
    return null;
}

//Takes a link and validates it
function validateLink($link)
{
//Validate the links
    if (!empty($link)) {
        if (strpos((string)$link, 'http://') === false && strpos((string)$link, 'https://') === false) {
            redirectWithError("Link must contain http:// or https:// for value: " . (string)$link, 'edit.php');
        }
    }
}

//Takes the required directory and file array and uploads the image. Returns error if needed
function uploadImages($directory, $toUploadArray)
{
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

    $file_array = reArrayFiles($toUploadArray);
    //pre_r($file_array);
    //die();

    //Create the directory if it does not exist
    if (!is_dir($directory)) {
        mkdir($directory);
    }

    for ($i = 0; $i < count($file_array); $i++) {
        //Check for errors
        if ($file_array[$i]['error']) {
            return $file_array[$i]['name'] . " " . $phpFileUploadErrors[$file_array[$i]['error']];

            //Check for extensions errors
        } else {
            //Allowable file types
            $extensions = array("jpg", "png", "gif", "jpeg");
            $file_ext = explode(".", $file_array[$i]["name"]);
            $file_ext = end($file_ext);

            //Check if the extension is acceptable
            if (!in_array($file_ext, $extensions)) {
                return $file_array[$i]["name"] . " Invalid file extension!";
            } else {
                //File uploaded successfully
                //Check if the file already exists in the directory
                if (!file_exists("images/examples/" . $file_array[$i]["name"])) {
                    //Move the file from the temporary directory to the intended directory. Resize at the same time
                    move_uploaded_file($file_array[$i]["tmp_name"], $directory . "/" . $file_array[$i]["name"]);

                } else {
                    //Print message stating that the file already exists
                    return $file_array[$i]["name"] . " already exists";
                }
            }
        }

        //Resize the image
        $file = $directory . "/" . $file_array[$i]["name"];
        $image = resize_image($directory . "/" . $file_array[$i]['name'], 250, 250);

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
    return true;
}

//Checks that the provided file is an image
function isImage($path)
{
    $a = getimagesize($path);
    $image_type = $a[2];

    if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP))) {
        return true;
    }
    return false;
}

//Deletes an entire directory (recursively)
function deleteDirectory($filePath)
{
    //Only remove if the directory exists
    if (is_dir($filePath)) {
        //Look for new folders
        $files = glob($filePath . '*', GLOB_MARK);

        //Loop through the contents
        foreach ($files as $file) {
            //If the file is a directory call the function again
            if (is_dir($file)) {
                deleteDirectory($file);
            } else {
                unlink($file);
            }
        }

        //Remove the directory
        rmdir($filePath);
    }
}

//Calculates and returns the directory where the example images are stored for a record
function getExampleDirectory($uniqueKey, mysqli $con)
{
//Get the current example name. This will be used later when renaming the directory.
    $originalDirectory = $value = null;
    $query = "SELECT examples.name FROM examples WHERE examples.uniqueKey = " . $uniqueKey;
    $query = $con->prepare($query);
    $query->execute();
    $query->bind_result($value);
    $query->store_result();

    while ($row = $query->fetch()) {
        $originalDirectory = $value;
    }

    $query->close();

    //Strip the spaces from the directory
    $originalDirectory = 'images/examples/' . stripSpaces($originalDirectory);

    return $originalDirectory;
}

//Sets up and returns all the required parameters needed when foreign keys are removed from the database. Specific to the examples table
function setCleanupExamples($key, mysqli $con)
{
//Create a 2d array of the FK column's and their respective keys
    $uniqueKey = $name = $yearFk = $description = $languageOneFK = $languageTwoFK = $languageThreeFK = $languageFourFK = $languageFiveFK = $link = $github = null;
    $query = "SELECT * FROM examples WHERE examples.uniqueKey = " . $key;
    $query = runAndReturn($query, $con);
    $query->bind_result($uniqueKey, $name, $yearFk, $description, $languageOneFK, $languageTwoFK, $languageThreeFK, $languageFourFK, $languageFiveFK, $link, $github);
    $query->store_result();
    $query->fetch();

    //The tables that use this FK, the table that stores the FK, the column where the FK can be found, the value
    $toDelete = [[['examples', 'education'], 'year', 'yearFK', $yearFk],
        [['examples'], 'languages', 'languageOneFK', $languageOneFK],
        [['examples'], 'languages', 'languageTwoFK', $languageTwoFK],
        [['examples'], 'languages', 'languageThreeFK', $languageThreeFK],
        [['examples'], 'languages', 'languageFourFK', $languageFourFK],
        [['examples'], 'languages', 'languageFiveFK', $languageFiveFK]];

    $query->close();
    return $toDelete;
}

//Sets up and returns all the required parameters needed when foreign keys are removed from the database. Specific to the examples table
function setCleanupEducation($key, mysqli $con)
{
//Create a 2d array of the FK column's and their respective keys
    $uniqueKey = $institutionFK = $subject = $gradeFK = $subjectLevelFK = $yearFK = $subjectCodeFK = $creditsFK = $codeExtensionFK = null;
    $query = "SELECT * FROM education WHERE education.uniqueKey = " . $key;
    $query = runAndReturn($query, $con);
    $query->bind_result($uniqueKey, $institutionFK, $subject, $gradeFK, $subjectLevelFK, $yearFK, $subjectCodeFK, $creditsFK, $codeExtensionFK);
    $query->store_result();
    $query->fetch();
    $query->close();

    //The tables that use this FK, the table that stores the FK, the column where the FK can be found, the value
    return [[['education'], 'institution', 'institutionFK', $institutionFK],
        [['education'], 'grade', 'gradeFK', $gradeFK],
        [['education'], 'subjectLevel', 'subjectLevelFK', $subjectLevelFK],
        [['education', 'examples'], 'year', 'yearFK', $yearFK],
        [['education'], 'subjectCode', 'subjectCodeFK', $subjectCodeFK],
        [['education'], 'credits', 'creditsFK', $creditsFK],
        [['education'], 'codeExtension', 'codeExtensionFK', $codeExtensionFK]];
}

//Takes a 2d array of foreign keys and cleans any that are no longer needed
//ToClean = The tables that use this FK, the table that stores the FK, the column where the FK can be found, the value
function cleanUpFK($toClean, $con)
{
    //Go through each foreign key
    for ($i = 0; $i < sizeof($toClean); $i++) {
        $fkContained = false; //Assume that that foreign key does not exist anywhere else

        //Check to see if the foreign key is 0
        if ($toClean[$i][3] != 0) {
            //Go through each of the tables
            for ($j = 0; $j < sizeof($toClean[$i][0]); $j++) {
                //Only run if it is an array
                if (is_array($toClean[$i][$j])) {
                    for ($k = 0; $k < sizeof($toClean[$i][$j]); $k++) {
                        $query = "SELECT * FROM " . $toClean[$i][$j][$k] . " WHERE " . $toClean[$i][$j][$k] . "." . $toClean[$i][2] . " = " . $toClean[$i][3];

                        $query = $con->prepare($query);
                        $query->execute();
                        $recordCount = 0;
                        while ($query->fetch()) {
                            $recordCount++;
                        }

                        //Get the number of records that use this foreign key. If !0 return without deleting the record
                        if ($recordCount != 0) {
                            //The record should not be deleted
                            $fkContained = true;
                        }

                        //close the query
                        $query->close();
                    }
                }
            }
            //Delete the foreign key if it is safe
            if ($fkContained == false) {
                $query = "DELETE FROM " . $toClean[$i][1] . " WHERE " . $toClean[$i][1] . "PK = " . $toClean[$i][3];
                runQuery($query, $con);
            }
        }
    }
}

//Looks for any invalid values that the user may have entered. Takes education/project and an array of all the values
function findInvalid($values)
{
    $gradeValid = false; //flag used to determine if the grade is valid
    for ($i = 0; $i < sizeof($values); $i++) {
        //Array used when checking the length of the string. Declared here to avoid multiple function calls
        $checkLenArray = checkLength($values[$i][0], $values[$i][3]);

        //Look for illegal characters
        if (containsIllegalCharacter($values[$i][0]) != null) {

            //Set the cookie and redirect
            redirectWithError("Illegal character (" . containsIllegalCharacter($values[$i][0]) . "). For value: " . $values[$i][0], 'edit.php');

            //Check to see if the string is shorter that the maximum length allowed
        } else if ($checkLenArray[0] == false) {
            redirectWithError("Input too large. For value: " . $checkLenArray[2] . "(" . $checkLenArray[1] . "). Max length for field " . $values[$i][1] . " is " . $values[$i][3], 'edit.php');


            //Check to see if credits and grade are valid
        } else if (($values[$i][1] == 'credits' || $values[$i][1] == 'grade') && !$gradeValid) {

            //Validating the grade and credits
            //First check to see if both fields are empty
            $creditsPos = findIn2dArray($values, 'credits');
            $gradePos = findIn2dArray($values, 'grade');


            //No grades have been entered
            if (isEmpty($values[$creditsPos][0]) && isEmpty($values[$gradePos][0])) {
                redirectWithError("You must fill in at least one type of grade", 'edit.php');
                //die("Grade cannot be empty!");

                //Both grades have been entered
            } else if (!isEmpty($values[$creditsPos][0]) && !isEmpty($values[$gradePos][0])) {
                redirectWithError("You can only enter one credits/grade value. For credits: " . $values[$creditsPos][0] . " and grade: " . $values[$gradePos][0], 'edit.php');
                //die("You can only enter one grade!");

                //Check to see if the entered grades match the specified types
            } else {
                if ($values[$i][1] == 'credits' && !isEmpty($values[$i][0])) {
                    //Check that the credits are numeric.
                    if (!isType($values[$i][2], $values[$i][0])) {
                        redirectWithError("Credits must be numeric. For credits: " . $values[$creditsPos][0], 'edit.php');
                        //die("Credits must be numeric");

                        //Check for any decimal points
                    } else if (strpos($values[$i][0], '.') !== false) {
                        redirectWithError("Credits must be a whole number: For credits: " . $values[$creditsPos][0], 'edit.php');
                        //die("Credits must be a whole number");

                        //Look for negative numbers
                    } else if ((int)$values[$i][0] < 0) {
                        redirectWithError("Credits cannot be negative. For credits: " . $values[$creditsPos][0], 'edit.php');
                        //die("Credits cannot be negative");

                        //Look for numbers that are too high
                    } else if ((int)$values[$i][0] > 50) {
                        redirectWithError("Credits cannot be greater than 50. For value: " . $values[$creditsPos][0], 'edit.php');
                        //die("That's too many credits!");
                    } else {
                        $gradeValid = true;
                    }
                } else if ($values[$i][1] == 'grade' && !isEmpty($values[$i][0])) {
                    $gpaGrades = ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'E', 'F'];
                    //Check that the grades are not numeric
                    if (!isType($values[$i][2], $values[$i][0])) {
                        redirectWithError("GPA cannot be numeric. For value: " . $values[$gradePos][0], 'edit.php');
                        //die("Grades cannot be numeric");
                    } else if (!isMemberOf($values[$i][0], $gpaGrades)) {
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
            redirectWithError("Value cannot be empty. For field: " . $values[$i][1], 'edit.php');

            //Look for values that do not match their specified type
        } else if (!isType($values[$i][2], $values[$i][0])) {
            redirectWithError("Invalid type. For value: " . $values[$i][0] . ". " . $values[$i][1] . " should be of type " . $values[$i][2], 'edit.php');
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
}

//Update records for education and examples. Takes an array of values and uses them to insert a record.
function insertValues(array $toInsert, mysqli $con, $tableToUpdate)
{

//Loop through the array updating the values so that they can easily be used to generate SQL
    for ($i = 0; $i < sizeof($toInsert); $i++) {
        //If the value is null and an FK set a default value
        if ($toInsert[$i][1] == 'NULL' && strpos($toInsert[$i][0], 'FK')) {
            $toInsert[$i][1] = 0;

            //Check to see if the field should contain a foreign key.
        } else if (strpos($toInsert[$i][0], 'FK')) {

            //Run a query to check if the value already exists in a linked table. Look for null value if necessary
            $selectQuery = "SELECT * FROM " . $toInsert[$i][2] . " WHERE " . $toInsert[$i][2] . "." . $toInsert[$i][2] . " = '" . $toInsert[$i][1] . "'";

            if (recordCount($selectQuery, $con) == 0) {
                //The record does not exist. Create it
                //When inserting the FK has to be converted to PK

                runQuery("INSERT INTO " . $toInsert[$i][2] . " (" . $toInsert[$i][2] . "PK, " . $toInsert[$i][2] . ") VALUES (NULL , '" . $toInsert[$i][1] . "')", $con);
            }
            //Run a query to get the primary key of the value
            $key = getPK($selectQuery, $con);

            //If the key is empty set it to default to 0
            if (empty($key)) {
                $key = 0;
            }

            //Update the value in the insert array
            $toInsert[$i][1] = $key;
        }
    }

    //Generate the insert statement
    $insert = "INSERT INTO " . $tableToUpdate . " (uniqueKey, ";
    $values = "VALUES (NULL, ";
    $duplicateSelect = "SELECT * FROM " . $tableToUpdate;
    $duplicateWhere = " WHERE ";

    for ($i = 0; $i < sizeof($toInsert); $i++) {
        //Generate the fields
        $insert = $insert . $toInsert[$i][0];
        if ($toInsert[$i][1] == "NULL") {
            $values = $values . $toInsert[$i][1];
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
function updateValues(array $toInsert, mysqli $con)
{
    for ($i = 0; $i < sizeof($toInsert); $i++) {
        //Ignore tables that have no associated foreign key
        if (!is_null($toInsert[$i][0])) {

            //Look for the value in the linked table
            //If the value is null the foreign key should be set to 0
            if (is_null($toInsert[$i][2])) {
                $toInsert[$i][2] = 0;

                //Query the appropriate table to see if the record exists
            } else {
                $selectQuery = "SELECT * FROM " . $toInsert[$i][0] . " WHERE " . $toInsert[$i][0] . " = '" . $toInsert[$i][2] . "'";

                $PK = getPK($selectQuery, $con);
                if ($PK != false) {

                    //The record exists in this table. Update the insert array
                    $toInsert[$i][2] = $PK;
                } else {
                    //The record does not exist in this table. Perform a query to create it
                    $insertQuery = "INSERT INTO " . $toInsert[$i][0] . " (" . $toInsert[$i][0] . "PK, " . $toInsert[$i][0] . ")" . " VALUES (NULL, '" . $toInsert[$i][2] . "')";
                    runQuery($insertQuery, $con);

                    //Query the database again to get the new foreign key
                    $toInsert[$i][2] = getPK($selectQuery, $con);
                }
            }
        }
    }
    return $toInsert;
}

//Converts $_FILES to a cleaner array when uploading multiple files
function reArrayFiles($file_post)
{
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

//Takes a query and returns a single value (the PK)
function getPK($query, $con)
{
    $primary = $val = null;
    $query = $con->prepare($query);

    //Look for errors
    if (false === $query) {
        die("Prepare failed with error: " . htmlspecialchars($con->error));
    }

    $query->execute();
    $query->bind_result($primary, $val);
    $query->store_result();
    $recordCount = $query->num_rows();

    //Return false if there are no rows returned
    if ($recordCount == 0) {
        return false;
    }

    $key = "";
    while ($row = $query->fetch()) {
        $key = $primary;
    }
    $query->close();
    return $key;
}

//Checks the maximum length of a value. returns an array with a true/false flag, the values length and a shortened version of the value
function checkLength($value, $maxLen)
{
    if (strlen($value) > $maxLen) {
        return [false, strlen($value), trimString($value, $maxLen)];
    }
    return [true];
}

//shortens a value to 40 chars including (...). Returns the result.
function trimString($value, $max)
{
    return substr($value, 0, $max - 3) . "...";
}

//Checks to see if a value is a member of the supplied values. returns true if it is.
function isMemberOf($value, $parameters)
{
    for ($i = 0; $i < sizeof($parameters); $i++) {
        if (strpos($parameters[$i], $value) !== false) {
            return true;
        }
    }

    return false;
}

//Checks to see if a value is of the specified type. Returns true if it is, false otherwise
function isType($type, $value)
{
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
function containsIllegalCharacter($value)
{
    $characters = ['“', '”', '"', '"', '«', '»', '「', '」', '`'];
    for ($i = 0; $i < sizeof($characters); $i++) {
        //Check if the character is contained in the value
        if (strpos($value, $characters[$i]) !== false) {
            return $characters[$i];
        }
    }

    //Return false
    return null;
}

//Takes a value and checks to see if it is empty. Return true if it is
function isEmpty($value)
{
    if (sizeof($value) == 0 || $value == "" || empty($value) || $value == '') {
        return true;
    }
    return false;
}

//Takes a 2d array and returns the position of a specified value
function findIn2dArray($array, $value)
{
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

//Run a specified query
function runQuery($query, $con)
{
    //execute the query
    $query = $con->prepare($query);
    $query->execute();

    //Close the query
    $query->close();
}

//Executes and returns a query so that the variables can be stored and bound as required
function runAndReturn($query, mysqli $con)
{
    $query = $con->prepare($query);

    //Look for errors
    if (false === $query) {
        die("Prepare failed with error: " . htmlspecialchars($con->error));
    }
    $query->execute();
    return $query;
}

//Get the number of times that a foreign key is used in the education table
function numTimesFkUsedEducation($query, $con)
{
    //Declare values
    $uniqueKey = $institutionFK = $subject = $gradeFK = $subjectLevelFK = $yearFk = $subjectCodeFK = $creditsFK = $creditsFK = $codeExtensionFK = null;

    $query = runAndReturn($query, $con);
    $query->bind_result($uniqueKey, $institutionFK, $subject, $gradeFK, $subjectLevelFK, $yearFk, $subjectCodeFK, $creditsFK, $codeExtensionFK);
    $query->store_result();
    $recordCount = $query->num_rows();
    $query->close();
    return $recordCount;
}

//Checks if a record exists in a linked table. Returns the number of records
function recordCount($query, $con)
{
    $key = "";
    $result = "";
    $query = $con->prepare($query);
    $query->execute();
    $query->bind_result($key, $result);
    $query->store_result();
    $recordCount = $query->num_rows();
    $query->close();
    return $recordCount;
}

//Looks for duplicate records in the given table. Takes a the table name. An array of fields and and array of values.
//Returns true if a duplicate is found
function findDuplicate($query, $con)
{

    //Run the query and get the number of rows
    $recordCount = 0;
    foreach ($con->query($query) as $row) {
        $recordCount++;
    }
    return $recordCount;
}

//Takes a value, strips the spaces and returns it
function stripSpaces($value)
{
    return str_replace(' ', '', $value);
}

//Takes an image file and adjusts the size
function resize_image($file, $w, $h, $crop = true)
{
    list($width, $height) = getimagesize($file);

    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width - ($width * abs($r - $w / $h)));
        } else {
            $height = ceil($height - ($height * abs($r - $w / $h)));
        }
        $newWidth = $w;
        $newHeight = $h;
    } else {
        if ($w / $h > $r) {
            $newWidth = $h * $r;
            $newHeight = $h;
        } else {
            $newHeight = $w / $r;
            $newWidth = $w;
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

    $dst = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    return $dst;
}