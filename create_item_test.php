<?php

/* TODO!
- check of checkbox minimaal 1 vinkje heeft
*/
	$title = "Create event";
	include("header.php");
	include('config.php');

    requireRank("AUTHOR");

    const EMPLOYEES = 0x01;
    const SHAREHOLDERS = 0x02;
    const CUSTOMERS = 0x04;

	$formError = false;
	$currentYear = intval(date("Y"));
	$currentMonth = date("F");
	$currentDay = intval(date("j"));
	$currentDate = date("m-d-Y");

	//Alleen uitvoeren wanneer het een post request is, dus alleen wanneer het formulier wordt gesubmit
	if ($_POST) {
		$post = true;
		//Maak een mysqli object aan met de gegevens uit config.php
		$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

		//Query om een user te registreren, gegevens worden ingevuld door variabelen te "binden", dwz invullen op de plaats waar een ? staat
		$queryRegister = "INSERT INTO Events (user_id, subject, target_audience, description, start_date, end_date, start_time, end_time, place, approved)
		      VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, 0)";

		//Alleen registreren wanneer alles is ingevoerd
		if(isset($_POST['subject']) && isset($_POST['target']) 
		&& isset($_POST['description']) && isset($_POST['start_month'])
		&& isset($_POST['start_day']) && isset($_POST['start_year'])
		&& isset($_POST['end_month']) && isset($_POST['end_day'])
		&& isset($_POST['end_year']) && isset($_POST['time1'])
		&& isset($_POST['time2']) && isset($_POST['time3']) 
		&& isset($_POST['time4']) && isset($_POST['place'])) {
	
			//Plaats de geposte data (gelinked aan namen uit het form) in variabelen
			//$user_id = $SESSION['user_id'];
			$subject = strip_tags($_POST['subject']);
            $targetArray = $_POST['target'];
			$description = strip_tags($_POST['description']);
			$start_month = strip_tags($_POST['start_month']);
			$start_day = strip_tags($_POST['start_day']);
			$start_year = strip_tags($_POST['start_year']);
			$end_month = strip_tags($_POST['end_month']);
			$end_day = strip_tags($_POST['end_day']);
			$end_year = strip_tags($_POST['end_year']);
			$time1 = strip_tags($_POST['time1']);
			$time2 = strip_tags($_POST['time2']);
			$time3 = strip_tags($_POST['time3']);
			$time4 = strip_tags($_POST['time4']);
			$place = strip_tags($_POST['place']);
			$seconds = 00;
			$start_date = $start_year . '-' . $start_month . '-' . $start_day;
			$end_date = $end_year . '-' . $end_month . '-' . $end_day;

            $target = 0;
            if(in_array('employees', $targetArray)) {
                $target |= EMPLOYEES;
            }
            if(in_array('shareholders', $targetArray)) {
                $target |= SHAREHOLDERS;
            }
            if(in_array('customers', $targetArray)) {
                $target |= CUSTOMERS;
            }

			//Check de lengte van de geposte data
			requireLength($subject, 1, 50, $subjectError);
			requireLength($description, 1, 500, $descriptionError);
			requireLength($place, 1, 100, $placeError);
			validStartDate($start_month, $start_day, $start_year, $start_date, $startDateError);
			validEndDate($end_month, $end_day, $end_year, $start_date, $end_date, $endDateError);
			
			if(!$formError) {
				if($stmt = $mysqli->prepare($queryRegister)) {
					//$start_date = $start_year . '-' . $start_month . '-' . $start_day;
					//$end_date = $end_year . '-' . $end_month . '-' . $end_day;
					$start_time = $time1 . ':' . $time2 . ':' . $seconds;
					$end_time = $time3 . ':' . $time4 . ':' . $seconds;
					$stmt->bind_param('sissssss', $subject, $target, $description, $start_date, $end_date, $start_time, $end_time, $place);
						
					if(!$stmt->execute()) {
	                    echo 'The form could not be submitted.'.$mysqli->error;
	                } else {
						echo 'Submitted.';
	                }

					$stmt->close();

				} else {
					echo 'Er zit een fout in de query: '.$mysqli->error;
				}
			}
		}
	}

	if(mysqli_connect_errno()) {
        trigger_error('Fout bij verbinding: '.$mysqli->error);
    }

	function error(&$errorVar, $msg) {
        global $formError;
        $formError = true;
        $errorVar = $msg;
    }

	function requireLength($field, $minLen, $maxLen, &$error) {
        if(strlen($field) < $minLen || strlen($field) > $maxLen) {
            error($error, "$minLen - $maxLen characters");
        }
    }

	function validStartDate($month, $day, $year, $start_date, &$error) {
		global $currentDate;
		$yearpattern = '[2-9][0-9][0-9][0-9]';
    	if(!preg_match($yearpattern, $year)) {
    		error($error, "Enter a valid year");
		} else if(!checkdate($month, $day, $year)) {
			error($error, "Enter a valid date");
		} else if(strtotime($start_date) < strtotime($currentDate)) {
			error($error, "Enter a Start Date in the future");
		}
	}

	function validEndDate($month, $day, $year, $start_date, $end_date, &$error) {
		global $currentDate;
		$yearpattern = '/^[2-9][0-9][0-9][0-9]$/';
    	if(!preg_match($yearpattern, $year)) {
    		error($error, "Enter a valid year");
		} else if(!checkdate($month, $day, $year)) {
			error($error, "Enter a valid date");
		} else if(strtotime($end_date) < strtotime($start_date)) {
			error($error, "Enter a valid End Date");
		}
	}
    
    function createField(&$prevVal, &$error, $id, $label, $mandatory, $class, $rule, $size, $type = 'text') {
        print '<li><label for="' . $id . '">' . $label . ':';
        if($mandatory) {
            print '*';
        }
        print '</label>';
        print '<input type="' . $type . '" id="' . $id . '" name="' . $id . '" size="' . $size . '"';
        if(isset($prevVal) && $type == 'text') {
            print ' value="' . strip_tags($prevVal) . '"';
        }
        if(isset($error)) {
            print ' class="errorinput"';
        }
        print ' />';
        if(isset($error)) {
            print '<span class="errormsg">' . $error . '</span>';
        }
        print '<br />';
        print '<p class="' . $class . '">' . $rule . '</p>';
        print '</li>';
    }
    
    function createTextarea(&$prevVal, &$error, $id, $label, $mandatory, $cols, $rows, $class, $rule) {
    print '<li><label for="' . $id . '">' . $label . ':';
        if($mandatory) {
            print '*';
        }
        print '</label>';
        print '<textarea cols="' . $cols . '" rows="' . $rows . '
        " id="' . $id . '" name="' . $id . '"';
        if(isset($error)) {
            print ' class="errorinput"';
        }
        print '></textarea>';
        if(isset($error)) {
            print '<span class="errormsg">' . $error . '</span>';
        }
        print '<br />';
        print '<p class="' . $class . '">' . $rule . '</p>';
        print '</li>';
    }
    
    function createCheckbox(&$prevVal, &$error, $id, $name, $label, $mandatory, $value1, $value2, $value3, $name1, $name2, $name3, $type = 'checkbox') {
    	print '<li><label for="' . $id . '">' . $label . ':';
        if($mandatory) {
            print '*';
        }
        print '</label>';
        print '<div id="' . $id . '">';
    	print '<input type="' . $type . '" name="' . $name . '" value="' . $value1 . '"';
        if(isset($error)) {
			print ' class="errorinput"';
        }
        print ' />';
		print $name1;
        print '<br />';
        print '<input type="' . $type . '" name="' . $name . '" value="' . $value2 . '"';
        if(isset($error)) {
			print ' class="errorinput"';
        }
        print ' />';
		print $name2;
        print '<br />';
        print '<input type="' . $type . '" name="' . $name . '" value="' . $value3 . '"';
        if(isset($error)) {
            print ' class="errorinput"';
        }
        print ' />';
		print $name3;
		if(isset($error)) {
            print '<span class="errormsg">' . $error . '</span>';
        }
        print '<br />';
        print '</div>';
        print '</li>';
	}

	function createDate(&$error, $id, $label, $mandatory, $month, $day, $year) {
		global $currentYear;
		global $currentMonth;
		global $currentDay;
		$dateformat1 = '<option value="%1$02d" selected>%1$02d</option>';
		$dateformat2 = '<option value="%1$02d">%1$02d</option>';
    	
		print '<li><label for="' . $id . '">' . $label . ':';
        if($mandatory) {
            print '*';
        }
        print '</label>';
        print '<div id="' . $id . '">';
        print 'Month:';
        print '<select name="' . $month . '">';
		for ($i = 1; $i <= 12; $i++) {
			$j = date("F", mktime(0, 0, 0, $i, 1, 2000));
			if($j == $currentMonth) {
				print '<option value="' . $i . '" selected>' . $j . '</option>';
			} else {
      			print '<option value="' . $i . '">' . $j . '</option>';
			}
   		}
		print '</select>';
		print ' Day:';
		print '<select name="' . $day . '">';
		for ($i = 1; $i <= 31; $i++) {
			if($i == $currentDay) {
				print sprintf($dateformat1,$i,$i);
			} else {
				print sprintf($dateformat2,$i,$i);
			}
   		}
    	print '</select>';
    	print ' Year:';
    	print '<input type="text" name="' . $year . '" value="' . $currentYear . '" size="4"';
        if(isset($error)) {
			print ' class="errorinput"';
        }
    	print '/>';
        if(isset($error)) {
            print '<span class="errormsg">' . $error . '</span>';
        }
		print '<br />';
		print '</div>';
		print '</li>';
    }

	function createTime($id, $label, $mandatory, $time1, $time2) {
		$timeformat = '<option value="%1$02d">%1$02d</option>';

		print '<li><label for="' . $id . '">' . $label . ':';
        if($mandatory) {
            print '*';
        }
        print '</label>';
		print '<select name="' . $time1 . '">';
        for ($i = 0; $i <= 23; $i++) {
        	print sprintf($timeformat,$i,$i);
    	}
    	print '</select>:';
		print '<select name="' . $time2 . '">';
        for ($i = 0; $i <= 59; $i++) {
        	print sprintf($timeformat,$i,$i);
    	}
    	print '</select>';
		print '<br />';
		print '</li>';
    }
?>

<?php
	if(!isset($post) || (isset($post) && $formError)) {
	  echo '<div id="content">
                <h1>Create event</h1>
				<div id="ccform">
	        			<form method="post" action="create_item_test.php">
						<fieldset>
            			<legend>Fill in this form to create a new event</legend>
							<ul>';

		createField($subject, $subjectError, 'subject', 'Subject', true, 'maximum', 'Maximum of 50 characters.', '30');
		createCheckbox($target, $targetError, 'target', 'target[]', 'Target audience', true, 'employees', 'shareholders', 'customers', 'Employees', 'Shareholders', 'Customers');
		createTextarea($description, $descriptionError, 'description', 'Description', true, '35', '5', 'maximum', 'Maximum of 500 characters.');
		createDate($startDateError, 'start_date', 'Start Date', true, 'start_month', 'start_day', 'start_year');
		createTime('start_time', 'Start Time', true, 'time1', 'time2');
		createDate($endDateError, 'end_date', 'End Date', true, 'end_month', 'end_day', 'end_year');
		createTime('end_time', 'End Time', true, 'time3', 'time4');
		createField($place, $placeError, 'place', 'Place', true, 'maximum', 'Maximum of 100 characters.', '30');

				          echo '<li>
				                    <br />
					                <label for="submit">&nbsp;</label>
						            <input type="submit" id="submit" name="submit" value="Submit" />
					            </li>
                            </ul>
				            <p class="mandatory"><br />Fields marked with an asterisk (*) are mandatory.</p>
                        </fieldset>
                    </form>
		        </div>
	        </div>';
	}

	include('footer.php');
?>