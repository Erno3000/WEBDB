<?php
$title = "Account settings";
include('header.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: agenda.php');
}
?>

<div id="content">
    <!--<h1>Edit or review your account settings here.</h1><br/>-->

    <?php
    /* Set up a new connection to the database. */
    include('dbconnect.php');
    include('crypto.php');

    /* If the connection failed, bail out. */
    if (!$db) {
        die('Could not connect: ' . mysql_error());
    }

    /* Prepare the query itself. */
    $query = 'SELECT * FROM Users WHERE user_id=? LIMIT 1';

    /* First we prepare the query for execution, by replacing the question marks in the query
     * with the variables of the actual values.
     */
    if ($stmt = $db->prepare($query)) {
        $stmt->bindValue(1, $_SESSION['id'], PDO::PARAM_INT);

        /* Then we execute the query. */
        if ($stmt->execute()) {
            /* And output it in a form. */
            $n = $stmt->rowCount();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            echo '<h1>Account information:</h1>';
            echo '<ul> <li>Username: ' . $row['username'] . '</li>';
            echo '<li>First name: ' . $row['first_name'] . '</li>';
            echo '<li>Last name: ' . $row['last_name'] . '</li>';
            echo '<li>Email: ' . $row['email'] . '</li>';

            if ($row['rank'] == -1) {
                echo '<li>Rank: Unactivated user, please check your email.</li>';
            }

            if ($row['rank'] == 0) {
                echo '<li>Rank: Activated user</li>';
            }

            if ($row['rank'] == 1) {
                echo '<li>Rank: Author</li>';
            }

            if ($row['rank'] == 2) {
                echo '<li>Rank: Moderator</li>';
            }

            if ($row['rank'] == 3) {
                echo '<li>Rank: Admin</li>';
            }

            echo '</ul> <br />';

            echo '<h1>Edit Account Information</h1>';

            echo '<form action="account_settings.php" method="post" id="accountsettingsform"> <fieldset>
            <legend>Edit only the fields which you would like to change</legend> <ul>
                <li> <label for="currentpassword">Current password:</label>
                    <input type="password" class="css3text" name="currentpassword" id="currentpassword" /> <br /> </li>
                <li> <label for="newpassword">New password:</label>
                    <input type="password" class="css3text" name="newpassword" id="newpassword" /> <br /> </li>
                <li> <label for="newpasswordretyped">Retype new password:</label>
                    <input type="password" class="css3text" name="newpasswordretyped" id="newpasswordretyped" /> <br /> </li>
                <li> <label for="newemail">Email:</label>
                    <input type="email" class="css3text" name="newemail" id="newemail" value=' . $row['email'] . '> <br /> </li>
                <li> <input type="submit" value="Save changes" /> </li>
            </ul> </fieldset> </form> <br />';

            if ($_POST) {
                $changed_something = false;

                if(isset($_POST['currentpassword']) && isset($_POST['newpassword']) && isset($_POST['newpasswordretyped'])
                && !empty($_POST['currentpassword']) && !empty($_POST['newpassword']) && !empty($_POST['newpasswordretyped'])) {
                    echo '1, ';

                    if (!checkPassword($row['password'], $_POST['currentpassword'])) {
                        echo "<p>The current password doesn't match your actual current password!</p>";
                    } else if ($_POST['newpassword'] != $_POST['newpasswordretyped']) {
                        echo "<p>The new passwords didn't match! Please try again.</p>";
                    } else {
                        $query_changepw = 'UPDATE Users SET password=? WHERE user_id=? LIMIT 1';
                        echo '2, ';

                        if($stmt = $db->prepare($query_changepw)) {
                            $stmt->bindValue(1, hashPassword($_POST["newpassword"]), PDO::PARAM_STR);
                            $stmt->bindValue(2, $row['user_id'], PDO::PARAM_INT);
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

                if (isset($_POST['newemail']) && $_POST['newemail'] != $row['email'] && !empty($_POST['newemail'])) {
                    $query_changeemail = 'UPDATE Users SET email=? WHERE user_id=? LIMIT 1';
                    echo '8, ';

                    if($stmt = $db->prepare($query_changeemail)) {
                        $stmt->bindValue(1, $_POST['newemail'], PDO::PARAM_STR);
                        $stmt->bindValue(2, $row['user_id'], PDO::PARAM_INT);
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
?>
</div>

<?php include('footer.php'); ?>
