<?php

    include('config.php');

    const EMPLOYEES = 0x01;
    const SHAREHOLDERS = 0x02;
    const CUSTOMERS = 0x04;

    $queryFollow = "INSERT INTO Following (user_id, event_id) VALUES (?, ?)";
    $queryUnfollow = "DELETE FROM Following WHERE user_id=? AND event_id=?";
    $queryEventExists = "SELECT event_id FROM Events WHERE event_id=?";


    function() {
        echo 'stupid function created for git test';
    }


    function urlFromArray($page, $array) {
        $page .= '?';
        $first = true;
        foreach($array as $k => $v) {
            if($first) {
                $page .= $k . '=' . $v;
                $first = false;
            } else {
                $page .= '&' . $k . '=' . $v;
            }
        }

        return $page;
    }

    function followEvent($userId, $eventId) {
        global $dbHost, $dbUser, $dbPass, $dbName, $queryFollow;
        $success = false;
        $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        if (eventExists($eventId) && $stmt = $mysqli->prepare($queryFollow)) {
            $stmt->bind_param('ii', $userId, $eventId);

            if ($stmt->execute()) {
                $success = true;
            }

            $stmt->close();
        } else {
            echo 'Er zit een fout in de query: ' . $mysqli->error;
        }

        return $success;
    }

    function unfollowEvent($userId, $eventId) {
        global $dbHost, $dbUser, $dbPass, $dbName, $queryUnfollow;
        $success = false;
        $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        if (eventExists($eventId) && $stmt = $mysqli->prepare($queryUnfollow)) {
            $stmt->bind_param('ii', $userId, $eventId);

            if ($stmt->execute()) {
                $success = true;
            }

            $stmt->close();
        } else {
            echo 'Er zit een fout in de query: ' . $mysqli->error;
        }

        return $success;
    }

    function eventExists($id) {
        global $dbHost, $dbUser, $dbPass, $dbName, $queryEventExists;
        $success = false;
        $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        if ($stmt = $mysqli->prepare($queryEventExists)) {
            $stmt->bind_param('i', $id);

            if ($stmt->execute() && $stmt->store_result()) {
                $stmt->bind_result($eventId);
                $numResults = $stmt->num_rows;
                $success = $numResults > 0;
            }

            $stmt->close();
        } else {
            echo 'Er zit een fout in de query: ' . $mysqli->error;
        }

        return $success;
    }

?>