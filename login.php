<?php 
	session_start();
	$_SESSION['id']=-1;
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
							
								echo "You are now logged in, returning to the " . 
									"home page in 3 seconds... <br />";
								
								$_SESSION['id'] = $id;
								$_SESSION['loggedin'] = true;
								$_SESSION['username'] = $user;
								$_SESSION['rank'] = $rank;
							} else {
								/* Apparantly it is not a valid password, since
								 * it didn't match the one in the database.
								 */
								echo "Invalid login!";
							}
						}
						
						else {
							/* Apparantly it is not a valid user, because
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


