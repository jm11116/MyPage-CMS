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

<title>MyPage CMS</title>

</head>

<body>

</div>

<div id="login">

<?php

$secret_questions = simplexml_load_file(dirname(__DIR__, 2) . "/" . "secret_questions.xml");

//Add too many attempts thing.

?>

<h2 class="settings_subheading">Security Questions</h2>
<p style="margin-top:25px;">Please answer the following security questions: </p>
<form action="verify_security_questions.php" method="POST" autocomplete="off" id="login_form" style="margin-top:-15px;">
  <input type="text" id="security_q_1" name="security_q_1" value="<?php echo $secret_questions->q1; ?>" style="border-style:none;" readonly><br><br>
<label for="sequrity_a_1">Answer:</label>
  <input type="password" id="security_a_1" name="security_a_1"><br><br><br><br>

  <input type="text" id="security_q_2" name="security_q_2" value="<?php echo $secret_questions->q2; ?>" style="border-style:none;" readonly><br><br>
<label for="sequrity_a_1">Answer:</label>
  <input type="password" id="security_a_2" name="security_a_2"><br><br><br><br>

  <input type="text" id="security_q_3" name="security_q_3" value="<?php echo $secret_questions->q3; ?>" style="border-style:none;" readonly><br><br>
<label for="sequrity_a_1">Answer:</label>
  <input type="password" id="security_a_3" name="security_a_3"><br><br><br><br>

</form>
</div>

<button id="login_submit">Submit</button>
<br><br>
<div style="text-align:center;display:block;margin:auto;font-style:italic;">
<a href="index.php">< Back to Login</a>
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

</body>

</html>