<?php
$title = "My events";

const EMPLOYEES = 0x01;
const SHAREHOLDERS = 0x02;
const CUSTOMERS = 0x04;

include('header.php');
?>

<div id="content">
<h1>Showing all of your events.</h1><br/>

<?php
/* Set up a new connection to the database. */
include('dbconnect.php');

/* If the connection failed, bail out. */
if (!$mysqli) {
    die('Could not connect: ' . mysql_error());
}

/* Prepare the query. */
$query = 'SELECT * FROM Following JOIN Events ON Following.event_id=Events.event_id WHERE Following.user_id=?';

/* First we prepare the query for execution. */
if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param('i', $_SESSION['id']);
    /* Then we execute the query. */
    if ($stmt->execute()) {
        /* And output it in an HTML table. */
        if ($stmt->store_result()) {
            $stmt->bind_result($a, $b, $b, $starter, $subject, $target_audience, $description, $start_date, $end_date,
                $start_time, $end_time, $place, $approved);
            echo '<table border="1" id="myevents"> <tr>' .
                '<th>Subject</th>' .
                '<th>Target audience</th>' .
                '<th id="description">Description</th>' .
                '<th>Place</th>' .
                '<th class="smallth">Start date</th>' .
                '<th class="smallth">Start time</th>' .
                '<th class="smallth">End date</th>' .
                '<th class="smallth">End time</th>' .
                '<th class="smallth">Approved</th> </tr>';

            while ($stmt->fetch()) {
                if ($approved || (intval($starter) == intval($_SESSION['id']))) {
                    echo '<tr> <td>' . $subject . '</td>';

                    $first = true;
                    $audience = '';
                    $target_audience = intval($target_audience);

                    if($target_audience & EMPLOYEES) {
                        /* Employees is onderdeel van de target audience */
                        $first = false;
                        $audience = 'Employees';
                    }

                    if($target_audience & SHAREHOLDERS) {
                        if ($first) {
                            $audience = 'Shareholders';
                            $first = false;
                        } else {
                            $audience .= ', Shareholders';
                        }
                    }

                    if($target_audience & CUSTOMERS) {
                        if ($first) {
                            $audience = 'Customers';
                        } else {
                            $audience .= ', Customers';
                        }
                    }

                    echo '<td>' . $audience . '</td>' .
                    '<td>' . $description . '</td>' .
                    '<td>' . $place . '</td>' .
                    '<td>' . $start_date . '</td>' .
                    '<td>' . $start_time . '</td>' .
                    '<td>' . $end_date . '</td>' .
                    '<td>' . $end_time . '</td>';

                    if($approved) {
                        echo '<td>Yes</td>';
                    } else {
                        if ($starter == $_SESSION['id']) {
                            echo '<td>No</td>';
                        }
                    }

                echo '</tr>';
                }
            }

            echo '</table>';
        }
    }
}
?>
</div>

<?php include('footer.php'); ?>
