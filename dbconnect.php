<?php
  /* Set up a new connection with the variables defined in
	 * "config.php"
	 */
	include('config.php');
	// "Old" method of making a connection:
	//$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    /* New method of making a database connection: */
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=UTF-8", $dbUser, $dbPass )
?>
