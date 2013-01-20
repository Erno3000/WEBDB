<?php

$title = "Calendar";
include("header.php");
include('config.php');
include('calsearch.php');

$queryCal = "SELECT subject, start_date, end_date, start_time, end_time FROM Events WHERE approved=1
                    AND ((start_date LIKE ? OR end_date LIKE ?) OR (start_date LIKE ? OR end_date LIKE ?)
                    OR (start_date LIKE ? OR end_date LIKE ?)) ORDER BY start_date, start_time";

const WEEK = 0;
const MONTH = 1;
const YEAR = 2;

const MIN_YEAR = 0;
const MAX_YEAR = 9999;
const MIN_MONTH = 1;
const MAX_MONTH = 12;
const MIN_DAY = 1;
const MAX_DAY = 31;

const MONTHVIEW_ROWS = 6;
const MONTHVIEW_COLS = 7;

$calview = MONTH;

$currYear = intval(date("Y"));
$currMonth = intval(date("n"));
$currDay = intval(date("j"));

$request = $_GET;

if (isset($_GET['calview'])) {
    $tmp = intval($_GET['calview']);
    if ($tmp >= WEEK && $tmp <= YEAR) {
        $calview = $tmp;
    }
}

if(isset($_GET['y'])) {
    $year = intval($_GET['y']);
}
if(isset($_GET['m'])) {
    $month = intval($_GET['m']);
}
if(isset($_GET['d'])) {
    $day = intval($_GET['d']);
}

if(isset($year) && isset($month) && isset($day) && validDate($year, $month, $day)) {
    $request['y'] = intval($year);
    $request['m'] = intval($month);
    $request['d'] = intval($day);
} else {
    $request['y'] = $currYear;
    $request['m'] = $currMonth;
    $request['d'] = $currDay;
}

if($calview == YEAR) {
    createYearCalview($currYear, $currMonth, $currDay);
} else if($calview == MONTH) {
    createMonthCalview($currYear, $currMonth, $currDay);
} else {
    createWeekCalview($currYear, $currMonth, $currDay);
}

function validYear($year) {
    return $year >= MIN_YEAR && $year <= MAX_YEAR;
}

function validMonth($month) {
    return $month >= 1 && $month <= MAX_MONTH;
}

function validDay($day) {
    return $day >= MIN_DAY && $day <= MAX_DAY;
}

function validDate($year, $month, $day) {
    if(validYear($year) && validMonth($month) && validDay($day)) {
        $daysOfMonth = date("t", mktime(0, 0, 0, $month, 1, $year));
        return $day <= $daysOfMonth;
    }
}

function prevMonth($month) {
    if($month == 1) {
        return 12;
    }

    return fixMonth($month - 1);
}

function nextMonth($month) {
    if($month == 12) {
        return fixMonth(1);
    }

    return fixMonth($month + 1);
}

function fixMonth($month) {
    if($month <= 9) {
        return '0' . $month;
    }

    return $month;
}

function createYearCalview($year, $month, $day) {
    //todo
}

function createMonthCalview($year, $month, $day) {
    global $dbHost, $dbUser, $dbPass, $dbName, $queryCal, $request;

    $nextMonth = nextMonth($month);
    $prevMonth = prevMonth($month);
    $year = intval($request['y']);
    $nextYear = $year + 1;
    $prevYear = $year - 1;

    $month = fixMonth($month);

    $startDate1 = $endDate1 = $year . '-' . $prevMonth . '-__';
    $startDate2 = $endDate2 = $year . '-' . $month . '-__';
    $startDate3 = $endDate3 = $year . '-' . $nextMonth . '-__';

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


}

function createWeekCalview($year, $month, $day) {
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