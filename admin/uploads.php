<?php
session_start();
if (empty($_SESSION['success']) || $_SESSION['success'] != 1){
  die("<div style='display:block;margin:auto;text-align:center;width:100%;margin-top:30vh;font-size:1.2em;'>You are not logged in. Click <a href='index.php'>here</a> to go to login page.</div>");
}
?>

<div class="grid-flex">

<div id="uploader" class="grid-item" style="flex-grow: 40;">

<h2>Upload a file</h2>

<div id="uploader_container">

<form action="uploader.php" method="post" enctype="multipart/form-data" id="file_form">
  Select file to upload:
  <input type="file" name="fileToUpload" id="fileToUpload" style='margin-top:10px;'>
  <input type="submit" value="Upload File" name="submit" id="upload_submit_button" style='margin-top:20px;'>
</form>

</div>
<br>
<div id="percent_complete">Upload status: N/A</div>

</div>











<div id="uploads_viewer" class="grid-item" style="flex-grow: 60;">

<h2>Your uploads</h2>
<div id="upload_viewer_container">

<?php

function formatSize($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
      $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

$file_array = scandir($_SERVER['DOCUMENT_ROOT'] . $subdir . "/uploads/");

$file_count = 0;

$images = ["jpg", "jpeg", "png", "gif", "bmp", "tiff"];

foreach ($file_array as $file){
  if ($file != "." && $file != ".." && $file != ".DS_Store"){
    $file_count++;
    $size = $_SERVER['DOCUMENT_ROOT'] . $subdir . "/uploads/" . $file;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $title = str_replace("." . $ext, "", $file);
    $info = "Name: " . $file . "\nExtension: " . $ext . "\nSize: " . formatSize(filesize($size));
    echo "<a href='/uploads/" . $file . "' class='file_preview_link' target='blank'>" .
    substr($title, 0, 25); //Truncate long filenames.
    if (strlen($title) >= 25){
      echo "..";
    }
    echo "." . $ext;
    echo "</a>";
    echo "<div class='uploaded_buttons'>" . 
    " <button class='upload_delete_button' id='" . $file . "'>Delete</button>";
    if (in_array($ext, $images)){
      echo "<button class='embed_button_image' id='" . $file . "'>Get Embed Code</button>";
    } else {
      echo "<button class='embed_button_file' id='" . $file . "'>Get Button Code</button>";
    }
    echo "<button class='file_info' id='" . $info . "'>Info</button></div><br>"; 
  }
}
if ($file_count == 0){
  echo "Nothing uploaded yet.";
}

?>

</div>

</div>



</div>


<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    switch ($_POST['request']){
        case "delete":
            deleteUpload();
            break;
    }
}

function deleteUpload(){
  $file_to_delete = $_POST['upload'];
  unlink($_SERVER['DOCUMENT_ROOT'] . $subdir . "/uploads/" . $file_to_delete);
}

?>


<script>

$(document).ready(function(){

$(".file_info").click(function(){
  var id = $(this).attr("id");
  alert(id);
});

$("#file_form").submit(function(e) {
    e.preventDefault();    
    $("#upload_submit_button").prop("disabled", true);
    var formData = new FormData(this);
    $.ajax({
          xhr: function() {
        var xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(evt) {
            if (evt.lengthComputable) {
                var percentComplete = (evt.loaded / evt.total) * 100;
                $("#percent_complete").text("Upload status: " + Math.round(percentComplete) + "% complete");
            }
            if (percentComplete == 100) {
              $("#percent_complete").text("Upload status: 100% complete. Please wait...");
            }
       }, false);
       return xhr;
    },
        url: "file_uploader.php",
        type: 'POST',
        data: formData,
        success: function (data) {
            alert(data);
            $("#main").load("uploads.php");
            $("#upload_submit_button").prop("disabled", false);
        },
        cache: false,
        contentType: false,
        processData: false
    });
});

$(".embed_button_image").click(function(){
  var id = $(this).attr('id');
  alert("Copy this code and paste it anywhere on your page to create an image:" + "   <!--Image Embed Code Begins--><a href='../uploads/" + id + "' target='blank' class='image_href'><img src='../uploads/" + id + "' class='page_image' id='" + id + "'></a><!--Image Embed Code Ends-->");
});

$(".embed_button_file").click(function(){
  var id = $(this).attr('id');
  alert("Copy this code and paste it anywhere on your page to create an image:" + "   <!--Button Embed Code Begins--><a href='/download.php?file=" + id + "' target='blank' class='file_href'><button class='download_button'>Download " + id + "</button></a><!--Button Embed Code Ends-->");
});

$(".upload_delete_button").click(function(){
if (confirm("Are you sure you want to delete this file?")){
      var id = $(this).attr("id");
      $.ajax({
        url: "uploads.php",
        data:{
            request: "delete",
            upload: id,
        },
        type: "POST",
        error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        },
        success: function(data){
            alert("Upload successfully deleted.");
            $("#main").load("uploads.php"); //Best way to do it!
        }
      });
    }
  });


});

</script>


