<?php

//Get settings
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/site_info.xml")){
    $site_info = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . "/admin/admin_settings.xml");
} else {
    $xml_file = fopen($_SERVER['DOCUMENT_ROOT'] . "/site_info.xml", "w");
    fclose($xml_file);
}

//Set timezone globally:
date_default_timezone_set($site_info->timezone);

?>