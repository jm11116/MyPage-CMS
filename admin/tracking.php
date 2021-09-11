<div class="grid-flex">

<div id="tracking_data" class="grid-item" style="flex-grow: 35;">

<h2 style="padding-bottom:14px;">Tracking Data</h2>

<div id="tracking_list">

<?php 

session_start();
$_SESSION['can_view'] = true; //Only allow tracking page views from this page.

$file_array = scandir($_SERVER['DOCUMENT_ROOT'] . $subdir . "/tracking/tracking_logs/");
natsort($file_array);
$reversed = array_reverse($file_array); //Unlike natsort, array_reverse needs to be assigned to a new variable! It can also work when wrapped around a variable!

$file_count = 0;

foreach ($reversed as $file){
    if (strpos($file, ".txt") != false){
        $file_count++;
        $exploded = explode("^", $file);
        $exploded2 = explode("@", $exploded[1]);
        echo "<a href='javascript:void(0)' class='tracking_link' id='" . $file . "'>" . str_replace(".txt", "", $exploded2[1] . " (" . $exploded2[0] . ")") . "</a>";
        echo " <button class='day_delete_button' id='" . $file . "'>Delete</button>";
        echo " <a href='tracking_viewer.php?" . $file . "' target='_blank'><button class'download_day'>Export</button></a><br>";
    }
}

if ($file_count === 0){
    echo "No tracking data available yet.";
}

function getIP (){
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = $_SERVER['HTTP_CLIENT_IP'];
        $forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
        if (filter_var($client, FILTER_VALIDATE_IP)){
            $ip = $client;
        } elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $ip = $forward;
        } else {
            $ip = $remote;
        } 
        $address = $ip;
    }
?>

</div>
<br>
<i style='margin-left:10px;'><a href="tracking_viewer.php?all" target="_blank" style='color:#6f42c1;font-style:bold;'>Export All ></a></i>

</div>

<div id="tracking" class="grid-item" style="flex-grow: 70;">

<div id="tracking_loading"></div>

<h2>Tracking Viewer</h2>
<div id="tracking_container">
<?php

$reversed_array = array_reverse($file_array);
$file_count = 0;
foreach ($reversed_array as $file){
    if (strpos($file, ".txt") != false){
        $file_count++;
    }
}
if ($file_count != 0){
    echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . $subdir . "/tracking/tracking_logs/" . $reversed_array[0]);
}


?>
</div>

<button class="back_to_list">< Back to List</button>

</div>

</div>

<script>

$(document).ready(function(){

var viewing = false;

$(".back_to_list").click(function(){
    if ($(window).width() < 800 || $(window).height() > $(window).width()){
        $("#tracking").hide();
        $("#tracking_data").show();
        $("html, body").animate({ scrollTop: 0 }, 1000);
        viewing == false;
    }
});

$(".day_delete_button").click(function(){
    if (confirm("Are you sure you want to delete this day of tracking data? This operation cannot be undone!")){
        var id = $(this).attr("id");
            $.ajax({
            url: "tracking.php",
            data:{
                delete_day: id
            },
            type: "POST",
            error: function(xhr){
                alert("An error occured: " + xhr.status + " " + xhr.statusText);
            },
            success: function(data){
                alert("Tracking data deleted.");
                $("#main").load("tracking.php");
            }
        });
    }
});

if ($(window).width() < 800  || $(window).height() > $(window).width()){
    $("#tracking").hide();
}
$(window).resize(function(){
    if ($(window).width() > 800 || $(window).height() > $(window).width()){
        $("#tracking").show();
        $("#tracking_data").show();
        $(".back_to_list").hide();
    }
    if (viewing == true && $(window).width() < 800 || viewing == true && $(window).height() > $(window).width()){
        $("#tracking_data").hide();
        $("#tracking").show();
        $(".back_to_list").show();
    }
    if (viewing == false && $(window).width() < 800 || viewing == false && $(window).height() > $(window).width()){
        $("#tracking_data").show();
        $("#tracking").hide();
        $(".back_to_list").hide();
    }
});

function changeFirstLinkColor(){
    $(".tracking_link:first").css("color", "#6f42c1");
    $(".tracking_link:first").css("font-style", "italic");
    $(".tracking_link:first").css("font-weight", "bold");
}
changeFirstLinkColor();

$(".tracking_link").click(function(){
    $("#tracking_loading").show();
    viewing = true;
    if ($(window).width() < 800  || $(window).height() > $(window).width()){
        $("#tracking_data").hide();
        $("#tracking").show();
        $(".back_to_list").show();
    }
    $("html, body").animate({ scrollTop: $(document).height() }, 1000);
    var id = $(this).attr("id");
    var id_fixed = encodeURI(id);
    $("#tracking_container").load("/tracking/tracking_logs/" + id_fixed, function(){
        $("#tracking_loading").hide(); //jQuery load success function.
    });
    $(".tracking_link").css("color", "blue");
    $(".tracking_link").css("font-weight", "normal");
    $(".tracking_link").css("font-style", "normal");
    $(this).css("color", "#6f42c1");
    $(this).css("font-style", "italic");
    $(this).css("font-weight", "bold");
});

});
</script>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_day'])){
    unlink($_SERVER["DOCUMENT_ROOT"] . "/tracking/tracking_logs/" . $_POST['delete_day']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button_id'])){
    function getFileToDeleteFrom(){
        $file_array = scandir($_SERVER["DOCUMENT_ROOT"] . "/tracking/tracking_logs/", SCANDIR_SORT_DESCENDING);
        foreach ($file_array as $file){
            $step1 = explode("^", $file);
            if ($step1[0] === getPrefixFromId()){
                return $_SERVER["DOCUMENT_ROOT"] . "/tracking/tracking_logs/" . $file;
            }
        }
    }
    function renameFile(){
        $old_filename = getFileToDeleteFrom();
        $exploded1 = explode("^", $old_filename);
        $exploded2 = explode("@", $exploded1[1]);
        $count = (intval($exploded2[0]) - 1);
        $new_filename = $exploded1[0] . "^" . $count . "@" . $exploded2[1];
        if ($count === 0){
            unlink($old_filename);
        } else {
            rename($old_filename, $new_filename);
        }
    }
}
?>