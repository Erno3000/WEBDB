<?php
$title = "My events";

const EMPLOYEES = 0x01;
const SHAREHOLDERS = 0x02;
const CUSTOMERS = 0x04;

include('header.php');

if(!isset($_SESSION['loggedin'])) {
    header('Location: agenda.php');
}
?>

<div id="content">
<h1>Showing all of your events.</h1><br/>

<?php
/* Set up a new connection to the database. */
include('dbconnect.php');

/* If the connection failed, bail out. */
if (!$db) {
    die('Could not connect: ' . mysql_error());
}

/* Prepare the query. */
$query = 'SELECT * FROM Following JOIN Events ON Following.event_id=Events.event_id WHERE Following.user_id=?';

/* First we prepare the query for execution. */
if ($stmt = $db->prepare($query)) {
    $stmt->bindValue(1, $_SESSION['id'], PDO::PARAM_INT);
    /* Then we execute the query. */
    if ($stmt->execute()) {
        /* And output it in an HTML table. */
        //$stmt->bind_result($a, $b, $b, $starter, $subject, $target_audience, $description, $start_date, $end_date,
        //    $start_time, $end_time, $place, $approved);
        $n = $stmt->rowCount();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo '<table border="1" id="myevents"> <tr>' .
            '<th class="normalth">Subject</th>' .
            '<th class="normalth">Target audience</th>' .
            '<th id="description">Description</th>' .
            '<th class="normalth">Place</th>' .
            '<th>Start date</th>' .
            '<th>Start time</th>' .
            '<th>End date</th>' .
            '<th>End time</th>' .
            '<th>Approved</th> </tr>';

        foreach($results as $row) {
            if ($row['approved'] || (intval($row['user_id']) == intval($_SESSION['id']))) {
                echo '<tr> <td>' . $row['subject'] . '</td>';

                $first = true;
                $audience = '';
                $target_audience = intval($row['target_audience']);

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
                '<td>' . $row['description'] . '</td>' .
                '<td>' . $row['place'] . '</td>' .
                '<td>' . $row['start_date'] . '</td>' .
                '<td>' . $row['start_time'] . '</td>' .
                '<td>' . $row['end_date'] . '</td>' .
                '<td>' . $row['end_time'] . '</td>';

                if($row['approved']) {
                    echo '<td>Yes</td>';
                } else {
                    if ($row['user_id'] == $_SESSION['id']) {
                        echo '<td>No</td>';
                    }
                }

            echo '</tr>';
            }
        }

        echo '</table>';
    }
}
?>
</div>

<?php include('footer.php'); ?>
