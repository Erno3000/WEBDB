<?php 
	session_start();
	$_SESSION['loggedin']=false;
	$_SESSION['rank']=0;
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
    <link rel="stylesheet" type="text/css" href="css/register.css" />
    <link rel="stylesheet" type="text/css" href="css/create_item.css" />
</head>

<body>

<div id="header">

    <div id="navigatie">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li class="sep"><a href="agenda.php">Agenda</a></li>
			<?php
				if($_SESSION['loggedin']==true)
					{
						echo "<li class='sep'><a href='create_item.php'>Create event</a></li>";
						echo "<li class='sep'><a href='approve_item.php'>Approve event</a></li>";
					}
			?>
        </ul>
    </div>

    <div id="login">
		<?php
		if($_SESSION['loggedin']==false)
			{
			echo "<form>";
				echo "<input type='text' class='css3text' name='username' id='username' placeholder='username' />";
				echo "<input type='password' class='css3text' name='password' id='password' placeholder='password' />";
				echo "<input type='button' class='css3button' value='Login' />";
				echo "<p><span class='register'>You can register <a href='register.php'>here</a>.</span></p>";
			echo "</form>";
			}
		else
			{
			echo "<form>";
				echo "<input type='submit' class='css3button' value='Logout' />";
			echo "</form>";
			}
		?>
    </div>

</div>