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
$caldate = $today;

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

    $startDate1 = $endDate1 = substr($date->copy()->previousMonth()->toString(), 0, 7) . '-__';
    $startDate2 = $endDate2 = substr($date->toString(), 0, 7) . '-__';
    $startDate3 = $endDate3 = substr($date->copy()->nextMonth()->toString(), 0, 7) . '-__';

    $events = array();

    $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($stmt = $mysqli->prepare($queryCal)) {
        $stmt->bind_param('ssssss', $startDate1, $endDate1, $startDate2, $endDate2, $startDate3, $endDate3);

        if ($stmt->execute() && $stmt->store_result()) {
            $stmt->bind_result($rSubj, $rStartDate, $rEndDate, $rStartTime, $rEndTime);

            //todo remove
            $numResults = $stmt->num_rows;
            echo 'Found ' . $numResults . 'events';

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

    $request["m"] = $prevMonth;
    $prevMonthUrl = urlFromArray('agenda.php', $request);
    $request["m"] = $nextMonth;
    $nextMonthUrl = urlFromArray('agenda.php', $request);
    $monthName = date("F", mktime(0, 0, 0, intval($request['m']), 1, 2000));
    echo 'intval request m = ' . intval($request['m']);
    $request["m"] = $month;
    $request["y"] = $prevYear;
    $prevYearUrl = urlFromArray('agenda.php', $request);
    $request["y"] = $nextYear;
    $nextYearUrl = urlFromArray('agenda.php', $request);


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

    $start = new DateTime();
    $start->setDate($year, $request['m'], $request['d']);
    $weekday = intval($start->format('w'));

    if($weekday != 1) {
        $start->setDate($year, $prevMonth, $request['d']);
        $daysInMonth = intval($start->format('t'));
        $diff = $weekday == 0 ? 0 : $weekday - 2;
        $start->setDate($year, $prevMonth, $daysInMonth - $diff);
    }

    for($i = 0; $i < MONTHVIEW_COLS*MONTHVIEW_ROWS; $i++) {
        if(intval($start->format('j')) > intval($start->format('w'))) {
            $start->setDate($year, intval($month+1), 1);
        }
        $start->add(new DateInterval());
        echo $start->format('F j');
    }


    


}

function createWeekCalview(Date $date) {
    //todo
}

?>


<div id="calviews">
<div id="calview_tabs">
    <a href="agenda.php?calview=0" onclick="javascript:return false;">Week</a>
    <a href="agenda.php?calview=1" onclick="javascript:return false;">Month</a>
    <a href="agenda.php?calview=2" onclick="javascript:return false;">Year</a>
</div>

<div id="calview_opts">
    <div id="calview_opt0">
            <span id="calview_opt0_week">
                <a href="javascript:calendar.prevWeek()"><img src="img/previous_entry.png" alt="previous week"
                                                              height="12px" /></a>
                <span id="week">7 - 13. January 2013</span>
                <a href="javascript:calendar.nextWeek()"><img src="img/next_entry.png" alt="next month"
                                                              height="12px" /></a>
            </span>
    </div>
    <div id="calview_opt1">
            <span id="calview_opt1_month">
                <a href="javascript:calendar.prevMonth()"><img src="img/previous_entry.png" alt="previous month"
                                                               height="12px" /></a>
                <span id="month">April</span>
                <a href="javascript:calendar.nextMonth()"><img src="img/next_entry.png" alt="next month" height="12px" /></a>
            </span>

            <span id="calview_opt1_year">
                <a href="javascript:calendar.prevYear()"><img src="img/previous_entry.png" alt="previous year"
                                                              height="12px" /></a>
                <span id="year">2011</span>
                <a href="javascript:calendar.nextYear()"><img src="img/next_entry.png" alt="next year"
                                                              height="12px" /></a>
            </span>
    </div>
    <div id="calview_opt2">
            <span id="calview_opt2_year">
                <a href="javascript:calendar.prevYear()"><img src="img/previous_entry.png" alt="previous year"
                                                              height="12px" /></a>
                <span id="year">2013</span>
                <a href="javascript:calendar.nexYear()"><img src="img/next_entry.png" alt="next year" height="12px" /></a>
            </span>
    </div>

</div>

<div id="calview_0">

</div>
<div id="calview_1">

</div>
<div id="calview_2">

</div>



    <tr class="days">
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
    </tr>

    <tr class="events">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    <tr class="days">
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
    </tr>

    <tr class="events">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    <tr class="days">
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
    </tr>

    <tr class="events">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    <tr class="days">
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
    </tr>

    <tr class="events">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    <tr class="days">
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
    </tr>

    <tr class="events">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    <tr class="days">
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
        <td>28 March</td>
    </tr>

    <tr class="events">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

</table>

</div>

<?php include("footer.php") ?>