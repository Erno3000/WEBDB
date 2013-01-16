<?php
	//Uncomment when JS implementation works:
	//session_set_cookie_params(0);
	session_start();
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

<div class="header">
    <div class="headertopcontainer">
        <div class="headertop">
            
            <div id="logo">
                <a href="">
                    <img src="" />
                </a>
            </div>
                      
            <div class="login">
                <?php
                	if(!isset($_SESSION['loggedin'])) {
                    	echo '                
		                    <div class="loginpane">
		                    	<form action="login.php" method="post">
		                    		<input type="text" class="css3text" name="username"
				                    	id="username" placeholder="username" />
				                    <input type="password" class="css3text" name="pwd"
				                    	id="password" placeholder="password" />
				                    <input type="submit" class="css3button" id="submit"
				                    	value="Login" />
		                    	</form>
		                    </div>
		                    
		                    <div class="loginbar2">
		                        <ul>
		                            <li><a href="forgotpass.php">Forgot password</a></li>
		                            <li><a href="register.php">Register</a></li>
		                        </ul>
		                    </div> ';
       				} else {
       					echo '<div class="loginpane">';
       					if (isset($_SESSION['username'])) {
       						echo "Welcome, " . $_SESSION['username'] . "!</div>";
		   					echo '<div class="loginbar2">'. 
		   						'<ul>
		   							<li><a href="logout.php">Logout</a></li>
		   							<li><a href="accountsettings.html">
		   								Account settings</a></li>
		   						</ul>';
		   				}
       					echo '</div>';
       				}
       			?>
       		</div>
        
        </div>
    </div>
    <div class="headernavcontainer">
        <div class="navigatie">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li class="sep"><a href="agenda.php">Agenda</a></li>
                <?php
                    if (isset($_SESSION['loggedin'])) {
                    	if ($_SESSION['rank'] >= 1) {
		                    echo '<li class="sep"><a href="create_item.php">
		                    	Create event</a></li>';
		               	}
		               	
		               	if ($_SESSION['rank'] >= 2) {
		                    echo '<li class="sep"><a href="approve_item.php">
		                    	Approve event</a></li>';
		                }
		     		}
                ?>
            </ul>
        </div>
    </div>
</div>
