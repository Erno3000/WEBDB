<?php

include('config.php');
include('util.php');

$currYear = intval(date("Y"));
$currMonth = intval(date("n"));
$currDay = intval(date("j"));

$querySearch = "SELECT event_id, subject, target_audience, description, start_date, end_date, start_time, end_time
                    FROM Events WHERE approved=1
                    AND (start_date LIKE ? OR end_date LIKE ?) AND (subject LIKE ? OR description LIKE ?)
                    ORDER BY start_date LIMIT ?, ?";

$defaultSubject = '%';
$defaultTarget = EMPLOYEES | CUSTOMERS | SHAREHOLDERS;
$defaultDescription = '%';
$defaultDate = '____-__-__';
$defaultLimit1 = 0;
$defaultLimit2 = 184467440737;

$resultsPerPage = 5;
$searchPage = 1;
$totalSearchPages = 1;

if(isset($_GET['p'])) {
    $searchPage = intval($_GET['p']);
    if($searchPage <= 1) {
        $searchPage = 1;
    }
}

if (isset($_GET['txt_search'])) {
    $defaultSubject = $defaultDescription = '%' . $_GET['txt_search'] . '%';
}

if(isset($_GET['follow']) && isset($_SESSION['loggedin'])) {
    followEvent($_SESSION['id'], intval($_GET['follow']));
    unset($_GET['follow']);
}

if(isset($_GET['unfollow']) && isset($_SESSION['loggedin'])) {
    unfollowEvent($_SESSION['id'], intval($_GET['unfollow']));
    unset($_GET['unfollow']);
}

if (isset($_GET['search_year'])) {
    $year = $_GET['search_year'];
    if (preg_match('/\d\d\d\d/', $year)) {
        $defaultDate = $year . '-__-__';
    }
}

if (isset($_GET['employees']) || isset($_GET['customers']) || isset($_GET['shareholders'])) {
    if (!isset($_GET['employees'])) {
        $defaultTarget ^= EMPLOYEES;
    }
    if (!isset($_GET['customers'])) {
        $defaultTarget ^= CUSTOMERS;
    }
    if (!isset($_GET['shareholders'])) {
        $defaultTarget ^= SHAREHOLDERS;
    }
}

$results = "";

const MAX_RESULTS = 5;

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($stmt = $mysqli->prepare($querySearch)) {
    $stmt->bind_param('ssssii', $defaultDate, $defaultDate, $defaultSubject,
        $defaultDescription, $defaultLimit1, $defaultLimit2);

    if ($stmt->execute() && $stmt->store_result()) {
        $stmt->bind_result($id, $subj, $target, $desc, $startDate, $endDate, $startTime, $endTime);
        $numResults = 0;
        $following = getFollowingEvents();

        $i = 1;
        while ($stmt->fetch()) {
            if($target & $defaultTarget) {
                $numResults++;
                if ($i <= $resultsPerPage*$searchPage && $i > $resultsPerPage * ($searchPage-1)) {
                    $monthName = date("F", mktime(0, 0, 0, intval(substr($startDate, 5, 7)), 1, 2000));
                    $day = intval(substr($startDate, 8, 9));
                    if(in_array($id, $following)) {
                        $follow = '<div class="result_buttons"><a href="' . createUnfollowUrl($id) . '" class="unfollow"><span>Unfollow event</span></a></div>';
                    } else if(isset($_SESSION['loggedin'])) {
                        $follow = '<div class="result_buttons"><a href="' . createFollowUrl($id) .'" class="follow"><span>Follow event</span></a></div>';
                    } else {
                        $follow = '';
                    }

                    $results .= '<div class="search_result" onclick="javascript:slide.toggleDisplay(' . "'slide" . ($i-1) . "'" .
                        ')"><h2>' . $subj . '</h2>' .
                        $follow . '<h3>' . $monthName . ' ' . $day . ' at ' . substr($startTime, 0, 5);


                    $results .= '</h3><div id="slide' . ($i-1) . '">' . $desc . '</div></div>';
                }
                $i++;
            }
        }
        $totalSearchPages = ceil($numResults / $resultsPerPage);
    }

    $stmt->close();
} else {
    echo 'Er zit een fout in de query: ' . $mysqli->error;
}

$searchYears = "";
for ($i = $currYear; $i >= 2000; $i--) {
    if (isset($year) && $year == $i) {
        $searchYears .= '<option value="' . $i . '" selected>' . $i . '</option>';
    } else {
        $searchYears .= '<option value="' . $i . '">' . $i . '</option>';
    }
}

function createSearchField()
{
    echo '<input type="text" id="txt_search" name="txt_search" autofocus="autofocus" value="';
    if (isset($_GET['txt_search'])) {
        echo strip_tags($_GET['txt_search']);
    }
    echo '" />';
}

function createTargetCheckbox($name, $bitmask) {
    echo '<input type="checkbox" id="' . $name . '" name="' . $name . '" class="css3check" value="' . $name . '"';
    global $defaultTarget;
    if ($defaultTarget & $bitmask) {
        echo ' checked="checked"';
    }
    echo ' />';
}

function createSearchPages() {
    global $totalSearchPages;
    global $searchPage;

    for($i = 1; $i < $totalSearchPages+1; $i++) {
        if($i == $searchPage) {
            echo '<span class="calsearch_footer_active">' . $i . '</span> ';
        } else {
            echo '<a href="' . createSearchPage($i) . '">' . $i . '</a> ';
        }
    }
}

function createSearchPage($page) {
    $url = 'agenda.php?';
    $first = true;
    $found = false;

    foreach($_GET as $k => $v) {
        if($k == 'p') {
            $v = $page;
            $found = true;
        }

        if(!$first) {
            $url .= '&';
        }

        $url .= $k . '=' . $v;

        if($first) {
            $first = false;
        }
    }

    if(!$found) {
        if($first) {
            $url .= 'p=' . $page;
        } else {
            $url .= '&p=' . $page;
        }
    }

    return $url;
}

function getFollowingEvents() {
    $queryFollowing = "SELECT event_id FROM Following WHERE user_id=?";

    if(!isset($_SESSION['loggedin'])) {
        return array();
    }

    $id = $_SESSION['id'];
    $results = array();
    global $mysqli;
    if ($stmt = $mysqli->prepare($queryFollowing)) {
        $stmt->bind_param('i', $id);

        if ($stmt->execute() && $stmt->store_result()) {
            $stmt->bind_result($event_id);

            while ($stmt->fetch()) {
                $results[] = $event_id;
            }
        }

        $stmt->close();
    } else {
        echo 'Er zit een fout in de query: ' . $mysqli->error;
    }

    return $results;
}

function createHiddenFields() {
    createHiddenField('y');
    createHiddenField('m');
    createHiddenField('d');
}

function createHiddenField($name) {
    if(isset($_GET[$name])) {
        echo '<input type="hidden" name="' . $name . '" value="' . strip_tags($_GET[$name]) . '" />';
    }
}

function createFollowUrl($id) {
    $request = $_GET;
    $request['follow'] = $id;
    return urlFromArray('agenda.php', $request);
}

function createUnfollowUrl($id) {
    $request = $_GET;
    $request['unfollow'] = $id;
    return urlFromArray('agenda.php', $request);
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

        <?php createHiddenFields(); ?>

        <input type="submit" class="css3button" id="search_button" value="Zoeken" />

    </form>

    <?php echo $results ?>

    <div id="calsearch_footer">
        <p><?php createSearchPages() ?></p>
    </div>

</div>

<script src="js/slide.js" type="text/javascript"></script>
<script type="text/javascript">slide.init();</script>
