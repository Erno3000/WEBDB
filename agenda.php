<?php

    $title = "Calendar";
    include("header.php");
    include('config.php');

    const EMPLOYEES = 0x01;
    const SHAREHOLDERS = 0x02;
    const CUSTOMERS = 0x04;


    $querySearch = "SELECT subject, target_audience, description, start_date FROM Events WHERE approved=1
                    AND (start_date LIKE ? OR end_date LIKE ?) AND (subject LIKE ? OR description LIKE ?) LIMIT ?, ?";

    $defaultSubject = '%';
    $defaultTarget = EMPLOYEES | CUSTOMERS | SHAREHOLDERS;
    $defaultDescription = '%';
    $defaultDate = '____-__-__';
    $defaultLimit1 = 0;
    $defaultLimit2 = 5;

    $searchPage = 1;



    if(isset($_GET['txt_search'])) {
        $defaultSubject = $defaultDescription = '%' . $_GET['txt_search'] . '%';
    }

    if(isset($_GET['search_year'])) {
        $year = $_GET['search_year'];
        if(preg_match('/\d\d\d\d/', $year)) {
            $defaultDate = $year . '-__-__';
        }
    }

    if(isset($_GET['employees']) || isset($_GET['customers']) || isset($_GET['shareholders'])) {
        if(!isset($_GET['employees'])) {
            $defaultTarget ^= EMPLOYEES;
        }
        if(!isset($_GET['customers'])) {
            $defaultTarget ^= CUSTOMERS;
        }
        if(!isset($_GET['shareholders'])) {
            $defaultTarget ^= SHAREHOLDERS;
        }
    }


    $results = "";

    const WEEK = 0;
    const MONTH = 1;
    const YEAR = 2;

    const MAX_RESULTS = 5;

    $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if($stmt = $mysqli->prepare($querySearch)) {
        $stmt->bind_param('ssssii', $defaultDate, $defaultDate, $defaultSubject,
            $defaultDescription, $defaultLimit1, $defaultLimit2);

        if($stmt->execute()) {
            $stmt->bind_result($subj, $target, $desc, $date);
            while ($stmt->fetch()) {
                if(($target & $defaultTarget) > 0) {
                    $results .= '<div class="search_result"><p><h2>' . $subj . '</h2></p><p>' . $desc . '<p></div>';
                }
            }
        }

        $stmt->close();
    } else {
        echo 'Er zit een fout in de query: '.$mysqli->error;
    }


    $calview = MONTH;

    $currYear = intval(date("Y"));
    $currMonth = intval(date("n"));
    $currDay = intval(date("j"));

    $searchYears = "";
    for($i = $currYear; $i >= 2000; $i--) {
        if(isset($year) && $year == $i) {
            $searchYears .= '<option value="' . $i . '" selected>' . $i . '</option>';
        } else {
            $searchYears .= '<option value="' . $i . '">' . $i . '</option>';
        }
    }

    if(isset($_GET['calview'])) {
        $tmp = intval($_GET['calview']);
        if($tmp >= WEEK && $tmp <= YEAR) {
            $calview = $tmp;
        }
    }

    function createSearchField() {
        echo '<input type="text" id="txt_search" name="txt_search" autofocus="autofocus" value="';
        if(isset($_GET['txt_search'])) {
            echo strip_tags($_GET['txt_search']);
        }
        echo '" />';
    }

    function createTargetCheckbox($name, $bitmask) {
        echo '<input type="checkbox" id="' . $name . '" name="' . $name . '" class="css3check" value="' . $name . '"';
        global $defaultTarget;
        if($defaultTarget & $bitmask) {
            echo ' checked="checked"';
        }
        echo ' />';
    }

?>

    <div id="calsearch">
        <form id="search" method="get" action="agenda.php">
            <?php createSearchField(); ?><br />

                <?php createTargetCheckbox('employees', EMPLOYEES); ?>
                <label for="employees" class="css3label">Employees</label><br />
                <?php createTargetCheckbox('shareholders', SHAREHOLDERS); ?>
                <label for="shareholders" class="css3label">Shareholders</label><br />
                <?php createTargetCheckbox('customers', CUSTOMERS); ?>
                <label for="customers" class="css3label">Customers</label><br />

                <select class="css3text" id="search_year" name="search_year">
                    <?php echo $searchYears ?>
                </select>

                <input type="submit" class="css3button" id="search_button" value="Zoeken" />

        </form>

        <?php echo $results ?>

        <div id="calsearch_footer">
            <p>1 <span class="calsearch_footer_active">2</span> 3 4 5 6</p>
        </div>

    </div>

<div id="calviews">
    <div id="calview_tabs">
        <a href="agenda.php?calview=0" onclick="javascript:return false;">Week</a>
        <a href="agenda.php?calview=1" onclick="javascript:return false;">Month</a>
        <a href="agenda.php?calview=2" onclick="javascript:return false;">Year</a>
    </div>

    <div id="calview_opts">
        <div id="calview_opt0">
            <span id="calview_opt0_week">
                <a href="javascript:calendar.prevWeek()"><img src="img/previous_entry.png" alt="previous week" height="12px"></a>
                <span id="week">7 - 13. January 2013</span>
                <a href="javascript:calendar.nextWeek()"><img src="img/next_entry.png" alt="next month" height="12px"></a>
            </span>
        </div>
        <div id="calview_opt1">
            <span id="calview_opt1_month">
                <a href="javascript:calendar.prevMonth()"><img src="img/previous_entry.png" alt="previous month" height="12px"></a>
                <span id="month">April</span>
                <a href="javascript:calendar.nextMonth()"><img src="img/next_entry.png" alt="next month" height="12px"></a>
            </span>

            <span id="calview_opt1_year">
                <a href="javascript:calendar.prevYear()"><img src="img/previous_entry.png" alt="previous year" height="12px"></a>
                <span id="year">2011</span>
                <a href="javascript:calendar.nextYear()"><img src="img/next_entry.png" alt="next year" height="12px"></a>
            </span>
        </div>
        <div id="calview_opt2">
            <span id="calview_opt2_year">
                <a href="javascript:calendar.prevYear()"><img src="img/previous_entry.png" alt="previous year" height="12px"></a>
                <span id="year">2013</span>
                <a href="javascript:calendar.nexYear()"><img src="img/next_entry.png" alt="next year" height="12px"></a>
            </span>
        </div>

    </div>

    <div id="calview_0">

    </div>
    <div id="calview_1">

    </div>
    <div id="calview_2">

    </div>


    <table id="calendar">
        <caption>

        <span id="month_caption">
            <a href="javascript:calendar.prevMonth()"><img src="img/previous_entry.png" alt="previous month" height="12px"></a>
            <span id="month">April</span>
            <a href="javascript:calendar.nextMonth()"><img src="img/next_entry.png" alt="next month" height="12px"></a>
        </span>

        <span id="year_caption">
            <a href="javascript:calendar.prevYear()"><img src="img/previous_entry.png" alt="previous year" height="12px"></a>
            <span id="year">2011</span>
            <a href="javascript:calendar.nextYear()"><img src="img/next_entry.png" alt="next year" height="12px"></a>
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
            <td class="selected">
                <div class="event">Een borrel?</div>
                <div class="event even">Nog een borrel?</div>
                <div class="event">I guess</div>
                <div class="event even">Why not?</div>
                <div class="event">...</div>
            </td>
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





<script src="js/calendar.js"></script>
<script type="text/javascript">calendar.init()</script>


<?php include("footer.php") ?>


</body>

</html>