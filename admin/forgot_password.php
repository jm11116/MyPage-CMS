<!DOCTYPE HTML>

<head>

<?php 
$site_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml");

if (strlen($site_xml->userstack_key) > 5 && strlen($site_xml->ipstack_key) > 5){
    require "../tracking/tracking.php";
}
?>

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

</div>

<div id="login">

<?php

session_start();

if (isset($_SESSION['questions_error']) && $_SESSION['questions_error'] === true){
  echo("<div class='error'>One or more of your answers is incorrect/incomplete!</div>");
}
if (isset($_SESSION['password_error']) && $_SESSION['password_error'] === true){
  echo("<div class='error'>Your password must include at least one upper case letter, one lower case letter, one number, and be a minimum of eight characters long!</div>");
}
if (isset($_SESSION['captcha_error']) && $_SESSION['captcha_error'] === true){
  echo("<div class='error'>You must complete the captcha!</div>");
}

$secret_questions = simplexml_load_file(dirname(__DIR__, 2) . "/" . "secret_questions.xml");

//Add too many attempts thing.

?>

<h2 class="settings_subheading">Security Questions</h2>
<p style="margin-top:25px;">Please answer the following security questions to change your password: </p>
<form action="verify_secret_questions.php" method="POST" autocomplete="off" id="login_form" style="margin-top:-15px;">
<span><?php echo $secret_questions->q1; ?></span><br><br>
<label for="security_a_1">Answer:</label>
  <input type="password" id="security_a_1" name="security_a_1" autocomplete="off"><br><br><br><br>

<span><?php echo $secret_questions->q2; ?></span><br><br>
<label for="security_a_1">Answer:</label>
  <input type="password" id="security_a_2" name="security_a_2" autocomplete="off"><br><br><br><br>

<span><?php echo $secret_questions->q3; ?></span><br><br>
<label for="security_a_1">Answer:</label>
  <input type="password" id="security_a_3" name="security_a_3" autocomplete="off"><br><br><br><br>

<br>
<label for="new_password">New password:</label>
  <input type="password" id="new_password" name="new_password" autocomplete="off"><br><br><br><br>
<br>
<div class="g-recaptcha" style="display:block;margin-left:5px;" data-sitekey="6Lcj4wIaAAAAAAxHUZZZdy_NmkriZkJqZ8NqBU4p" data-callback='onSubmit'></div>
<br><br>

</form>
</div>

<button id="login_submit">Submit</button>
<br><br>
<div style="text-align:center;display:block;margin:auto;font-style:italic;">
<a href="index.php">< Back to Login</a>
</div>

<br><br><br><br><br>

<?php 
session_start();
unset($_SESSION['questions_error']);
unset($_SESSION['password_error']);
unset($_SESSION['captcha_error']);
?>

<script>

$(document).ready(function(){

$(document).on('keypress',function(e) {
    if(e.which == 13) {
        $("#login_form").submit();
    }
});

$("#login_submit").click(function(event){
    $("#login_form").submit();
    
});

});

</script>

</body>

</html>