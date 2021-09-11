<?php

function createWebsiteNavFile(){
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $subdir . "/nav.php")){
        fopen($_SERVER['DOCUMENT_ROOT'] . $subdir . "/nav.php", "w");
    }
    $nav_file = fopen($_SERVER['DOCUMENT_ROOT'] . $subdir . "/nav.php", "r+");
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . $subdir . "/nav.php", "");
    fwrite($nav_file, "<center><div class='nav_container'><br>");
    $sortArray = json_decode(file_get_contents("sorted_pages.txt"));
    $site_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml");
    $hidden_string = str_replace(", ", ",", $site_xml->hidden);
    $hidden_array = explode(",", $hidden_string);
    if ($site_xml->contact_enabled == "false"){
        $contact_index = array_search("contact.php", $sortArray);
        unset($sortArray[$contact_index]);
    }
    if ($site_xml->blog_enabled == "false"){
        $blog_index = array_search("blog.php", $sortArray);
        unset($sortArray[$blog_index]);
    }
    for ($i = 0; $i <= count($sortArray); $i++){
        if (strpos($sortArray[$i], ".php") && !in_array($sortArray[$i], $hidden_array)){
        $title = ucfirst(str_replace(".php", "", $sortArray[$i]));
                $link = "<a href='../pages/" . $sortArray[$i]. "' class='nav-link'>" . $title . "</a><p class='separator'> | </p>";
                fwrite($nav_file, $link);
        }
    }
        fwrite($nav_file, "<br></div></center>");
}

?>