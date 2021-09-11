<?php

$site_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml");

if (strlen($site_xml->userstack_key) > 5 && strlen($site_xml->ipstack_key) > 5){
    require "tracking/tracking.php";
}

$query = htmlspecialchars($_SERVER['QUERY_STRING']);
parse_str($query);
header("Location: /uploads/" . $file);

echo "Click <a href='/uploads/" . $file . "'>here</a> if you are not redirected.";

?>