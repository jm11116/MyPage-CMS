<?php

function echoBackupList(){
    $file_array = scandir($_SERVER['DOCUMENT_ROOT'] . $subdir . "/admin/backups/", SCANDIR_SORT_DESCENDING);
    $file_count = 0;
    natsort($file_array);
    $file_array = array_reverse($file_array);
    foreach ($file_array as $file){
        if (strpos($file, ".txt") != false){
            $file_count++;
            $exploded1 = explode("@", $file);
            echo "<a href=javascript:void(0);" . 
            " id='" . $file . "'" . 
            "class='backup_link'"  . 
            ">" .
            str_replace(".txt", "", $exploded1[1]) . 
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