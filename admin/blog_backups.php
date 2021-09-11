<?php
session_start();
if (empty($_SESSION['success']) || $_SESSION['success'] != 1){
  die("<div style='display:block;margin:auto;text-align:center;width:100%;margin-top:30vh;font-size:1.2em;'>You are not logged in. Click <a href='index.php'>here</a> to go to login page.</div>");
}
?>

<div class="grid-flex">


<div id="backed_up_pages" class="grid-item" style="flex-grow: 35;">

<h2>Blog Post Backups</h2>

<div id="backed_up_pages_container">

<?php 

require "global_functions.php";

//Ajax responses
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    switch ($_POST['request']){
        case "restore":
            restoreFromBackup();
            break;
        case "delete":
            deleteBackup();
            break;
        case "clear_all":
            clearAllBackups();
            break;
    }
}

function restoreFromBackup(){
    $backup_filename = $_POST['page'];
    $exploded1 = explode("@", $backup_filename);
    $exploded2 = explode(" - ", $exploded1[1]);
    $page_name = $exploded2[0];
    copy($_SERVER['DOCUMENT_ROOT'] . $subdir . "/admin/post_backups/" . $backup_filename, $_SERVER['DOCUMENT_ROOT'] . $subdir . "/posts_text/" . $page_name . ".txt");
    copy($_SERVER['DOCUMENT_ROOT'] . $subdir . "/admin/templates/new_post.php", $_SERVER['DOCUMENT_ROOT'] . $subdir . "/posts/" . $page_name . ".php");
    addToSort($page_name);
}

function addToSort($page_name){
    $json = json_decode(file_get_contents("sorted_blog.txt"), true);
    if (!in_array($page_name, $json)){
        array_push($json, $page_name . ".php");
        file_put_contents("sorted_blog.txt", json_encode($json));
    }
}

function deleteBackup(){
    if (unlink($_SERVER['DOCUMENT_ROOT'] . $subdir . "/admin/post_backups/" . $_POST['page'])){
        writeToLog($_POST['page'] . " backup file deleted successfully.");
    } else {
        writeToLog($_POST['page'] . " backup file could NOT be deleted!");
    }
    echoBackupList();
}

function clearAllBackups(){
    $file_array = scandir($_SERVER['DOCUMENT_ROOT'] . $subdir . "/admin/post_backups/");
    foreach ($file_array as $file){
        unlink($_SERVER['DOCUMENT_ROOT'] . $subdir . "/admin/post_backups/" . $file);
    }
}

?>

</div>

</div>











<div id="backup_viewer_container" class="grid-item" style="flex-grow: 55;">

<h2>Blog Backup Viewer</h2>
<div id="backup_viewer"></div>

<div id="backup_button_container">
<button class="backup_button" id="backup_delete">Delete</button>
<button class="backup_button" id="backup_restore">Restore</button>
<button class="backup_button" id="back_to_backup_list">< Back to List</button>
</div>

</div>



</div>











<script>

$(document).ready(function(){

$("#backup_delete").prop("disabled", true);
$("#backup_restore").prop("disabled", true);

var viewing = false;

$(window).resize(function(){
    if ($(window).width() > 800 || $(window).height() > $(window).width()){
        $("#backed_up_pages").show();
    }
    if (viewing == true && $(window).width() < 800 || $(window).height() > $(window).width()){
        $("#backed_up_pages").hide();
        $("#backup_viewer_container").show();
    }
});

$("#back_to_backup_list").click(function(){
    $("#backup_viewer_container").hide();
    $("#backup_viewer").html("");
    $("#backed_up_pages").show();
    $("#backup_delete").prop("disabled", true);
    $("#backup_restore").prop("disabled", true);
    $(".backup_link").css("color", "blue");
    $(".backup_link").css("font-weight", "normal");
    $(".backup_link").css("font-style", "normal");
});

function loadBackups(){
        $.ajax({
        url: "blog_backup_list.php",
        beforeSend: function(){
            $("#backed_up_pages_container").html("<br><div id='loading_nav'></div>");
        },
        type: "POST",
        error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        },
        success: function(data){
            $("#backed_up_pages_container").html(data);
            if ($(".backup_link").length === 0){
                $("#clear_backups_button").prop("disabled", true);
            } else {
                $("#clear_backups_button").prop("disabled", false);
            }
            bindings();
        }
    });
}

loadBackups();

var current_page = null;

function bindings(){
    $(".backup_link").click(function(){
        viewing = true;
        if ($(window).width() < 800 || $(window).height() > $(window).width()){
            $("#backed_up_pages").hide();
            $("#backup_viewer_container").show();
        }
        current_page = $(this).attr("id");
        $("#backup_delete").prop("disabled", false);
        $("#backup_restore").prop("disabled", false);
        $(".backup_link").css("color", "blue");
        $(".backup_link").css("font-weight", "normal");
        $(".backup_link").css("font-style", "normal");
        $(this).css("color", "#6f42c1");
        $(this).css("font-style", "italic");
        $(this).css("font-weight", "bold");
        var backup_page = encodeURIComponent($(this).attr("id")); //For some reason this URL needs to be escaped or it will throw a 404 on load.
            $.ajax({
            url: "post_backups/" + backup_page,
            type: "GET",
            error: function(xhr){
                alert("An error occured loading " + backup_page + ": " + xhr.status + " " + xhr.statusText);
            },
            success: function(data){
                console.log("Backup loaded.");
                $("#backup_viewer").text(data);
            }
        });
    });

    $("#backup_restore").click(function(){
    //Confirmation
        var page = current_page;
                $.ajax({
                url: "blog_backups.php",
                data:{
                    request: "restore",
                    page: page
                },
                type: "POST",
                error: function(xhr){
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                },
                success: function(data){
                    alert("Your page has been restored.");
                    $("#main").html(data);
                }
            });
    });

    $("#backup_delete").click(function(){
    //Confirmation
        var page = current_page;
                $.ajax({
                url: "blog_backups.php",
                data:{
                    request: "delete",
                    page: page
                },
                type: "POST",
                error: function(xhr){
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                },
                success: function(data){
                    $("#file_list").remove();
                    $("#main").load("blog_backups.php");
                    alert("Backup deleted.");
                }
            });
    });

    $("#clear_backups_button").click(function(){
        if (confirm("Are you sure you want to delete all backups? This action cannot be undone!")){
                $.ajax({
                url: "blog_backups.php",
                data:{
                    request: "clear_all"
                },
                type: "POST",
                error: function(xhr){
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                },
                success: function(data){
                    $("#main").load("blog_backups.php");
                    alert("All backups deleted.");
                }
            });
        }
    });
}

});
</script>