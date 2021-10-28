<?php session_start();?>

<link rel="apple-touch-icon" sizes="76x76" href="../icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="../icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="../icons/favicon-16x16.png">
<link rel="manifest" href="../icons/site.webmanifest">
<link rel="mask-icon" href="../icons/safari-pinned-tab.svg" color="#5bbad5">
<link rel="shortcut icon" href="../icons/favicon.ico">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="msapplication-config" content="../icons/browserconfig.xml">
<meta name="theme-color" content="#ffffff">

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>


<link rel="stylesheet" href="../themes/core.css">

<div id="next"><img src="/assets/next.png"></div>
<div id="prev"><img src="/assets/prev.png"></div>
<div id="loading_container"><img src="/assets/loading.gif" id="image_loading" ></div>
<!--Gallery code in footer-->

<script>
    function imgError(){
        $("#next").hide();
    }
</script>
<div id="lightbox"><img id="popup_image" onerror="imgError()"></div>

<?php

include $_SERVER["DOCUMENT_ROOT"] . "/admin/global_functions.php";

$page_name = str_replace(".php", "", basename($_SERVER['PHP_SELF']));
$txt_path = "../pages_text/" . $page_name . ".txt";
$blog_txt_path = "../posts_text/" . $page_name . ".txt";
$page_title = strtoupper($page_name);
$site_info = simplexml_load_file($_SERVER["DOCUMENT_ROOT"] . "/site_info.xml");
if ($site_info->site_enabled != "enabled"){
    exit("<div style='padding-top:30vh;display:block;margin:auto;'><p style='color:white;text-align:center;font-size:2em;'>This website is offline.</p></div>");
}
echo "<link href='../themes/" . strtolower($site_info->theme) . "/styles.css' rel='stylesheet'>";

$site_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml");

if (strlen($site_xml->userstack_key) > 5 && strlen($site_xml->ipstack_key) > 5){
    require "../tracking/tracking.php";
}

?>

<div class="background"></div>

<?php 

if (strtolower($site_info->theme) == "barber" || strtolower($site_info->theme) == "feature"){
    $file_array = scandir($_SERVER['DOCUMENT_ROOT'] . "/themes/" . strtolower($site_info->theme) . "/backgrounds");
    $new_file_array = [];
    foreach ($file_array as $file){
        if ($file[0] != "."){
            array_push($new_file_array, $file);
        }
    }
    shuffle($new_file_array);
    echo "<script>";
    echo "var backgrounds_array = [";
    foreach ($new_file_array as $file){
        echo "'" . $file . "'" . ",";
    }
    echo "];</script>\n";
} else {
    echo "<script>\n";
    echo "var backgrounds_array = ['null'];\n";
    echo "</script>\n";
}
echo "<script>\n";
echo "var site_theme = '" . strtolower($site_info->theme) . "';\n";
echo "</script>\n";

?>

<script src='/scripts/animations.js'></script>

<meta charset="UTF-8">
<title><?php echo ucfirst(strtolower($page_title)) . " – " . $site_info->title_descript;?></title>
<meta name="description" content="<?php echo $site_info->tagline;?>">
<meta name="keywords" content="<?php echo $site_info->keywords;?>">
<meta name="author" content="<?php echo $site_info->owner;?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://kit.fontawesome.com/dd7fe6b2fc.js" crossorigin="anonymous"></script>

<div class="content_box">

<div class="subheading">
<a class="header" href="/pages/index.php"><?php echo strtoupper($site_info->sitename);?></a>
<div class="tagline"><?php echo $site_info->tagline;?></div>

<?php include "nav.php"; ?>

</div>