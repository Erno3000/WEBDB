<?php

$title = "Event Management";
include("header.php");
include('config.php');

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if (!$result = $mysqli->query("SELECT event_id, user_id, subject, start_date, place FROM Events WHERE approved=0 ORDER BY start_date DESC LIMIT 20")) {
    trigger_error('Fout in query: ' . $mysqli->error);
}

?>


<div id="content">
<!--A list of all events that need approval (flag=0 in table events.).-->
    <div id="eventlist">
        <form action="">
            <select name="events">
                <option value="" disabled selected>event_id / start_date / subject</option>
                <?php {
                    while ($row = $result->fetch_row()) {
                        $rowname=$row['0']." / ".$row['3']." / ".$row['2'].".";
                        echo '<option value="">' . $rowname . '</option>';
                    }
                } ?>
            </select>
        </form>
    </div>

    <!--Event information displayed like 2 columns.-->
    <div id="eventinfo">
        <ul>
            <li>
                <label for="">event_id</label>

            </li>
            <li>
                <label for=""></label>

            </li>
            <li>
                <label for=""></label>

            </li>
        </ul>
    </div>
</div>







