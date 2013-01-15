<?php

$title = 'Register';


//TODO
//geen cijfers in namen

    include('header.php');
    include('config.php');

    $formError = false;

    //Alleen uitvoeren wanneer het een post request is, dus alleen wanneer het formulier wordt gesubmit
    if ($_POST) {
        $post = true;
        //Maak een mysqli object aan met de gegevens uit config.php
        $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        //Query om een user te registreren, gegevens worden ingevuld door variabelen te "binden", dwz invullen
        // op de plaats waar een ? staat
        $queryRegister = "INSERT INTO Users (first_name, last_name, username, password, email, rank)
          VALUES (?, ?, ?, ?, ?, 1)";

        //Alleen registreren wanneer alles (excl first name) is ingevoerd
        if(isset($_POST['lname']) && isset($_POST['username']) && isset($_POST['email'])
            && isset($_POST['pass1']) && isset($_POST['pass2'])) {

            //Wanneer first name is ingevuld, even de lengte checken
            if(isset($_POST['fname'])) {
                $fname = $_POST['fname'];
                requireLength($fname, 1, 30, $fnameError);
            } 

            //Plaats de geposte data (gelinked aan id's uit het form) in variabelen
            $lname = $_POST['lname'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $pass1 = $_POST['pass1'];
            $pass2 = $_POST['pass2'];

            if(isset($_POST['preposition'])) {
                $preposition = $_POST['preposition'];
                requireLength($preposition, 1, 30, $prepError);
            }

            //Check de lengte van de geposte data
            requireLength($lname, 1, 60,  $lnameError);
            requireLength($username, 6, 20, $usernameError);
            requireLength($pass1, 6, 20, $pass1Error);
            requireLength($pass2, 6, 20, $pass2Error);

            if($pass1 != $pass2) {
                $pass2Error = "Does not match!";
            }

            //Check of de email valid is
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailError = "Invalid email!";
            }

            if(!$formError && $stmt = $mysqli->prepare($queryRegister)) {
                $lnameprep = $lname . ',' . $preposition;
                $stmt->bind_param('sssss', $fname, $lnameprep, $username, $pass1, $email);

                if(!$stmt->execute()) {
                    error($usernameError, "Username in use!");
                } else {
                    echo 'Registered.';
                }

                $stmt->close();
            } else {
                echo 'Er zit een fout in de query: '.$mysqli->error;
            }

        }

    }

    if(mysqli_connect_errno()) {
        trigger_error('Fout bij verbinding: '.$mysqli->error);
    }

    function error(&$errorVar, $msg) {
        global $formError;
        $formError = true;
        $errorVar = $msg;
    }

    function requireLength($field, $minLen, $maxLen, &$error) {
        if(strlen($field) < $minLen || strlen($field) > $maxLen) {
            $error = "$minLen - $maxLen characters";
        }
    }

    function createField(&$prevVal, &$error, $id, $label, $mandatory, $type = 'text') {
        print '<li><label for="' . $id . '">' . $label . ':';
        if($mandatory) {
            print '*';
        }
        print '</label>';

        print '<input type="' . $type . '" id="' . $id . '" name="' . $id . '" size="25"';

        if(isset($prevVal) && $type == 'text') {
            print ' value="' . strip_tags($prevVal) . '"';
        }

        if(isset($error)) {
            print ' class="errorinput"';
        }
        print " />";
        if(isset($error)) {
            print '<span class="errormsg">' . $error . '</span>';
        }
        print '</li>';
    }


?>

<?php
    if(!isset($post) || (isset($post) && $formError)) {
        echo '<div id="content">
                <h1>Create a new account</h1>
                    <div id="caform">
                        <form action="register.php" method="post">
                            <fieldset>
                                <legend>Fill in this form to create a new account.</legend>
                                    <ul>';

        createField($fname, $fnameError, 'fname', 'First Name', false);
        createField($preposition, $prepError, 'preposition', 'Preposition', false);
        createField($lname, $lnameError, 'lname', 'Last Name', true);
        createField($username, $usernameError, 'username', 'Username', true);
        createField($email, $emailError, 'email', 'Email address', true);
        createField($pass1, $pass1Error, 'pass1', 'Password', true, 'password');
        createField($pass2, $pass2Error, 'pass2', 'Retype Password', true, 'password');

        echo '<li>
                <label for="submit">&nbsp;</label>
                    <input type="submit" id="submit" name="submit" value="Submit" />
              </li>
           </ul>
           <p class="rule"><br /><br />Fields with an * are mandatory.</p>
        </fieldset>
    </form>
  </div>
</div>';





    }

    include('footer.php');

?>

</body>
</html>
