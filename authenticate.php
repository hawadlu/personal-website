<?php
    session_start();
    require("connect.php");

    //Now we check if the data from the login form was submitted, isset() will check if the data exists.
    if ( !isset($_POST['username'], $_POST['password']) ) {
        // Could not get the data that should have been sent.
        die ('Please fill both the username and password field!');
    }

    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.
    if ($stmt = $con->prepare('SELECT users.id, users.password FROM users WHERE userName = ?;')) {
        // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $password);
            $stmt->fetch();
            // Account exists, now we verify the password.
            // Note: remember to use password_hash in your registration file to store the hashed passwords.

            if (hash('sha256', $_POST['password']) == $password) {
                // Verification success! User has loggedin!
                // Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['username'];
                $_SESSION['id'] = $id;

                //Redirect to the edit page
                header("location: edit.php");
            } else {
                redirectWithError('Incorrect username or password', 'login.php');
                echo 'Incorrect password!';
            }
        } else {
            redirectWithError('Incorrect username or password', 'login.php');
        }

        $stmt->close();
    }

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
?>

