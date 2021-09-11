<?php

function echoBackupList(){
    $file_array = scandir($_SERVER['DOCUMENT_ROOT'] . $subdir . "/admin/post_backups/", SCANDIR_SORT_DESCENDING);
    $file_count = 0;
    natsort($file_array);
    $file_array = array_reverse($file_array);
    foreach ($file_array as $file){
        $parts = explode(" - ", $file);
        $parts1 = explode("@", $parts[0]);
        $parts1[1] = str_replace(".php", "", $parts1[1]);
        if (strlen($parts1[1]) > 15){
            $text = substr($parts1[1], 0, 15) . "..";
            $text = $text . " - " . str_replace(".txt", "", $parts[1]);
        } else {
            $text = str_replace(".txt", "", $parts1[1]) . " - " . str_replace(".txt", "", $parts[1]);
        }
        if (strpos($file, ".txt") != false){
            $file_count++;
            $exploded1 = explode("@", $file);
            echo "<a href=javascript:void(0);" . 
            " id='" . $file . "'" . 
            "class='backup_link'"  . 
            ">" . $text .
            "</a>" .
            " <button id='" . 
            $file . "'" . 
            "class='backup_delete'>Delete</button>" .
            "<button id='" . 
            $file . "'" .
            "class='backup_restore'>Restore</button>" .
            "<br>";
        }
    }
    if ($file_count === 0){
        echo "No backups available yet.";
    }
}
echoBackupList();

?>