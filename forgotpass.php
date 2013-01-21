<?php
    $title = "Forgot password";
    include('header.php');
    if (isset($_SESSION['loggedin'])) {
        header('Location: agenda.php');
    }
?>
<div id="content">
	<h1>Please enter your email.</h1>
	<p>An email with a new, temporary password will be sent to you.<br />
	Don't forget to change it afterwards!</p>
	<form id="forgotpwform" method=post action="forgotpass.php">
		<input type="text" class="css3text" name="email" id="email" 
			placeholder="Enter your email here..." />
		<input type="submit" class="css3button" id="submit" value="Reset password" />
	</form>
	
	<?php
		if ($_POST) {
			/* Set up a new connection to the database. */
			include('dbconnect.php');
			    			
			/* If the connection failed, bail out. */
			if (!$mysqli) {
				die('Could not connect: ' . mysql_error());
			}
			
			/* Prepare the query. */
			$query = 'SELECT user_id, username, email FROM Users
						WHERE email=? LIMIT 1';
			/* First we prepare the query by replacing the question marks
    		 * in the query with the variables with the actual values.
    		 */
    		if($stmt = $mysqli->prepare($query)) {
				$stmt->bind_param('s', $_POST["email"]);
				
				/* Then we execute the query. */
				if($stmt->execute()) {
					/* Check to see if it's a valid user. */
					if($stmt->store_result() && $stmt->num_rows == 1) {
						/* Bind the result to these variables and fetch
						 * the result of the query.
						 */
						$stmt->bind_result($id, $user, $email);
						$stmt->fetch();
						
						echo 'Resetting username "' . $user . '"... <br />';
						echo 'Please check your email (' . $email . ')';
					} else {
						echo 'Invalid email! Are you sure you typed it correctly?';
					}
				}
			}
		}
	?>
</div>

<?php include('footer.php'); ?>
