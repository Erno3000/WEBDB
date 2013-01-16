<?php
	session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Logging out...</title>
        <link rel="stylesheet" type="text/css" href="login_style.css" />
        <meta http-equiv="REFRESH" content="3;url=index.php">
    </head>

    <body>
    	<div id="logout">
    		<?php 
    			/* If we are already logged out, tell the user and return. */
    			if (!isset($_SESSION['loggedin'])) {
    				die('Already logged out! DERP!');
    				/* Sent the user back to the index page in 3 seconds... */
    			}
    			
    			//unset($_SESSION['loggedin']);
    			session_destroy();
    			    			
    			/* We are now done with the logout and the php script. */
				echo 'Successfully logged out, returning to the index page in ' .
					'3 seconds...';
    		?>
		</div>
    </body>
</html>


