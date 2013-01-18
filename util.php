<?php

    const EMPLOYEES = 0x01;
    const SHAREHOLDERS = 0x02;
    const CUSTOMERS = 0x04;

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
    }

?>