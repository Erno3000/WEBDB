<?php

    include('header.php');
    include('config.php');

    //Alleen uitvoeren wanneer het een post request is, dus alleen wanneer het formulier wordt gesubmit
    if ($_POST) {
        //Maak een mysqli object aan met de gegevens uit config.php
        $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        //Query om te checken of de username al bestaat
        $queryUsername = "SELECT * FROM Users WHERE username = ?";

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
                requireLength($fname, 1, 30, "First name");
            } else {
                $fname = "";
            }



            //Plaats de geposte data (gelinked aan id's uit het form) in variabelen
            $lname = $_POST['lname'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $pass1 = $_POST['pass1'];
            $pass2 = $_POST['pass2'];

            if(isset($_POST['preposition'])) {
                requireLength($_POST['preposition'], 1, 30, "Preposition");
                $lname .= ',' . $_POST['preposition'];
            }

            //TODO MAX LEN! 20 max voor pass
            //geen cijfers in namen
            //tussenvoegsel met , bij achternaam
            //

            //Check de lengte van de geposte data
            requireLength($lname, 1, 60,  "Last name");
            requireLength($username, 6, 20, "Username");
            requireLength($pass1, 6, 20, "Password");
            requireLength($pass2, 6, 20, "Password");

            if($pass1 != $pass2) {
                error("Password should be equal to check password!");
            }

            //Check of de email valid is
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error("Invalid email!");
            }


            //Assign de gepreparede query (voor username check) aan de var stmt
            if($stmt = $mysqli->prepare($queryUsername)) {
                //Bind de geposte data aan het ? in de query
                $stmt->bind_param('s', "'$username'");

                if(!$stmt->execute()) {
                    echo 'Het uitvoeren van de query is mislukt: '.$stmt->error.' in query: '.$queryUsername;
                } else  {
                    //Als er 0 rows matchen is de username niet in gebruik
                    $count = $stmt->num_rows;
                    echo "num rows: $count";
                    if($count > 0) {
                        $stmt->close();
                        error("Username is already used!");
                    }

                }

                $stmt->close();
            } else {
                echo 'Er zit een fout in de query: '.$mysqli->error;
            }

            if($stmt = $mysqli->prepare($queryRegister))
            {
                //TODO remove ofc
                echo "$fname, $lname, $preposition, $username, $pass1, $email";

                $stmt->bind_param('sssss', $fname, $lname, $username, $pass1, $email);

                if(!$stmt->execute()) {
                    echo 'Het uitvoeren van de query is mislukt: '.$stmt->error.' in query: '.$queryRegister;
                }

                $stmt->close();
            } else {
                echo 'Er zit een fout in de query: '.$mysqli->error;
            }

            echo 'Registered.';

        }





    }





    if(mysqli_connect_errno()) {
        trigger_error('Fout bij verbinding: '.$mysqli->error);
    }

    function error($msg) {
        echo $msg;
        exit;
    }

    function requireLength($field, $minLen, $maxLen, $msg) {
        if(strlen($field) < $minLen || strlen($field) > $maxLen) {
            echo $msg . " does not meet the length requirements: minimum $minLen and maximum $maxLen characters";
            exit;
        }
    }





?>

<div id="content">
    <h1>Create a new account</h1>
        <div id="caform">            
            <form action="register.php" method="post">
                <fieldset>
                    <legend>Fill in this form to create a new account.</legend>                
                <ul>
                    <li>
                        <label for="fname">First name:</label>
                        <input type="text" id="fname" name="fname" size="25" />
                    </li>
                    <li>
                        <label for="preposition">Preposition</label>
                        <input type="text" id="preposition" name="preposition" size="25" />
                    </li>
                    <li>
                        <label for="lname">Last name:*</label>
                        <input type="text" id="lname" name="lname" size="25" />
                    </li>
                    <li>
                        <label for="username">Username:*</label>
                        <input type="text" id="username" name="username" size="25" />
                    </li>
                    <li>
                        <label for="email">Email adress:*</label>
                        <input type="text" id="email" name="email" size="25" />
                    </li>
                    <li>
                        <label for="pass1">Password:*</label>
                        <input type="password" id="pass1" name="pass1" size="25" />
                    </li>
                    <li>
                        <label for="pass2">Retype password:*</label>
                        <input type="password" id="pass2" name="pass2" size="25" />
                    </li>
                    <li>
                        <label for="submit">&nbsp;</label>
                        <input type="submit" id="submit" name="submit" value="Submit" />
                    </li>
                </ul>
                <p class="rule"><br /><br />Fields with an * are mandatory.</p>
                </fieldset>
            </form>
        </div>
</div>

<?php

    include('footer.php');

?>

</body>
</html>
