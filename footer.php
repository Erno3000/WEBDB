<?php

$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')
    === FALSE ? 'http' : 'https';
$host     = $_SERVER['HTTP_HOST'];
$script   = $_SERVER['SCRIPT_NAME'];

$currentUrl = strip_tags($protocol . '://' . $host . $script);

?>

</div>
<div id="footer">
    <div id="validator">
        <a href="http://validator.w3.org/check?uri=<?php echo $currentUrl?>">
            <img src="http://www.w3.org/Icons/valid-xhtml11" alt="Valid XHTML 1.1" height="31" width="88" /></a>
        <a href="http://jigsaw.w3.org/css-validator/check/<?php echo $currentUrl?>">
            <img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="Valid CSS" /></a>
    </div>
</div>
</body>
</html>