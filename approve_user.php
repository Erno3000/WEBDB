<?php
	session_start();
	$title = "Approve User";
?>


<?xml version="1.1" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title><?php echo $title ?></title>
    <link rel="stylesheet" href="css/forms.css" />
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/calendar.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />    
</head>

<body>
	<?php include('header.php'); ?>
	<h1>Approve or edit users for author or moderator.</h1>
	<p>Select a user to edit or approve:</p>
		
	<?php>
		/* Set up a new connection to the database. */
		include('dbconnect.php');
		    			
		/* If the connection failed, bail out. */
		if (!$mysqli) {
			die('Could not connect: ' . mysql_error());
		}
		
		/* Prepare the query. */
		$query = 'SELECT user_id, username, first_name, last_name, email, rank FROM Users
			SORT BY user_id';
		
		/* First we prepare the query for execution. */
		if($stmt = $mysqli->prepare($query)) {
			//$stmt->bind_param('ss', $_POST["username"], $_POST["pwd"]);
			
			/* Then we execute the query. */
			if($stmt->execute()) {
				/* And output it in an HTML table. */
				if ($stmt->store_result()) {
					$stmt->bind_result($id, $user, $fname, $lname, $email, $rank);
					
					echo '<table border="1"> <tr>' .
						'<th>ID</th>' .
						'<th>Username</th>' .
						'<th>First name</th>' .
						'<th>Last name</th>' .
						'<th>Email</th>' .
						'<th>Rank</th> </tr>';
										
					while ($stmt->fetch()) {
						echo '<tr>' .
							'<td>' . $id . '</td>' .
							'<td>' . $user . '</td>' .
							'<td>' . $fname . '</td>' .
							'<td>' . $lname . '</td>' .
							'<td>' . $rank . '</td> </tr>';
					}
					
					echo '</table>';
				}
			}
		}
		/* We are now done with the php script. */
	?>
		
	
	<?php include('footer.php'); ?>
</body>
</html>
