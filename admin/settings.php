<?php
session_start();
if (empty($_SESSION['success']) || $_SESSION['success'] != 1){
  die("<div style='display:block;margin:auto;text-align:center;width:100%;margin-top:30vh;font-size:1.2em;'>You are not logged in. Click <a href='index.php'>here</a> to go to login page.</div>");
}
?>

<?php
if (file_exists($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml")){
  $site_info = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml");
} else {
  $xml_file = fopen($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml", "w");
  fclose($xml_file);
}
?>

<div class="grid-flex">

<div id="site_settings" class="grid-item" style="flex-grow: 50;">

<h2>Site Settings</h2><br>

<form  method="POST" autocomplete="off" id="settings_form">
<label for="fname">Site name:</label>
  <input type="text" id="sitename" name="sitename" value="<?php echo $site_info->sitename; ?>"><br><br><br>
<label for="fname">Tagline:</label>
  <input type="text" id="tagline" name="tagline" value="<?php echo $site_info->tagline; ?>"><br><br><br>
<label for="fname">Website keywords (separated by commas):</label>
  <input type="text" id="keywords" name="keywords" value="<?php echo $site_info->keywords; ?>"><br><br><br>
<label for="fname">Owner name:</label>
  <input type="text" id="owner" name="owner" value="<?php echo $site_info->owner; ?>"><br><br><br>
<label for="fname">Description:</label>
  <input type="text" id="title_descript" name="title_descript" value="<?php echo $site_info->title_descript; ?>"><br><br><br>
<label for="fname">Footer text:</label>
  <input type="text" id="company" name="company" value="<?php echo $site_info->company; ?>"><br><br><br>
<label for="fname">Theme:</label>
  <input type="text" id="theme" name="theme" value="<?php echo $site_info->theme; ?>" readonly="readonly" style="background-color:#f5f5f5;"><br><br><br>
<label for="fname">Contact form email:</label>
  <input type="text" id="email" name="email" value="<?php echo $site_info->email; ?>"><br><br><br>
<label for="fname">Pages hidden in nav (separated by commas (e.g. index.php, contact.php):</label>
  <input type="text" id="hidden" name="hidden" value="<?php echo $site_info->hidden; ?>"><br><br><br>
<label for="fname">Footer code injection (HTML, JS, CSS & PHP allowed):</label>
  <textarea id="code" name="code"><?php echo file_get_contents("code.php"); ?></textarea><br><br>
<div class="checkbox_div">
<label for="site_enabled">Website enabled:</label><br>
<?php 
if ($site_info->site_enabled == "enabled"){
  echo '<input type="checkbox" name="site_enabled" checked><br><br><br>';
} else {
  echo '<input type="checkbox" name="site_enabled"><br><br><br>';
}
?>
</div>
<div class="checkbox_div">
<label for="contact_enabled">Contact form enabled:</label><br>
<?php 
if ($site_info->contact_enabled == "true"){
  echo '<input type="checkbox" name="contact_enabled" checked><br><br><br>';
} else {
  echo '<input type="checkbox" name="contact_enabled"><br><br><br>';
}
?>
</div>
<div class="checkbox_div">
<label for="contact_enabled">Blog enabled:</label><br>
<?php
if ($site_info->blog_enabled == "true"){
  echo '<input type="checkbox" name="blog_enabled" checked><br><br><br>';
} else {
  echo '<input type="checkbox" name="blog_enabled"><br><br><br>';
}
?>
</div>
<br>
  <input type="submit" id="main_settings_submit" value="Submit">

<br><br>
<h2 class="settings_subheading">Tracking Settings</h2>
<br>

<?php
$site_xml = simplexml_load_file(dirname(__DIR__, 1) . "/site_info.xml");
if (strlen($site_xml->userstack_key) < 5 || strlen($site_xml->ipstack_key) < 5){
  echo '<p style="color:red;font-style:italic;margin-top:-15px;">Tracking functionality is currently disabled. Please get your free Userstack and IPStack keys and enter them below to start tracking visitors.</p>';
}
?>

<label for="fname">Userstack access key:</label>
  <input type="text" id="userstack_key" name="userstack_key" value="<?php echo $site_info->userstack_key; ?>"><br><br><br><br>
<label for="fname">IPStack access key:</label>
  <input type="text" id="ipstack_key" name="ipstack_key" value="<?php echo $site_info->ipstack_key; ?>"><br><br><br><br>
<label for="fname">Timezone: (<a href="https://www.php.net/manual/en/timezones.php" target="_blank">List of timezones here</a>)</label>
  <input type="text" id="timezone" name="timezone" value="<?php echo $site_info->timezone; ?>"><br><br><br><br>
<label for="fname">Email alerts for these pages (separated by commas):</label>
  <p style="margin-top: -0em;color:grey;"><i>Type 'all' to get email alerts for all pages. Check your spam folder if emails not sending.</i></p>
  <input type="text" id="alert_pages" name="alert_pages" value="<?php echo $site_info->alert_pages; ?>"><br><br>
<label for="fname">Don't track these IPs (separated by commas):</label>
  <input type="text" id="no_track" name="no_track" value="<?php echo $site_info->no_track; ?>"><br><br><br>

<p style="font-style:italic;">Your current IP address is: <b style="color:green;">
<?php
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
    $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
$client  = $_SERVER['HTTP_CLIENT_IP'];
$forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
$remote  = $_SERVER['REMOTE_ADDR'];
if (filter_var($client, FILTER_VALIDATE_IP)){
    $ip = $client;
} elseif (filter_var($forward, FILTER_VALIDATE_IP)){
    $ip = $forward;
} else {
    $ip = $remote;
}
echo $ip;
?>
</b>
</p>
<?php 
if (!isset($_COOKIE["tracking_block"])){
  echo '<a href="javascript:void(0)" id="cookie_button" style="font-style:italic;">Set a blocking cookie in this browser for 30 days</a><br><br>';
} else {
  echo "<p style='color:grey;'><i>Blocking cookie has been set in this browser.</i></p>";
}

?>
<br>


<input type="submit" id="tracking_submit" value="Submit">

</form>

<br><br>
<h2 class="settings_subheading">Security</h2>
<p style="margin-top:25px;">Change password: </p>
<form action="password_change.php" method="POST" id="password_changer" autocomplete="off">
<label for="old_password">Old password:</label>
  <input type="password" id="old_password" name="old_password"><br><br><br><br>
<label for="new_password">New password:</label>
  <input type="password" id="new_password" name="new_password" autocomplete="off"><br><br><br><br>
  <input type="submit" value="Submit" id="#password_change_submit">
  </form>

<?php

$secret_questions = simplexml_load_file(dirname(__DIR__, 2) . "/" . "secret_questions.xml");

?>

<br><br>
<h2 class="settings_subheading">Security Questions</h2>
<p style="margin-top:25px;">Update your security questions: </p>
<form action="change_secret_questions.php" method="POST" id="secret_questions_form" autocomplete="off">
<label for="password">Enter password to make changes:</label>
  <input type="password" id="password" name="password"><br><br><br><br>
<label for="security_q_1">Security question 1:</label>
  <input type="text" id="security_q_1" name="security_q_1" value="<?php echo $secret_questions->q1; ?>"><br><br><br><br>
<label for="security_a_1">New security question answer:</label>
  <input type="password" id="security_a_1" name="security_a_1"><br><br><br><br>

<label for="security_q_2">Security question 2:</label>
  <input type="text" id="security_q_2" name="security_q_2" value="<?php echo $secret_questions->q2; ?>"><br><br><br><br>
<label for="security_a_2">New security question answer:</label>
  <input type="password" id="security_a_2" name="security_a_2"><br><br><br><br>

<label for="security_q_3">Security question 3:</label>
  <input type="text" id="security_q_3" name="security_q_3" value="<?php echo $secret_questions->q3; ?>"><br><br><br><br>
<label for="security_a_3">New security question answer:</label>
  <input type="password" id="security_a_3" name="security_a_3"><br><br><br><br>

  <input type="submit" id="secret_questions_submit" value="Submit">
</form>

<br><br>
<h2 class="settings_subheading">Troubleshooting</h2>
<i>
<div id="troubleshooting">
<a href="javascript:void(0);" id="recreate_website_nav">Recreate Website Nav</a><br>
<a href="javascript:void(0);" id="recreate_blog_nav">Recreate Blog Nav</a><br<><br>
<a href="javascript:void(0);" id="clear_site_backups">Delete All Webpage Backups</a><br>
<a href="javascript:void(0);" id="clear_blog_backups">Delete All Blog Backups</a>
</i>
</div>

<br>
<h2 class="settings_subheading">Access History</h2>
<div class="logs"> <?php echo nl2br(file_get_contents("activity.txt")); ?> </div>

</div>

<div id="themes" class="grid-item" style="flex-grow: 50;">

<h2>Themes</h2>
<p id="current_theme"><b>Current theme:</b> <?php echo $site_info->theme; ?></p>

<?php

$thumbs_array = scandir("theme_thumbs");
foreach ($thumbs_array as $thumb){
  if ($thumb[0] != "."){ //Ignore hidden UNIX files.
    $file_parts = explode(".", $thumb);
    echo "<img src='theme_thumbs/" . $thumb . "' class='theme_thumb' id='" . $file_parts[0] . "'>";
    echo "<figcaption>" . ucfirst($file_parts[0]) . " theme</figcaption><br><br>";
  }
}

?>

</div>



</div>


<script>
$(document).ready(function(){

$("#cookie_button").click(function(){
      $.ajax({
      url: "set_cookie.php",
      type: "POST",
      error: function(xhr){
          alert("An error occured: " + xhr.status + " " + xhr.statusText);
      },
      success: function(data){
          alert(data);
          $("#main").load("settings.php");
      }
  });
});

$("#clear_site_backups").click(function(){
        if (confirm("Are you sure you want to delete all website page backups? This action cannot be undone!")){
                $.ajax({
                url: "backups.php",
                data:{
                    request: "clear_all"
                },
                type: "POST",
                error: function(xhr){
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                },
                success: function(data){
                    alert("All page backups deleted.");
                }
            });
        }
    });

$("#clear_blog_backups").click(function(){
        if (confirm("Are you sure you want to delete all blog post backups? This action cannot be undone!")){
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
                    alert("All blog backups deleted.");
                }
            });
        }
    });

function recreateWebsiteNavFile(){
    if (!confirm("Are you sure you want to recreate the nav file unsorted? This may rearrange all your pages.")){
            console.log("Recreate nav request cancelled.");
    } else {
            $.ajax({
            url: "blog.php",
            data:{
                request: "recreate_nav_file"
            },
            type: "POST",
            error: function(xhr){
                alert("An error occured: " + xhr.status + " " + xhr.statusText);
            },
            success: function(data){
              alert("Your website's nav file has been recreated.");
            }
        });
    }
}

function recreateBlogNavFile(){
    if (!confirm("Are you sure you want to recreate the nav file unsorted? This may rearrange all your blog posts.")){
            console.log("Recreate nav request cancelled.");
    } else {
            $.ajax({
            url: "editor.php",
            data:{
                request: "recreate_nav_file"
            },
            type: "POST",
            error: function(xhr){
                alert("An error occured: " + xhr.status + " " + xhr.statusText);
            },
            success: function(data){
              alert("Your blog's nav file has been recreated.");
            }
        });
    }
}
$("#recreate_website_nav").click(function(event){
    event.preventDefault();
    recreateWebsiteNavFile();
});
$("#recreate_blog_nav").click(function(event){
    event.preventDefault();
    recreateBlogNavFile();
});

function sendForm(){
    var data = $("#settings_form").serialize();
        $.ajax({
        url: "write_settings.php",
        data:{
            data: data
        },
        type: "POST",
        error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        },
        success: function(data){
            alert(data);
        }
    });
}

$("#main_settings_submit").click(function(event){
    event.preventDefault();
    sendForm();
});

$("#tracking_submit").click(function(event){
  event.preventDefault();
    var data = $("#settings_form").serialize();
        $.ajax({
        url: "write_settings.php",
        data:{
            data: data
        },
        type: "POST",
        error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        },
        success: function(data){
            alert(data);
            $("#main").load("settings.php");
        }
    });
});

$("#password_changer").submit(function(event){
  event.preventDefault();
  if (confirm("Are you sure you want to change your password?")){
    var data = $("#password_changer").serialize();
        $.ajax({
        url: "password_change.php",
        data:{
            old_password: $("#old_password").val(),
            new_password: $("#new_password").val()
        },
        type: "POST",
        error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        },
        success: function(data){
            alert(data);
        }
      });
  }
});

$("#secret_questions_submit").click(function(event){
  event.preventDefault();
  if (confirm("Are you sure you want to save your secret questions settings?")){
    var data = $("#secret_questions_form").serialize();
        $.ajax({
        url: "change_secret_questions.php",
        data:{
            data: data
        },
        type: "POST",
        error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        },
        success: function(data){
            alert(data);
        }
    });
  }
});

$(".theme_thumb").click(function(){
  var id = $(this).attr("id");
  var theme = id.charAt(0).toUpperCase() + id.substring(1).replace(".png", "");
  $("#theme").val(theme);
  sendForm();
  $("#current_theme").text("Current theme: " + id.charAt(0).toUpperCase() + id.substring(1));
});

});
</script>