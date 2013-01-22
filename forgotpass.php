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

	<form id="forgotpwform" method=post action="forgotpass.php"> <fieldset class="normal">
		<input type="email" class="css3text" name="email" id="email"
			placeholder="Please enter your email..." />
		<input type="submit" class="css3button" id="submit" value="Reset password" />
	</fieldset> </form>
	
	<?php
		if ($_POST) {
			/* Set up a new connection to the database. */
			include('dbconnect.php');
			    			
			/* If the connection failed, bail out. */
			if (!$db) {
				die('Could not connect: ' . mysql_error());
			}
			
			/* Prepare the query. */
			$query = 'SELECT user_id, username, email FROM Users
						WHERE email=? LIMIT 1';
			/* First we prepare the query by replacing the question marks
    		 * in the query with the variables with the actual values.
    		 */
    		if($stmt = $db->prepare($query)) {
				$stmt->bindValue(1, $_POST["email"], PDO::PARAM_STR);
				
				/* Then we execute the query. */
				if($stmt->execute()) {
					/* Check to see if it's a valid user. */
					if($stmt->rowCount() == 1) {
						/* Fetch the result of the query. */
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						
						echo 'Resetting username "' . $row['username'] . '"... <br />';
						echo 'Please check your email (' . $row['email'] . ')';
					} else {
						echo 'Invalid email! Are you sure you typed it correctly?';
					}
				}
			}
		}
	?>
</div>

<?php include('footer.php'); ?>
