<!DOCTYPE HTML>

<head>

<?php 
$site_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml");

if (strlen($site_xml->userstack_key) > 5 && strlen($site_xml->ipstack_key) > 5){
    require "../tracking/tracking.php";
}
?>

<noscript>
<p id="javascript_warning">JavaScript must be enabled for this application to run!</p> 
</noscript>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="icons/favicon-16x16.png">
<link rel="manifest" href="icons/site.webmanifest">

<script   src="https://code.jquery.com/jquery-3.5.1.js"   integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="   crossorigin="anonymous"></script>

<script   src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"   integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="   crossorigin="anonymous"></script>

<link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed:500,700" rel="stylesheet" type="text/css"/>
<link href="https://fonts.googleapis.com/css?family=Muli:400,400i,800,800i" rel="stylesheet" type="text/css"/>

<link href="styles.css" rel="stylesheet" type="text/css"/>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<title>MyPage CMS</title>

</head>

<body>

<?php
session_start();

$up_one_level = dirname(__DIR__, 2) . "/";

if (!file_exists($up_one_level . "user.php") || !file_exists($up_one_level . "secret_questions.xml")){
  header("Location: setup.php");
  die ("Entering setup mode...");
}

//Need to sanitize!!!
//Auth.php should return to this page on fail with get parameters, and this page should check for them on all loads.
//Save passwords as json (protected), load into array, check if there.
?>

<div id="login_contain">

<h1 id="login_title">MyPage CMS (Beta 2)</h1>

</div>

<div id="login">

</h2>Enter the password to log in:</h2>

<form action="auth.php" method="POST" id="login_form">
  <input type="password" id="password" name="password"><br><br>
  <input type="text" name="h17" style="display: none;" val="h17">
  <div class="g-recaptcha" style="display:block;margin-left:5px;" data-sitekey="6Lcj4wIaAAAAAAxHUZZZdy_NmkriZkJqZ8NqBU4p" data-callback='onSubmit'></div>
</form>
<br>

</div>

<button id="login_submit">Submit</button>
<br><br>
<div style="text-align:center;display:block;margin:auto;font-style:italic;">
<a href="forgot_password.php">Forgot Password</a>
</div>

</div>

<script>

$(document).ready(function(){

$(document).on('keypress',function(e) {
    if(e.which == 13) {
        $("#login_form").submit();
    }
});

$("#login_submit").click(function(){
    $("#login_form").submit();
});

});

</script>

<?php

if (isset($_SESSION['password_error']) && $_SESSION['password_error'] === true){
  echo("<div class='error'>Password is incorrect!</div>");
}
if (isset($_SESSION['captcha_error']) && $_SESSION['captcha_error'] === true){
  echo("<div class='error'>You must complete the captcha!</div>");
}

session_start();
unset($_SESSION['password_error']);
unset($_SESSION['captcha_error']);

?>

</body>

</html>