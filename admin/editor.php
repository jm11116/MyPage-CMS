<?php

$site_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml");

session_start();
if (empty($_SESSION['success']) || $_SESSION['success'] != 1){
  die("<div style='display:block;margin:auto;text-align:center;width:100%;margin-top:30vh;font-size:1.2em;'>You are not logged in. Click <a href='index.php'>here</a> to go to login page.</div>");
}
?>

<div class="grid-flex">



<div id="your_pages" class="grid-item" style="flex-grow: 45;">

<h2>Your Pages</h2>

<p>Your pages (drag to rearrange):</p>

<div id="nav_container"></div>

<button id="create_new_page">Create New Page</button>
<button id="view_backups">View Backups</button>
<br><br>

<p style="color: grey;">
<?php
if ($site_xml->blog_enabled == "false" && $site_xml->contact_enabled == "false"){
    echo "<i>Your contact form and blog are disabled. You can reenable them in settings.</i>";
} elseif ($site_xml->contact_enabled == "false"){
    echo "<i>Your contact form is disabled. You can reenable it in settings.</i>";
} elseif ($site_xml->blog_enabled == "false"){
    echo "<i>Your blog is disabled. You can reenable it in settings.</i>";
}
?>
</p>

</div>











<div id="editor" class="grid-item" style="flex-grow: 80;">

<div id="page_text_loading"></div>

<h2>Editor</h2>

<p id="current_page"><b>Currently editing:</b> None</p>

<form method="POST" action="javascript:void(0);">
<textarea id="page_editor"></textarea><br>
<div id="save_buttons">
    <input type="submit" name="submit" value="Save" id="editor_submit">
    <button id="expand">Toggle Fullscreen</button>
    <button class="back_to_list">< Back to List</button>
</div>
</form>

</div>

</div>




<?

require "global_functions.php";

require "create_website_nav.php";

//Main Ajax responses:
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    switch ($_POST['request']){
        case "delete":
            deletePage();
            break;
        case "create":
            createPageFromTemplate();
            break;
        case "rename":
            renamePage();
            break;
        case "write_sort":
            writeSortToFile();
            break;
        case "recreate_nav_file":
            recreateNavFile();
            break;
        case "save_page":
            savePage();
            break;
        case "backup_page":
            backupPage();
            break;
        case "load_page":
            loadPageContent();
            break;
    }
}

//Scans directory list and creates a new sort array in sorted_pages.txt, then uses createNavFileFromSortArray() to create a new nav file:
function recreateNavFile(){
    if (!file_exists("sorted_pages.txt")){
        $file = fopen("sorted_pages.txt", "w");
        fclose($file);
    }
    $raw_page_list = scandir($_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages/");
    $page_list = [];
    foreach ($raw_page_list as $page){
        if ($page != "." && $page != ".." && $page != ".DS_Store"){
            array_push($page_list, $page);
        }
    }
    $json = json_encode($page_list);
    file_put_contents("sorted_pages.txt", "");
    file_put_contents("sorted_pages.txt", $json);
    header("Location: editor.php");
}

//Results in a file to be loaded with loadNav(). Automatically triggered on page load before loadNav() does its thing!
function createNavFileFromSortArray(){
    if (!file_exists("nav.php")){
        fopen("nav.php", "w");
    }
    $nav_file = fopen("nav.php", "r+");
    file_put_contents("nav.php", "");
    fwrite($nav_file, "<ul id='sortable'>");
    $sortArray = json_decode(file_get_contents("sorted_pages.txt"));
    foreach ($sortArray as $item){
        $link = "<div id='" . $item . "'><a href='javascript:void(0);' class='page_link' id='" . $item . "'>" . " " . $item . " " . "</a><button class='delete_button' id='". $item . "'>Delete</button><button class='rename_button' id='". $item . "'>Rename</button></div>";
        fwrite($nav_file, $link);
    }
    fwrite($nav_file, "</ul>");
    fclose($nav_file);
    createWebsiteNavFile();
}
createNavFileFromSortArray();

function deletePage(){
    unlink($_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages/". $_POST['page']);
    $text_file = str_replace(".php", ".txt", $_POST['page']);
    unlink($_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages_text/" . $text_file);
    createNavFileFromSortArray();
    createWebsiteNavFile();
}

function writeSortToFile(){
    file_put_contents("sorted_pages.txt", $_POST['sort']);
    createNavFileFromSortArray();
    createWebsiteNavFile();
}

function renamePage() {
    rename($_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages/" . $_POST['page'], $_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages/" . $_POST['new_name']);
    $txt_file_to_rename = str_replace(".php", ".txt", $_POST['page']);
    $new_txt_name = str_replace(".php", ".txt", $_POST['new_name']);
    rename($_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages_text/" . $txt_file_to_rename, $_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages_text/" . $new_txt_name);
    createNavFileFromSortArray();
    createWebsiteNavFile();
}

function createPageFromTemplate(){
    if (copy("templates/new_page.php", $_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages/" . $_POST['name'])){
        writeToLog($_POST['name'] . " php file created successfully!");
    } else {
        writeToLog($_POST['name'] . " php file NOT created successfully!");
    }
    if (file_put_contents($_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages_text/" . str_replace(".php", ".txt", $_POST['name']), "This is your new page's text.")){
        writeToLog($_POST['name'] . " text file created successfully.");
    } else {
        writeToLog($_POST['name'] . " text file NOT created successfully!");
    }
    createWebsiteNavFile();
}

function errorCheck($type, $page1 = NULL, $page2 = NULL){ //Sets optional parameters.
    switch ($type){
        case "mismatch":
            $server_pages = scandir($_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages/");
            unset($server_pages[0]); //Removes ., .., and .DS_Store
            unset($server_pages[1]);
            unset($server_pages[2]);
            $sorted_file = json_decode(file_get_contents("sorted_pages.txt"));
            echo "<br>";
            echo "<br>";
            echo "<br>";
            if (array_values($server_pages) == array_values($sorted_file)){ //Convert associative arrays to indexed arrays for easy comparison.
                $report = "The sort file matches the files on the server. No problems detected.";
            } else {
                $report = "The sort file doesn't match the files on the server. Advise resort!";
            }
            writeToLog($report);
            break;
        case "writable":
            if (is_writable($_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages/") === true && is_writable($_SERVER['DOCUMENT_ROOT'] . $subdir . "/") === true){
                $report = "'Pages' and 'website' directories are writable. No problems here.";
            } else {
                $report = "'Pages' and 'website' directories are NOT writable.";
            }
            writeToLog($report);
            break;
    }
}

//Delete log file option.



?>

<script>

$(document).ready(function(){

$("#view_backups").click(function(){
    query.replace("section", "backups.php");
    $("#main").load("backups.php");
});

$("#nav_container").html("<div id='loading_nav'></div>");

function toggleEditMode(mode){
    if (mode == "disable"){
        $("#page_editor").prop("disabled", true);
        $("#save_backup").prop("disabled", true);
        $("#editor_submit").prop("disabled", true);
        $("#expand").prop("disabled", true);
        $("#current_page").html("<b>Currently editing: </b>" + "None");
        $("#page_editor").val("");
    } else if (mode == "enable"){
        $("#page_editor").prop("disabled", false);
        $("#save_backup").prop("disabled", false);
        $("#editor_submit").prop("disabled", false);
        $("#expand").prop("disabled", false);
    }
}
toggleEditMode("disable");

var fullscreen = false;

$("#expand").click(function(event){
    event.preventDefault();
    if (fullscreen == false){
        $("#your_pages").hide();
        fullscreen = true;
    } else if (fullscreen == true){
        $("#your_pages").show();
        fullscreen = false;
    }
});

//Set preview window size:
$("#preview").css("height", ($(window).height() - 110));

var globalSortArray = []; //Keeps page list available at all times to check for rename and create collisions. Recreated every time loadNav() is called.
var current_page = ""; //Page currently being edited in global scope.
var current_txt = "";
var no_edit = ["index.php", "contact.php", "blog.php"];

//Nav code:

function sendSortViaAjax(array_file){
        $.ajax({
        url: "editor.php",
        data:{
            request: "write_sort",
            sort: array_file
        },
        type: "POST",
        error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        },
        success: function(data){
            console.log("Sort order being written to file...");
            loadNav();
        }
    });
}

var editing = false;

function loadNav(){
    $("#nav_container").html("<div id='loading_nav'></div>");
    $("#nav_container").load("nav.php", function(){
        $(".delete_button").unbind().click(function(event){ //Unbind() prevents click event from firing multiple times!
            event.preventDefault();
            var page = $(this).attr("id");
            deletePage(page);
        });
        $(".rename_button").unbind().click(function(event){ //Unbind() prevents click event from firing multiple times!
            event.preventDefault();
            var page = $(this).attr("id");
            renamePage(page);
        });
        $("#create_new_page").unbind().click(function(event){ //Unbind() prevents click event from firing multiple times!
            event.preventDefault();
            createPage();
        });
        $(".page_link").unbind().click(function(event){
            event.preventDefault();
            current_page = $(this).attr("id");
            current_txt = current_page.replace(".php", ".txt");
            if ($(window).width() < 800 || $(window).height() > $(window).width()){
                $("#your_pages").hide();
                $("#editor").show();
                $("html, body").animate({ scrollTop: $(document).height() }, 1000);
            } else if ($(window).width() > 800 || $(window).height() > $(window).width()){
                $("#editor").show();
            }
            editing = true;
            $(".page_link").css("color", "blue");
            $(".page_link").css("font-weight", "normal");
            $(".page_link").css("font-style", "normal");
            $(this).css("color", "#6f42c1");
            $(this).css("font-style", "italic");
            $(this).css("font-weight", "bold");
            $("#page_text_loading").show();
            $.get("/pages_text/" + current_txt, function(data, status){
                var content = data;
                toggleEditMode("enable");
                $("#page_editor").val(content);
                $("#page_text_loading").hide();
            });
            $("#current_page").html("<b>Currently editing: </b>" + current_page);
        });
        $("#sortable").sortable();
        $("#sortable").sortable({
            stop: function(event, ui){
                var sortArray = $("#sortable").sortable("toArray");
                var array_file = JSON.stringify(sortArray);
                console.log(sortArray);
                sendSortViaAjax(array_file);
            }
        });
        globalSortArray = $("#sortable").sortable("toArray");
        console.log(globalSortArray);
    });
}

$(".back_to_list").click(function(event){
    event.preventDefault();
    $("#editor").hide();
    $("#your_pages").show();
    $(".page_link").css("color", "blue");
    $(".page_link").css("font-weight", "normal");
    $(".page_link").css("font-style", "normal");
    $("html, body").animate({ scrollTop: 0 }, 1000);
    toggleEditMode("disable");
});

$(window).resize(function(){
    if ($(window).width() > 800){
        $("#your_pages").show();
    }
    if (editing == true && $(window).width() < 800){
        $("#your_pages").hide();
        $("#editor").show();
    }
});

loadNav();

function deletePage(page){
    if (!no_edit.includes(page)){
        if (!confirm("Are you sure you want to delete " + page + "?")){
            console.log("Delete request cancelled.");
        } else {
                $.ajax({
                url: "editor.php",
                data:{
                    request: "delete",
                    page: page
                },
                type: "POST",
                error: function(xhr){
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                },
                success: function(data){
                    console.log("Deleting " + page + "...");
                    deleteItemInSortArray(page);
                    loadNav();
                    if (current_page == page) {
                        toggleEditMode("disable");
                    }
                }
            });
        }
    } else {
        alert("You cannot delete the " + page + " page!");
    }
}

function deleteItemInSortArray(page){
    var arrayIndex = globalSortArray.indexOf(page);
    globalSortArray.splice(arrayIndex, 1);
    var array_file = JSON.stringify(globalSortArray);
    sendSortViaAjax(array_file);
}

function doesFilenameExistAlready(name){
    if (globalSortArray.includes(name)){
        return true;
    } else {
        return false;
    }
}

function renamePage(page){
    if (!no_edit.includes(page)){
        var prompt = window.prompt("What would you like to rename your page?");
        var name = prompt.toLowerCase() + ".php";
        console.log(name);
        if (prompt == null){
            console.log("Page rename cancelled.");
        } else if (prompt == "") {
            alert("New name must not be blank!");
        } else if (prompt.includes(".")) {
            alert("New name cannot include a period!");
        } else if (doesFilenameExistAlready(name) == true){
            alert("The name " + name + " already exists!");
        } else if (prompt.length > 18){
            alert("Names cannot be more than 18 characters long!");
        } else {
                $.ajax({
                url: "editor.php",
                data:{
                    request: "rename",
                    page: page,
                    new_name: name
                },
                type: "POST",
                error: function(xhr){
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                },
                success: function(data){
                    console.log("Renaming " + page + "...");
                    loadNav();
                    updateItemInSortArray(page, name);
                }
            });
        }
    } else {
        alert("You can't rename the " + page + " page!");
    }
}

function createPage(){
    var prompt = window.prompt("What would you like to call your new page?");
    var name = prompt.toLowerCase() + ".php";
    if (prompt == null){
        console.log("Page creation cancelled.");
    } else if (doesFilenameExistAlready(name) == true){
        alert("The name " + name + " already exists!");
    } else if (prompt == ""){
        alert("Page name cannot be blank!");
    } else if (prompt.includes(".")) {
        alert("Name cannot include a period!");
    } else if (prompt.length > 18){
        alert("Names cannot be more than 18 characters long!");
    } else if (prompt != null) {
        $.ajax({
            url: "editor.php",
            data:{
                request: "create",
                name: name
            },
            type: "POST",
            error: function(xhr){
                alert("An error occured: " + xhr.status + " " + xhr.statusText);
            },
            success: function(data){
                console.log("Renaming " + name + "...");
                globalSortArray.push(name);
                var array_file = JSON.stringify(globalSortArray);
                sendSortViaAjax(array_file);
                loadNav();
            }
        });
    }
}

function updateItemInSortArray(page, name){
    var arrayIndex = globalSortArray.indexOf(page);
    globalSortArray[arrayIndex] = name;
    console.log(globalSortArray[arrayIndex]);
    var array_file = JSON.stringify(globalSortArray);
    sendSortViaAjax(array_file);
}


//Editor code:

$("form").submit(function(){
    saveWithAjax();
});

function saveWithAjax(){
    var new_content = $("#page_editor").val();
            $.ajax({
            url: "editor.php",
            data:{
                request: "save_page",
                page: current_txt,
                new_content: new_content
            },
            type: "POST",
            error: function(xhr){
                alert("An error occured: " + xhr.status + " " + xhr.statusText);
            },
            success: function(data){
                alert("Changes saved!");
            }
        });
}

});

</script>





<?

//Editor code:

function savePage(){
    backupPage($_POST['page']);
    $content = $_POST['new_content'];
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages_text/" . $_POST['page'], $content);
    writeToLog($_POST['page'] . " has been saved. Check required!");
}

function backupPage($page){
    try {
        $xml = simplexml_load_file(dirname(__DIR__, 1) . "/site_info.xml");
        $time = new DateTime(null, new DateTimeZone($xml->timezone));
        $current_date = $time->format("d-M-y");
        $current_time = $time->format("h:i:s a");
    } catch (Exception $e) {
        writeToLog($e->getMessage());
    }
    $old_filename = $page;
    $contents = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $subdir . "/pages_text/" . $page);
    $file_array = scandir($_SERVER['DOCUMENT_ROOT'] . $subdir . "/admin/backups/");
    natsort($file_array);
    $file_array = array_reverse($file_array);
    if (strpos($file_array[0], ".txt") != false){
        $filename_parts = explode("@", $file_array[0]);
        $old_prefix = intval($filename_parts[0]);
        $new_prefix = $old_prefix + 1;
        $new_filename = $new_prefix . "@" . str_replace(".txt", "", $page) . " - " .  $current_date . "-" .   $current_time . ".txt";
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . $subdir . "/admin/backups/" . $new_filename, $contents);
    } else {
        $new_filename = "0@" . str_replace(".txt", "", $page) . " - " .  date('d-M-y') . "-" .   date('h:i:s a') . ".txt";
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . $subdir . "/admin/backups/" . $new_filename, $contents);
    }
}

?>