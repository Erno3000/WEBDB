<?php
	$title = "Approve User";
	include('header.php');
    requireRank("ADMIN");
?>
<div id="content">
	<h1>Select a user to edit or approve:</h1><br />
		
	<?php
		/* Set up a new connection to the database. */
		include('dbconnect.php');
		    			
		/* If the connection failed, bail out. */
		if (!$db) {
			die('Could not connect: ' . mysql_error());
		}
		
		/* Prepare the query. */
		$query = 'SELECT user_id, username, first_name, last_name, email, rank FROM Users
			ORDER BY user_id';
		
		/* First we prepare the query for execution. */
		if($stmt = $db->prepare($query)) {
			/* Then we execute the query. */
			if($stmt->execute()) {
				/* And output it in an HTML table. */
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo '<table border="1" id="userstable"> <tr>' .
                    '<th>Username</th>' .
                    '<th>First name</th>' .
                    '<th>Last name</th>' .
                    '<th>Email</th>' .
                    '<th>Rank</th> </tr>';

                foreach($results as $row) {
                    echo '<tr>' .
                        '<td>' . $row['username'] . '</td>' .
                        '<td>' . $row['first_name'] . '</td>' .
                        '<td>' . $row['last_name'] . '</td>' .
                        '<td>' . $row['email'] . '</td>';

                        if ($row['rank'] == -1) {
                            echo '<td>Unactivated User</td>';
                        }

                        if ($row['rank'] == 0) {
                            echo '<td>User</td>';
                        }

                        if ($row['rank'] == 1) {
                            echo '<td>Author</td>';
                        }

                        if ($row['rank'] == 2) {
                            echo '<td>Moderator</td>';
                        }

                        if ($row['rank'] == 3) {
                            echo '<td>Admin</td>';
                        }

                        if ($row['rank'] < -1 or $row['rank'] > 3) {
                            echo '<td>Hacker</td>';
                        }

                        echo '</tr>';
                }

                echo '</table>';
			}
		}
		/* We are now done with the php script. */
	?>
</div>
<?php include('footer.php'); ?>
