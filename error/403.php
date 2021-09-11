<?php

$site_xml = simplexml_load_file(dirname(__DIR__, 1) . "/site_info.xml");

if (strlen($site_xml->userstack_key) > 5 && strlen($site_xml->ipstack_key) > 5){
    require "../tracking/tracking.php";
}

echo "<div id='403' style='width:100vw;text-align:center;display:block;margin:auto;margin-top:39vh;font-size:1.6em;'>403 Forbidden</div>";

?>