<?php
    session_start();

    const GUEST = -1;
    const USER = 0;
    const AUTHOR = 1;
    const MODERATOR = 2;
    const ADMIN = 3;

    /* This function takes a string holding a rank as an argument (e.g. GUEST, USER, AUTHOR, etc...).
     * If the user is not logged in, he will be redirected to the index.php page.
     * If he is logged in, but doesn't have the given, required rank ($rank parameter), he is redirected to the
     * index.php page.
     * If the user is logged in and has the required rank, no action is taken.
     */
    function requireRank($rank) {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            if($rank == 'GUEST') {
                $rank = GUEST;
            } elseif ($rank == 'USER') {
                $rank = USER;
            } elseif ($rank == 'AUTHOR') {
                $rank = AUTHOR;
            } elseif ($rank == 'MODERATOR') {
                $rank = MODERATOR;
            } elseif ($rank == 'ADMIN') {
                $rank = ADMIN;
            } else {
                $rank = -1;
            }

            if ($_SESSION['rank'] < intval($rank)) {
                header('Location: agenda.php');
            }
        } else {
            header('Location: agenda.php');
        }
    }
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
    <link rel="stylesheet" type="text/css" href="css/create_event.css" />
    <link rel="stylesheet" type="text/css" href="css/table_form.css" />
    <link rel="icon" type="image/x-icon" href="smile.ico" />
    <link rel="stylesheet" type="text/css" href="css/account_settings.css" />
</head>

<body>
<div id="container">
<div class="header">
    <div class="headertopcontainer">
        <div class="headertop">
            
            <div id="logo">
                <a href="">
                    <img src="" alt="" />
                </a>
            </div>
                      
            <div class="login">
                <?php
                	/* If the user is not logged in, show the login pane, etc. */
                	if(!isset($_SESSION['loggedin'])) {
                    	echo '                
		                    <div class="loginpane">
		                    	<form action="login.php" method="post">
		                    		<fieldset class="normal">
		                    			<input type="text" class="css3text" name="username"
				                    		id="username" placeholder="username" />
				                    	<input type="password" class="css3text" name="pwd"
				                    		id="password" placeholder="password" />
				                    	<input type="submit" class="css3button" id="submit"
				                    		value="Login" />
				                    </fieldset>
		                    	</form>
		                    </div>
		                    
		                    <div class="loginbar2">
		                        <ul>
		                            <li><a href="forgotpass.php">Forgot password</a></li>
		                            <li><a href="register.php">Register</a></li>
		                        </ul>
		                    </div> ';
       				} else {
       					/* Else show the other variant of the login pane. */
       					echo '<div class="loginpane">';
       					if (isset($_SESSION['username'])) {
       						echo "Welcome, " . $_SESSION['username'] . "!</div>";
		   					echo '<div class="loginbar2">'. 
		   						'<ul>
		   							<li><a href="myevents.php">My events</a></li>
		   							<li><a href="account_settings.php">
		   								Account settings</a></li>
		   							<li><a href="logout.php">Logout</a></li>
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
		                    echo '<li class="sep"><a href="create_event.php">
		                    	Create event</a></li>';
		               	}
		               	
		               	if ($_SESSION['rank'] >= 2) {
		                    echo '<li class="sep"><a href="approve_event.php">
		                    	Approve event</a></li>';
		                }
		                
		                if ($_SESSION['rank'] >= 3) {
		                    echo '<li class="sep"><a href="approve_user.php">
		                    	Approve users</a></li>';
		                }
		     	}
                ?>
            </ul>
        </div>
    </div>
</div>
