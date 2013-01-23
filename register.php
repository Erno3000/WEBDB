<?php
    $title = 'Register';

    include('header.php');
    include('crypto.php');
    try {
        include('dbconnect.php');
    }
    catch(PDOException $ex) {
        echo "Error while connecting to dB: ", $ex->getMessage();
    }

    $formError = false;

    //Execute only if there is a post request, so just when the form is being submitted.
    if ($_POST) {
        $post = true;

        //Query to register a user, data is filled by binding variables, i.e. filling in in the places where a ? states
        $queryRegister = "INSERT INTO Users (first_name, last_name, username, password, email, rank)
          VALUES (?, ?, ?, ?, ?, -1)";

        //Only register when every field (ex first name) is entered.
        if(isset($_POST['lname']) && isset($_POST['username']) && isset($_POST['email'])
            && isset($_POST['pass1']) && isset($_POST['pass2'])) {

            //If first name is entered, check the length.
            if(isset($_POST['fname'])) {
                $fname = strip_tags($_POST['fname']);
                requireLength($fname, 0, 30, $fnameError);
            }

            //Set the posted data (linked to names from the form) into variables.
            $lname = strip_tags($_POST['lname']);
            $username = strip_tags($_POST['username']);
            $email = strip_tags($_POST['email']);
            $pass1 = strip_tags($_POST['pass1']);
            $pass2 = strip_tags($_POST['pass2']);

            //Check the length of the posted data.
            requireLength($lname, 1, 60,  $lnameError);
            requireLength($username, 6, 20, $usernameError);
            requireLength($pass1, 6, 20, $pass1Error);
            requireLength($pass2, 6, 20, $pass2Error);

            requireNoNumbers($fname, $fnameError);
            requireNoNumbers($lname, $lnameError);

            if($pass1 != $pass2) {
                error($pass2Error, "Does not match!");
            }

            //Check the validity of the email adress.
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error($emailError, "Invalid email!");
            }

            $pass1 = hashPassword($pass1);

            if(!$formError) {
                if($stmt = $db->prepare($queryRegister)) {
                    $stmt->bindValue(1, $fname, PDO::PARAM_STR);
                    $stmt->bindValue(2, $lname, PDO::PARAM_STR);
                    $stmt->bindValue(3, $username, PDO::PARAM_STR);
                    $stmt->bindValue(4, $pass1, PDO::PARAM_STR);
                    $stmt->bindValue(5, $email, PDO::PARAM_STR);

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
    }

    if(!$db) {
        trigger_error('Error when connecting: '.$db->error);
    }

    function error(&$errorVar, $msg) {
        global $formError;
        $formError = true;
        $errorVar = $msg;
    }

    function requireLength($field, $minLen, $maxLen, &$error) {
        if(strlen($field) < $minLen || strlen($field) > $maxLen) {
            error($error, "$minLen - $maxLen characters");
        }
    }

    function requireNoNumbers($str, &$error) {
        if(preg_match('#\d#', $str)) {
            $error = "No digits allowed";
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
	        			<form method="post" action="'. "{$_SERVER['PHP_SELF']}" . '">
						<fieldset>
            			<legend>Fill in this form to create a new account</legend>
							<ul>';

                                createField($fname, $fnameError, 'fname', 'First Name', false);
                                createField($lname, $lnameError, 'lname', 'Last Name', true);
                                createField($username, $usernameError, 'username', 'Username', true);
                                createField($email, $emailError, 'email', 'Email address', true);
                                createField($pass1, $pass1Error, 'pass1', 'Password', true, 'password');
                                createField($pass2, $pass2Error, 'pass2', 'Retype Password', true, 'password');

                           echo '<li>
				                    <br />
					                <label for="submit">&nbsp;</label>
						            <input type="submit" id="submit" name="submit" value="Submit" />
					            </li>
                           </ul>
				            <p class="mandatory"><br />Fields marked with an asterisk (*) are mandatory.</p>
                        </fieldset>
                    </form>
		        </div>
	        </div>';
    }

	include('footer.php');
?>