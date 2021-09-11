<?php

parse_str($_POST["data"]);

if ($_SERVER['REQUEST_METHOD'] === "POST"){

if (!file_exists(dirname(__DIR__, 2) . "/user.php")){
    file_put_contents(dirname(__DIR__, 2) . "/user.php", "Ready.");
}
if (!file_exists(dirname(__DIR__, 2) . "/secret_questions.xml")){
    file_put_contents(dirname(__DIR__, 2) . "/secret_questions.xml", "<?xml version='1.0'?>");
}

function passwordRuleCheck($password){
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    if(!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
        return false;
    } else {
        return true;
    }
}

if (empty($password) || empty($password_confirm)){
    die("Both password fields must be completed.");
} elseif ($password != $password_confirm){
    die("Passwords don't match.");
} elseif (!passwordRuleCheck($password)){
    die("Your password must include at least one upper case letter, one lower case letter, one number, and be a minimum of eight characters long.");
} else {
    $new_contents = "<?php \$hash = '" . password_hash($password, PASSWORD_DEFAULT) . "'; ?>";
    file_put_contents(dirname(__DIR__, 2) . "/user.php", $password . $new_contents);
}

if ($security_q_1 == "" || $security_q_2 == "" || $security_q_2 == "" || $security_a_1 == "" || $security_a_2 == "" || $security_a_2 == "") {
    die("All secret questions and answers must be filled out.");
}

$data = '<?xml version="1.0"?>' . 
"<questions>" .
"<q1>" . $security_q_1 . "</q1>" .
"<a1>" . password_hash($security_a_1, PASSWORD_DEFAULT) . "</a1>" .
"<q2>" . $security_q_2 . "</q2>" .
"<a2>" . password_hash($security_a_2, PASSWORD_DEFAULT) . "</a2>" .
"<q3>" . $security_q_3 . "</q3>" .
"<a3>" . password_hash($security_a_3, PASSWORD_DEFAULT) . "</a3>" .
"</questions>";

file_put_contents(dirname(__DIR__, 2) . "/secret_questions.xml", $data);

echo "Your password and secret questions have been set! Please continue to the settings page in your new CMS to complete setup!";
file_put_contents("setup_complete.php", "This file is here to tell the system not to allow setup.php to be executed again. Delete this file and try to access the CMS again to access the setup screen again.");

} else {
    die("Permission denied.");
}

?>