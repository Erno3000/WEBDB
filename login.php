<?php
    $title="Logging in...";
	/* If no login was submitted, or the user is already logged in, return to the index page. */
    if(!$_POST || isset($_SESSION['loggedin'])) {
        header('Location: agenda.php');
    }

    /* Set up a new connection to the database. */
    include('dbconnect.php');

    /* If the connection failed, bail out. */
    if (!$db) {
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

    if($stmt = $db->prepare($query)) {
        $stmt->bindValue(1, $_POST["username"], PDO::PARAM_STR);

        /* Then we execute the query. */
        if($stmt->execute()) {
            /* Check to see if it's a valid user. */
            $n = $stmt->rowCount();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($results as $row) {
                /* If there is 1 row in the result set, then the username is valid. */
                if($n == 1) {
                    /* And if the given password matches the one in the
                     * database, it's a valid login.
                     */
                    if (checkPassword($row['password'], $_POST["pwd"])) {
                        /* Start the session, and initialise the Session variables with the correct values. */
                        session_start();
                        $_SESSION['id'] = $row['user_id'];
                        $_SESSION['loggedin'] = true;
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['rank'] = $row['rank'];

                        /* Sent the user back to the agenda page, as he logged in with a valid account. */
                        header('Location: agenda.php');
                    } else {
                        /* Apparantly it is not a valid password, since
                         * it didn't match the one in the database.
                         */
                        header('Location: agenda.php');
                    }
                } else {
                    /* Apparantly it is not a valid user, because
                     * there are no results.
                     */
                    header('Location: agenda.php');
                }
            }
        }
    }
    /* We are now done with the login and the php script. */
?>