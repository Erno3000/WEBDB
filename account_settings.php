<?php
$title = "Account settings";
include('header.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
}
?>

<div id="accountsettings">
    <h1>Edit or review your account settings here.</h1><br/>

    <?php
    /* Set up a new connection to the database. */
    include('dbconnect.php');
    include('crypto.php');

    /* If the connection failed, bail out. */
    if (!$mysqli) {
        die('Could not connect: ' . mysql_error());
    }

    /* Prepare the query itself. */
    $query = 'SELECT * FROM Users WHERE user_id=? LIMIT 1';

    /* First we prepare the query for execution, by replacing the question marks in the query
     * with the variables of the actual values.
     */
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('i', $_SESSION['id']);

        /* Then we execute the query. */
        if ($stmt->execute()) {
            /* And output it in a form. */
            if ($stmt->store_result()) {
                $stmt->bind_result($id, $fname, $lname, $username, $password, $email, $rank);
                $stmt->fetch();

                echo '<h2>Account information:</h2>';
                echo '<ul> <li>Username: ' . $username . '</li>';
                echo '<li>First name: ' . $fname . '</li>';
                echo '<li>Last name: ' . $lname . '</li>';
                echo '<li>Email: ' . $email . '</li>';

                if ($rank == -1) {
                    echo '<li>Rank: Unactivated user, please check your email.</li>';
                }

                if ($rank == 0) {
                    echo '<li>Rank: Activated user</li>';
                }

                if ($rank == 1) {
                    echo '<li>Rank: Author</li>';
                }

                if ($rank == 2) {
                    echo '<li>Rank: Moderator</li>';
                }

                if ($rank == 3) {
                    echo '<li>Rank: Admin</li>';
                }

                echo '</ul> <br />';

                echo '<h2>Edit Account Information</h2>';
                echo '<p>Edit only the fields which you would like to change</p>';

                echo '<form action="account_settings.php" method="post">
                    <label for="currentpassword">Current password:</label>
                        <input type="password" class="css3text" name="currentpassword" id="currentpassword" /> <br />
                    <label for="newpassword">New password:</label>
                        <input type="password" class="css3text" name="newpassword" id="newpassword" /> <br />
                    <label for="newpasswordretyped">Retype new password:</label>
                        <input type="password" class="css3text" name="newpasswordretyped" id="newpasswordretyped" /> <br />
                    <label for="newemail">Email:</label>
                        <input type="email" class="css3text" name="newemail" id="newemail" value=' . $email . '> <br />
                    <input type="submit" value="Save changes" />
                </form> <br />';

                if ($_POST) {
                    $changed_something = false;

                    if(isset($_POST['currentpassword']) && isset($_POST['newpassword']) && isset($_POST['newpasswordretyped'])) {
                        echo '1, ';

                        if (!checkPassword($password, $_POST['currentpassword'])) {
                            echo "<p>The current password doesn't match your actual current password!</p>";
                        } else if ($_POST['newpassword'] != $_POST['newpasswordretyped']) {
                            echo "<p>The new passwords didn't match! Please try again.</p>";
                        } else {
                            $query_changepw = 'UPDATE Users SET password=? WHERE user_id=? LIMIT 1';
                            echo '2, ';

                            if($stmt = $mysqli->prepare($query_changepw)) {
                                $stmt->bind_param('ss', hashPassword($_POST["newpassword"]), $id);
                                echo '3, ';
                                /* Then we execute the query. */
                                if($stmt->execute()) {
                                    echo '<p>Successfully changed your password!</p>';
                                    $changed_something = true;
                                }
                                echo '4, ';
                            }

                            echo '5, ';
                        }
                        echo '6, ';
                    }

                    echo '7, ';

                    if (isset($_POST['newemail']) && $_POST['newemail'] != $email) {
                        $query_changeemail = 'UPDATE Users SET email=? WHERE user_id=? LIMIT 1';
                        echo '8, ';

                        if($stmt = $mysqli->prepare($query_changeemail)) {
                            $stmt->bind_param('ss', $_POST['newemail'], $id);
                            echo '9, ';

                            /* Then we execute the query. */
                            if($stmt->execute()) {
                                echo '<p>Successfully changed your email!</p>';
                                $changed_something = true;
                            }
                            echo '10, ';
                        }
                        echo '11, ';
                    }

                    echo '12, ';

                    if (!$changed_something) {
                        echo '<p>Nothing has changed, so there is nothing to save!</p>';
                    }
                    echo '13.';
                }
            }
        }
    }
?>
</div>

<?php include('footer.php'); ?>
