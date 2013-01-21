<?php
	$title = "Approve User";
	include('header.php'); 
?>
<div id="content">
	<h1>Select a user to edit or approve:</h1><br />
		
	<?php
		/* Set up a new connection to the database. */
		include('dbconnect.php');
		    			
		/* If the connection failed, bail out. */
		if (!$mysqli) {
			die('Could not connect: ' . mysql_error());
		}
		
		/* Prepare the query. */
		$query = 'SELECT user_id, username, first_name, last_name, email, rank FROM Users
			ORDER BY user_id';
		
		/* First we prepare the query for execution. */
		if($stmt = $mysqli->prepare($query)) {
			/* Then we execute the query. */
			if($stmt->execute()) {
				/* And output it in an HTML table. */
				if ($stmt->store_result()) {
					$stmt->bind_result($id, $user, $fname, $lname, $email, $rank);
					echo '<table border="1" id="userstable"> <tr>' .
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
							'<td>' . $email . '</td>';

                            if ($rank == -1) {
                                echo '<td>Unactivated User</td>';
                            }
							
							if ($rank == 0) {
								echo '<td>User</td>';
							}
							
							if ($rank == 1) {
								echo '<td>Author</td>';
							}
							
							if ($rank == 2) {
								echo '<td>Moderator</td>';
							}
							
							if ($rank == 3) {
								echo '<td>Admin</td>';
							}
							
							if ($rank < -1 or $rank > 3) {
								echo '<td>Hacker</td>';
							}
								
							echo '</tr>';
					}
					
					echo '</table>';
				}
			}
		}
		/* We are now done with the php script. */
	?>
</div>
<?php include('footer.php'); ?>
