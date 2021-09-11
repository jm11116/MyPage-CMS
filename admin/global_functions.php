<?php
function writeToLog($report){
    try {
        $xml = simplexml_load_file(dirname(__DIR__, 1) . "/site_info.xml");
        $time = new DateTime(null, new DateTimeZone($xml->timezone));
        $current_date = $time->format("d-M-y");
        $current_time = $time->format("h:i:s a");
    } catch (Exception $e) {
        writeToLog($e->getMessage());
    }
    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/admin/log.txt")){
        $file = fopen($_SERVER["DOCUMENT_ROOT"] . "/admin/log.txt", "w");
        fclose($file);
    }
    $file = fopen($_SERVER["DOCUMENT_ROOT"] . "/admin/log.txt", "r+");
    $existing_contents = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/admin/log.txt");
    fwrite($file, $current_date . " " . $current_time . ": " . $report . "\n" . $existing_contents);
    fclose($file);
}
?>