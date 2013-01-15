<?xml version="1.1" encoding="utf-8" ?>
<?php session_start(); 
	$_SESSION[];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title><?php echo $title ?></title>

    <link rel="stylesheet" href="css/forms.css" />
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/calendar.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/register.css" />
    <link rel="stylesheet" type="text/css" href="css/create_item.css" />
</head>

<body>

<div id="header">

    <div id="navigatie">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li class="sep"><a href="agenda.php">Agenda</a></li>
			<li class="sep"><a href="create_item.php">Create event</a></li>
			<li class="sep"><a href="approve_item.php">Approve event</a></li>
        </ul>
    </div>

    <div id="login">
		<form>
			<input type="button" class="css3button" value="Logout" id="Logout" />
		</form>

		<form >
			<input type="text" class="css3text" name="username" id="username" placeholder="username" />
			<input type="password" class="css3text" name="password" id="password" placeholder="password" />
			<input type="button" class="css3button" value="Login" />
			<p>
				<span class="register">You can register <a href="register.php">here</a>.</span>
			</p>
		</form>
    </div>

</div>