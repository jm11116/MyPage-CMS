<?php

parse_str($_POST["data"]);

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

if (filter_var($email, FILTER_VALIDATE_EMAIL) === false){
    echo "The email you entered is invalid! Settings NOT saved!";
} else {
    $file = fopen($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml", "r+");
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml", "");
    fwrite($file, '<?xml version="1.0"?><info>');
    fwrite($file, "\n<sitename>" . stripslashes($sitename) . "</sitename>\n");
    fwrite($file, "<tagline>" . stripslashes(htmlspecialchars($tagline)) . "</tagline>\n");
    fwrite($file, "<keywords>" . stripslashes(htmlspecialchars($keywords)) . "</keywords>\n");
    fwrite($file, "<owner>" . stripslashes(htmlspecialchars($owner)) . "</owner>\n");
    fwrite($file, "<title_descript>" . stripslashes(htmlspecialchars($title_descript)) . "</title_descript>\n");
    fwrite($file, "<company>" . stripslashes(htmlspecialchars($company)) . "</company>\n");
    fwrite($file, "<email>" . filter_var($email, FILTER_SANITIZE_EMAIL) . "</email>\n");
    file_put_contents("code.php", $code);
    if (isset($site_enabled)){
        fwrite($file, "<site_enabled>" . "enabled" . "</site_enabled>\n");
    } else {
        fwrite($file, "<site_enabled>" . "disabled" . "</site_enabled>\n");
    }
    if (isset($contact_enabled)){
        fwrite($file, "<contact_enabled>" . "true" . "</contact_enabled>\n");
    } else {
        fwrite($file, "<contact_enabled>" . "false" . "</contact_enabled>\n");
    }
    if (isset($blog_enabled)){
        fwrite($file, "<blog_enabled>" . "true" . "</blog_enabled>\n");
    } else {
        fwrite($file, "<blog_enabled>" . "false" . "</blog_enabled>\n");
    }
    fwrite($file, "<hidden>" . stripslashes(htmlspecialchars($hidden)) . "</hidden>\n");
    fwrite($file, "<theme>" . stripslashes(htmlspecialchars($theme)) . "</theme>\n");
    fwrite($file, "<userstack_key>" . stripslashes(htmlspecialchars($userstack_key)) . "</userstack_key>\n");
    fwrite($file, "<ipstack_key>" . stripslashes(htmlspecialchars($ipstack_key)) . "</ipstack_key>\n");
    fwrite($file, "<timezone>" . stripslashes(htmlspecialchars($timezone)) . "</timezone>\n");
    fwrite($file, "<no_track>" . stripslashes(htmlspecialchars($no_track)) . "</no_track>\n");
    fwrite($file, "<alert_pages>" . stripslashes(htmlspecialchars($alert_pages)) . "</alert_pages>\n");
    fwrite($file, "</info>");
    fclose($file);
    include "create_website_nav.php";
    createWebsiteNavFile();
    echo "Your settings have been saved!";
}
}

?>