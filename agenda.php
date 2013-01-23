<?php

$title = "Calendar";
include("header.php");
include('config.php');
include('calsearch.php');
include('date.php');

$queryCal = "SELECT subject, start_date, end_date, start_time, end_time FROM Events WHERE approved=1
                    AND ((start_date LIKE ? OR end_date LIKE ?) OR (start_date LIKE ? OR end_date LIKE ?)
                    OR (start_date LIKE ? OR end_date LIKE ?)) ORDER BY start_date, start_time";

const WEEK = 0;
const MONTH = 1;
const YEAR = 2;

const MONTHVIEW_ROWS = 6;
const MONTHVIEW_COLS = 7;

$calview = MONTH;

$today = new Date(intval(date("Y")), intval(date("n")), intval(date("j")));
$caldate = $today->copy();

$request = $_GET;

if (isset($_GET['calview'])) {
    $tmp = intval($_GET['calview']);
    if ($tmp >= WEEK && $tmp <= YEAR) {
        $calview = $tmp;
    }
}

if(isset($_GET['y']) && isset($_GET['m']) && isset($_GET['d'])) {
    $caldate = new Date($_GET['y'], $_GET['m'], $_GET['d']);
}

if($calview == YEAR) {
    createYearCalview($caldate);
} else if($calview == MONTH) {
    createMonthCalview($caldate);
} else {
    createWeekCalview($caldate);
}

function createYearCalview(Date $date) {
    //todo
}

function createMonthCalview(Date $date) {
    global $dbHost, $dbUser, $dbPass, $dbName, $queryCal, $request;

    $startDate1 = $endDate1 = substr($date->copy()->previousMonth()->toMYSQLString(), 0, 7) . '-__';
    $startDate2 = $endDate2 = substr($date->toMYSQLString(), 0, 7) . '-__';
    $startDate3 = $endDate3 = substr($date->copy()->nextMonth()->toMYSQLString(), 0, 7) . '-__';

    $events = array();

    $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($stmt = $mysqli->prepare($queryCal)) {
        $stmt->bind_param('ssssss', $startDate1, $endDate1, $startDate2, $endDate2, $startDate3, $endDate3);

        if ($stmt->execute() && $stmt->store_result()) {
            $stmt->bind_result($rSubj, $rStartDate, $rEndDate, $rStartTime, $rEndTime);

            //todo remove
            $numResults = $stmt->num_rows;

            while ($stmt->fetch()) {
                if(!isset($events[$rStartDate])) {
                    $events[$rStartDate] = '';
                }

                $events[$rStartDate] .= '<div class="event">' . $rSubj . '</div>';
            }
        }

        $stmt->close();
    } else {
        echo 'Er zit een fout in de query: ' . $mysqli->error;
    }

    $prevMonthUrl = createUrlFromArray($date->copy()->previousMonth(), $request);
    $nextMonthUrl = createUrlFromArray($date->copy()->nextMonth(), $request);
    $prevYearUrl = createUrlFromArray($date->copy()->previousYear(), $request);
    $nextYearUrl = createUrlFromArray($date->copy()->nextYear(), $request);
    $monthName = $date->getMonthName();
    $year = $date->getYear();

    echo '<table id="calendar">
    <caption>
        <span id="month_caption">
            <a href="' . $prevMonthUrl . '"><img src="img/previous_entry.png" alt="previous month" height="12px" /></a>
            <span id="month">' . $monthName . '</span>
            <a href="' . $nextMonthUrl . '"><img src="img/next_entry.png" alt="next month" height="12px" /></a>
        </span>

        <span id="year_caption">
            <a href="' . $prevYearUrl . '"><img src="img/previous_entry.png" alt="previous year"
                                                          height="12px" /></a>
            <span id="year">' . $year . '</span>
            <a href="' . $nextYearUrl . '"><img src="img/next_entry.png" alt="next year" height="12px" /></a>
        </span>

    </caption>

    <tr id="weekdays">
        <td>Monday</td>
        <td>Tuesday</td>
        <td>Wednesday</td>
        <td>Thursday</td>
        <td>Friday</td>
        <td>Saturday</td>
        <td>Sunday</td>
    </tr>';

    $dateCopy = $date->copy();
    $today = new Date(intval(date("Y")), intval(date("n")), intval(date("j")));
    $dateCopy->setDay(1);
    $weekday = $dateCopy->getWeekDay();

    /* Current weekday numbers are from Sunday (day 0) to Saturday (day 6)
       Desired weekday numbers are from Tuesday (day 1) to Monday (day 7) to display the last $number
        amount of days from the previous month
    */
    if($weekday >= Date::TUESDAY && $weekday <= Date::SATURDAY) {
        $weekday = $weekday - 2;
    } else if($weekday == Date::MONDAY) {
        $weekday = 6;
    } else {
        $weekday = 5;
    }

    $date->previousMonth();
    $date->setDay($date->getDaysInMonth($date->getYear(), $date->getMonth()) - $weekday);

    for($i = 0; $i < MONTHVIEW_ROWS; $i++) {
        $eventsById = array();

        echo '<tr class="days">';
        for($j = 0; $j < MONTHVIEW_COLS; $j++) {
            if($date->equals($today)) {
                echo '<td class="selected">' . $date->format('F j') . '</td>';
            } else {
                echo '<td>' . $date->format('F j') . '</td>';
            }
            if(isset($events[$date->toMYSQLString()])) {

                $eventsById[$j] = $events[$date->toMYSQLString()];
            }
            $date->nextDay();
        }
        echo '</tr>';

        echo '<tr class="events">';
        for($j = 0; $j < MONTHVIEW_COLS; $j++) {
            if(isset($eventsById[$j])) {
                echo '<td>' . $eventsById[$j] . '</td>';
            } else {
                echo '<td>' . '</td>';
            }
        }
        echo '</tr>';
    }


}

function createUrlFromArray(Date $date, &$request) {
    $request['y'] = $date->getYear();
    $request['m'] = $date->fixNumber($date->getMonth());
    $request['d'] = $date->fixNumber($date->getDay());

    return urlFromArray('agenda.php', $request);
}

function createWeekCalview(Date $date) {
    //todo
}

?>

</table>

</div>

<?php include("footer.php") ?>