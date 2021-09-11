<!DOCTYPE HTML>

<head>

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

<div id="setup">

<?php

if (!is_writable(dirname(__DIR__, 2))){
  die("<div style='padding-top:10vh;width:80vh;display:block;margin:auto;text-align:justify;'><h1 style='text-align:center;display:block;margin:auto;'>Error</h1>MyPage CMS can't write outside your document root. As this application stores your secure passwords and secret questions/answers up one level from your server's document root, you will need to change the permissions on the folder that contains your public_html or www folder to allow all users to write to it before the program can run. This is annoying, I know, but it's necessary to store your confidential information there, where it is less likely to be accessible to hackers. If you need to help, please email me at <a href='mailto:john.micallef16@gmail.com'>john.micallef16@gmail.com</a> and I will get back to you within 24 hours. I would be more than happy to talk you through it. More information about this error can be found at <a href='http://www.MyPage-cms.com/documentation.php' target='_blank'>www.MyPage-cms.com/documentation.php</a></div>");
}


if (file_exists("setup_complete.php")){
  die("<div style='padding-top:10vh;width:80vh;display:block;margin:auto;text-align:center;'>This website has already been set up. Please delete setup_complete.php in the admin folder to run setup again.</div>");
}

?>

<h2 class="settings_subheading">MyPage CMS Setup</h2>
<p style="margin-top:25px;text-align:justify;">Welcome to MyPage CMS, an easy-to-use content management system for your website. To get started, please set your content management system's password here. Your password must include at least one upper case letter, one lower case letter, one number, and be a minimum of eight characters long.</p>

<form action="create_password_and_questions.php" method="POST" id="setup_form" autocomplete="off"><br>

<label for="password">New password:</label>
  <input type="password" id="password" name="password" autocomplete="off" autocomplete="chrome-off"><br><br><br><br>
<label for="password_confirm">Confirm your password:</label>
  <input type="password" id="password_confirm" name="password_confirm" autocomplete="off" autocomplete="chrome-off"><br><br><br><br>

<p style="margin-top:0px;text-align:justify;">Please set three secret questions here. These will be used to reset your password if you forget it. These questions and answers should be treated like passwords. Try not to make them obvious, as, if you don't, literally anyone will be able to change your content management system's password. If someone can easily answer all three of these questions, then anyone will be able to hack your website. You must be able to recall the EXACT answer to these questions, down to the case of the lettering and the punctation, so be careful!</p><br>

<label for="security_q_1">Security question 1:</label>
  <input type="text" id="security_q_1" name="security_q_1"><br><br><br><br>
<label for="security_a_1">New security question answer:</label>
  <input type="text" id="security_a_1" name="security_a_1" autocomplete="off" autocomplete="chrome-off"><br><br><br><br>

<label for="security_q_2">Security question 2:</label>
  <input type="text" id="security_q_2" name="security_q_2"><br><br><br><br>
<label for="security_a_2">New security question answer:</label>
  <input type="text" id="security_a_2" name="security_a_2" autocomplete="off" autocomplete="chrome-off"><br><br><br><br>

<label for="security_q_3">Security question 3:</label>
  <input type="text" id="security_q_3" name="security_q_3"><br><br><br><br>
<label for="security_a_3">New security question answer:</label>
  <input type="text" id="security_a_3" name="security_a_3" autocomplete="off" autocomplete="chrome-off"><br><br><br><br>

  <input type="submit" id="submit" value="Submit">
</form>

</div>


</div>

<br><br><br><br><br>

<script>

$(document).ready(function(){

$(document).on('keypress',function(e) {
    if(e.which == 13) {
        $("#setup_form").submit();
    }
});

$("#submit").click(function(event){
  event.preventDefault();
    var data = $("#setup_form").serialize();
        $.ajax({
        url: "create_password_and_questions.php",
        data:{
            data: data
        },
        type: "POST",
        error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        },
        success: function(data){
            alert(data);
            if (data === "Your password and secret questions have been set! Please continue to the settings page in your new CMS to complete setup!"){
              window.location.href = "index.php";
            }
        }
    });
});

});

</script>

</body>

</html>