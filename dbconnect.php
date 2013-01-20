<?php
  /* Set up a new connection with the variables defined in
	 * "config.php"
	 */
	include('config.php');
	$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
?>
