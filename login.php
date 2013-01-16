<?php 
	session_start();
	$_SESSION['loggedin']=false;
	$_SESSION['username']="username";
	$_SESSION['rank']=0;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Login</title>
        <link rel="stylesheet" type="text/css" href="login_style.css" />
        <meta http-equiv="REFRESH" content="3;url=index.php">
    </head>

    <body>
    	<div id="login">
    		<?php 
    			/* If no login was submitted, bail out. */
    			if(!$_POST) {
    				die('No Login done!');
    			}
    			
    			/* If we are already logged in, tell the user and return. */
    			if ($_SESSION['loggedin']) {
    				die('Already logged in!');
    				//Sent the user back to the index page.
    			}
    			
    			include("config.php");
				
				/* Set up a new connection with the variables defined in
				 * "config.php" 
				 */
				$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    			
    			/* If the connection failed, bail out. */
    			if (!$mysqli) {
    				die('Could not connect: ' . mysql_error());
    			}
    			
    			/* Prepare the query. */
    			$query = 'SELECT username, password, rank FROM Users
    						WHERE username=? AND password=? LIMIT 1';
    			
    			/* First we prepare the query by replacing the question marks
    			 * in the query with the variables with the actual values.
    			 */
    			if($stmt = $mysqli->prepare($query)) {
    				$stmt->bind_param('ss', $_POST["username"], $_POST["pwd"]);
    				
    				/* Then we execute the query. */
					if($stmt->execute()) {
						/* If there is 1 result (thus a valid login), show it.
						 */
						if($stmt->store_result() && $stmt->num_rows == 1) {
							/* Bind the result to these variables: */
							$stmt->bind_result($user, $pass, $rank);
							$stmt->fetch();
							
							echo "You are now logged in, returning to the " . 
								"home page in 3 seconds... <br />";
							/*
							echo "Username: " . $user . "<br />";
							echo "Password: " . $pass . "<br />";
							echo "Rank: " . $rank . "<br /> <br />";
							*/
							
							$_SESSION['loggedin'] = true;
							$_SESSION['username'] = $user;
							$_SESSION['rank'] = $rank;
						} else {
							/* Apparantly it is not a valid login, because
							 * there are no results.
							 */
							echo "Invalid login!";
						}
					}
				}
				/* We are now done with the login and the php script. */
    		?>
		</div>
    </body>
</html>


