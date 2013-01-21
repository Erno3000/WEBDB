<?php

/* TODO!
- check of checkbox minimaal 1 vinkje heeft (werkt nog niet!)
*/

	$title = "Create event";
	include("header.php");
    try {
        include('dbconnect.php');
    }
    catch(PDOException $ex) {
        echo "Error while connecting to dB: ", $ex->getMessage();
    }

    requireRank("AUTHOR");

    const EMPLOYEES = 0x01;
    const SHAREHOLDERS = 0x02;
    const CUSTOMERS = 0x04;

	$formError = false;
	$currentYear = intval(date("Y"));
	$currentMonth = date("F");
	$currentDay = intval(date("j"));
	$currentDate = date("m-d-Y");

    //Execute only if there is a post request, so just when the form is being submitted.
	if ($_POST) {
		$post = true;

        //Query to create an event, data is filled by binding variables, i.e. filling in in the places where a ? states
		$queryRegister = "INSERT INTO Events (user_id, subject, target_audience, description, start_date, end_date, start_time, end_time, place, approved)
		      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
        echo '0';
        //Only send the form when every field is entered.
		if(isset($_POST['subject'])	&& isset($_POST['target'])
            && isset($_POST['description']) && isset($_POST['start_month'])
            && isset($_POST['start_day']) && isset($_POST['start_year'])
            && isset($_POST['end_month']) && isset($_POST['end_day'])
            && isset($_POST['end_year']) && isset($_POST['time1'])
            && isset($_POST['time2']) && isset($_POST['time3'])
            && isset($_POST['time4']) && isset($_POST['place'])) {

            echo '1';

            //Set the posted data (linked to names from the form) in variables.
            $user_id = $_SESSION['id'];
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
            $start_time = $time1 . ':' . $time2 . ':' . $seconds;
            $end_time = $time3 . ':' . $time4 . ':' . $seconds;

            echo '2';

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
            if(!isset($_POST['target'])) {
                error($targetError, "Check at least one box");
            }

            echo '3';

            //Check the length and validity of the posted data
			requireLength($subject, 1, 50, $subjectError);
			requireLength($description, 1, 500, $descriptionError);
			requireLength($place, 1, 100, $placeError);
			validStartDate($start_month, $start_day, $start_year, $start_date, $startDateError);
			validEndDate($end_month, $end_day, $end_year, $start_date, $end_date, $endDateError);

            if(strtotime($end_date) == strtotime($start_date) && strtotime($end_time) <= strtotime($start_time)) {
                error($end_timeError, "Enter a valid End Time");
            }
			
			if(!$formError) {
				if($stmt = $db->prepare($queryRegister)) {
					$stmt->bindValue(1, $user_id, PDO::PARAM_INT);
                    $stmt->bindValue(2, $subject, PDO::PARAM_STR);
                    $stmt->bindValue(3, $target, PDO::PARAM_INT);
                    $stmt->bindValue(4, $description, PDO::PARAM_STR);
                    $stmt->bindValue(5, $start_date, PDO::PARAM_STR);
                    $stmt->bindValue(6, $end_date, PDO::PARAM_STR);
                    $stmt->bindValue(7, $start_time, PDO::PARAM_STR);
                    $stmt->bindValue(8, $end_time, PDO::PARAM_STR);
                    $stmt->bindValue(9, $place, PDO::PARAM_STR);
					echo '4';
					if(!$stmt->execute()) {
	                    echo 'The form could not be submitted.'.$db->error;
	                } else {
						echo 'Submitted.';
	                }

					$stmt->close();

				} else {
					echo 'There is an error in the query: '.$db->error;
				}
			}
		} else {
            echo '5';
        }
	}

	if(!$db) {
        trigger_error('Error when connecting: '.$db->error);
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
		$yearpattern = '/^[2-9][0-9][0-9][0-9]$/';
    	if(!preg_match($yearpattern, $year)) {
    		error($error, "Enter a valid year");
		} else if(!checkdate($month, $day, $year)) {
			error($error, "Enter a valid date");
		} else if(strtotime($start_date) <= strtotime($currentDate)) {
			error($error, "Enter a Start Date in the future");
		}
	}

	function validEndDate($month, $day, $year, $start_date, $end_date, &$error) {
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
        if(isset($prevVal)) {
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
        if(isset($prevVal)) {
            print ' value="' . strip_tags($prevVal) . '"';
        }
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
    
    function createCheckbox(&$error, $id, $name, $label, $mandatory, $value1, $value2, $value3, $name1, $name2, $name3, $type = 'checkbox') {
    	print '<li><label for="' . $id . '">' . $label . ':';
        if($mandatory) {
            print '*';
        }
        print '</label>';
        print '<div id="' . $id . '">';
    	print '<input type="' . $type . '" name="' . $name . '" value="' . $value1 . '"';
        print ' />';
		print $name1;
        print '<br />';
        print '<input type="' . $type . '" name="' . $name . '" value="' . $value2 . '"';
        if(isset($error)) {
            print ' class="errorinput"';
        }
        print ' />';
		print $name2;
        if(isset($error)) {
            print '<span class="errormsg">' . $error . '</span>';
        }
        print '<br />';
        print '<input type="' . $type . '" name="' . $name . '" value="' . $value3 . '"';
        print ' />';
		print $name3;
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

	function createTime(&$error, $id, $label, $mandatory, $time1, $time2) {
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
        if(isset($error)) {
            print ' class="errorinput"';
        }
    	print '</select>';
        if(isset($error)) {
            print '<span class="errormsg">' . $error . '</span>';
        }
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
		createCheckbox($targetError, 'target', 'target[]', 'Target audience', true, 'employees', 'shareholders', 'customers', 'Employees', 'Shareholders', 'Customers');
		createTextarea($description, $descriptionError, 'description', 'Description', true, '35', '5', 'maximum', 'Maximum of 500 characters.');
		createDate($startDateError, 'start_date', 'Start Date', true, 'start_month', 'start_day', 'start_year');
		createTime($timeError, 'start_time', 'Start Time', true, 'time1', 'time2');
		createDate($endDateError, 'end_date', 'End Date', true, 'end_month', 'end_day', 'end_year');
		createTime($end_timeError, 'end_time', 'End Time', true, 'time3', 'time4');
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