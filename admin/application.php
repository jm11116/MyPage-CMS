<!DOCTYPE HTML>

<head>

<noscript>
<p id="javascript_warning">JavaScript must be enabled!</p> 
<style>#main, #title_container{ display:none; }</style>
</noscript>

<?php
session_start();
if (empty($_SESSION['success']) || $_SESSION['success'] != 1){
  die("<div style='display:block;margin:auto;text-align:center;width:100%;margin-top:30vh;font-size:1.2em;'>You are not logged in. Click <a href='index.php'>here</a> to go to login page.</div>");
}
?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="icons/favicon-16x16.png">
<link rel="manifest" href="icons/site.webmanifest">

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script   src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"   integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="   crossorigin="anonymous"></script>

<link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed:500,700" rel="stylesheet" type="text/css"/>
<link href="https://fonts.googleapis.com/css?family=Muli:400,400i,800,800i" rel="stylesheet" type="text/css"/>

<link href="styles.css" rel="stylesheet" type="text/css"/>

<title>MyPage CMS (Beta 2)</title>

</head>

<body>

<div id="title_container">

<h1>MyPage CMS (Beta)</h1>

<div id="navbar">
<a href="javascript:void(0);" id="home_link" class="section_link">Home</a>
<a href="javascript:void(0);" id="editor_link" class="section_link">Editor</a>
<a href="javascript:void(0);" id="blog_link" class="section_link">Blog</a>
<a href="javascript:void(0);" id="tracking_link" class="section_link">Tracking</a>
<a href="javascript:void(0);" id="uploads_link" class="section_link">Uploads</a>
<a href="javascript:void(0);" id="settings_link" class="section_link">Settings</a>
<a href="../pages/" target="_blank" id="view_link" >View</a>
<a href="logout.php" id="view_link" >Logout</a>
</div>

</div>

<div id="main"></div>

<?php

$subdir = "";

//Load site settings from XML to used globally:
if (file_exists($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml")){
    $site_info = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . $subdir . "/admin/admin_settings.xml");
} else {
    $xml_file = fopen($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml", "w");
    fclose($xml_file);
}

//Set timezone globally:
date_default_timezone_set($site_info->timezone);

?>

<script>

$(document).ready(function(){

function preloadImage(url)
{
    var img = new Image();
    img.src = url;
}
preloadImage("assets/loading.gif");

//Prevent jQuery caching AJAX calls:
$.ajaxSetup ({
    cache: false
});

var base_font_size = "1em";
var bigger_font_size = "1.3em";

//Load section from query string or make a new one:

class Query {
    constructor(){
        this.url = window.location.href;
        this.keyvaluesarray = this.url.split(/\?|\=|\&/g);
        this.present = function(){
            if (this.url.indexOf("?") == "-1"){
                return false;
            } else {
                return true;
            }
        }
        this.parts = function(part){
            if (this.present() == true){
                this.array = this.url.split("?");
                if (part == "url"){
                    return this.array[0];
                } else if (part == "query"){
                    return this.array[1];
                }
            }
        }
        this.append = function(key, value){
            this.existing = "?" + this.parts("query") + "&";
            this.combined = this.existing + key + "=" + value;
            if (this.present() == true){
                window.history.replaceState("", "", this.combined);
            } else {
                window.history.replaceState("", "", "?" + key + "=" + value);
            }
        }
        this.replace = function(key, value){
                window.history.replaceState("", "", "?" + key + "=" + value);
        }
        this.getvaluefromkey = function(key){
            if (this.keyvaluesarray.includes(key) == true){
                this.valuepos = this.keyvaluesarray.indexOf(key) + 1;
                return this.keyvaluesarray[this.valuepos];
            } else {
                return false;
            }
        }
        this.getkeyfromvalue = function(value){
            if (this.keyvaluesarray.includes(value) == true){
                this.valuepos = this.keyvaluesarray.indexOf(value) - 1;
                return this.keyvaluesarray[this.valuepos];
            } else {
                return false;
            } 
        }
        this.loadfromquery = function(){
            if (this.present() == true && this.getvaluefromkey("section") != false) {
                this.pagetoload = this.getvaluefromkey("section");
                console.log(this.pagetoload);
                $("#main").load(this.pagetoload);
                $(".section_link").css("text-decoration", "none");
                this.id = "#" + this.pagetoload.replace(".php", "_link");
                $(".section_link").css("font-size", base_font_size);
                $(this.id).css("text-decoration", "underline");
                $(this.id).css("font-size", bigger_font_size);
            } else {
                $("#main").load("home.php");
                this.replace("section", "home.php");
                $("#home_link").css("text-decoration", "underline");
                $("#home_link").css("font-size", bigger_font_size);
            }
        }
    }
}
query = new Query;
query.loadfromquery();

$(".section_link").click(function(){
    var id = $(this).attr("id");
    var link = id.replace("_link", ".php");
    query.replace("section", link)
    $(".section_link").css("text-decoration", "none");
    $(this).css("text-decoration", "underline");
    var base_font_size = "1em";
    var bigger_font_size = "1.3em";
    $(".section_link").css("font-size", base_font_size);
    $(this).css("font-size", bigger_font_size);
            $.ajax({
            beforeSend: function(){
                $("#main").html("<div id='loading'></div>");
            },
            url: link,
            type: "POST",
            error: function(xhr){
                alert("An error occured: " + xhr.status + " " + xhr.statusText);
            },
            success: function(data){
                console.log("Section loaded.");
                $("#main").html(data);
            }
        });
});

});

</script>

<div class="bottom_spacer"></div>

</body>

</html>