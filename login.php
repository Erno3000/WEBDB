<?php
    $title="Logging in...";
	/* If no login was submitted, or the user is already logged in, return to the index page. */
    if(!$_POST || $_SESSION['loggedin']) {
        header('Location: index.php');
    }

    /* Set up a new connection to the database. */
    include('dbconnect.php');

    /* If the connection failed, bail out. */
    if (!$mysqli) {
        die('Could not connect: ' . mysql_error());
    }

    /* Prepare the query. */
    $query = 'SELECT user_id, username, password, rank FROM Users
                WHERE username=? LIMIT 1';

    /* First we prepare the query by replacing the question marks
     * in the query with the variables with the actual values.
     * Also, the given password is hashed, so we can check if it
     * matches the hashed password in the database.
     */
    include('crypto.php');

    if($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('s', $_POST["username"]);

        /* Then we execute the query. */
        if($stmt->execute()) {
            /* Check to see if it's a valid user. */
            if($stmt->store_result() && $stmt->num_rows == 1) {
                /* Bind the result to these variables and fetch
                 * the result of the query.
                 */
                $stmt->bind_result($id, $user, $pass, $rank);
                $stmt->fetch();

                /* And if the given password matches the one in the
                 * database, it's a valid login.
                 */
                if (checkPassword($pass, $_POST["pwd"])) {
                    /* Start the session, and initialise the Session variables with the correct values. */
                    session_start();
                    $_SESSION['id'] = $id;
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $user;
                    $_SESSION['rank'] = $rank;

                    /* Sent the user back to the index page, as he logged in with a valid account. */
                    header('Location: index.php');
                } else {
                    /* Apparantly it is not a valid password, since
                     * it didn't match the one in the database.
                     */
                    header('Location: index.php');
                }
            }

            else {
                /* Apparantly it is not a valid user, because
                 * there are no results.
                 */
                header('Location: index.php');
            }
        }
    }
    /* We are now done with the login and the php script. */
?>