<!DOCTYPE html>
<head>

<?php 

session_start();
if (!isset($_SESSION['can_view'])) { //Allow only views from tracking page.
    die();
}

$settings = simplexml_load_file(dirname(__DIR__, 1) . "/site_info.xml");

?>

<meta charset="UTF-8">
<title>
<?php 
    $query = htmlspecialchars($_SERVER['QUERY_STRING']);
    $exploded = explode("@", $query);
    if ($query != "all"){
        $title = str_replace(".txt", "", str_replace("%20", " ", $exploded[1]));
        echo $title . " – " . $settings->sitename . " Tracking Data";
    } else {
        $time = new DateTime(null, new DateTimeZone($settings->timezone));
        $date = $time->format("D d M Y");
        echo $settings->sitename . " Tracking Data as of " . $date;
    }
?>
</title>
<link href="styles.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<?php

    if ($query == "all"){
        $array1 = scandir(dirname(__DIR__, 1) . "/tracking/tracking_logs/");
        natsort($array1);
        $array = array_reverse($array1); //Unlike natsort, array_reverse needs to be assigned to a new variable!
        foreach ($array as $log_file){
            if ($log_file[0] != "."){
                echo file_get_contents(dirname(__DIR__, 1) . "/tracking/tracking_logs/". $log_file);
            }
        }
    } else {
            echo "<br><h1 style='text-align:center;font-size:2em;font-style:normal;font-weight:500;'>" . $title . "</h1>";
            echo file_get_contents(dirname(__DIR__, 1) . "/tracking/tracking_logs/" . str_replace("%20", " ", $query));
    }

?>

</body>
</html>