<?php

parse_str($_POST["data"]);

if ($_SERVER['REQUEST_METHOD'] === "POST"){
    $up_one_level = dirname(__DIR__, 2) . "/";
    require $up_one_level . "user.php";

    if ($security_q_1 === "" || $security_q_2 === "" || $security_q_2 === "" || $security_a_1 === "" || $security_a_2 === "" || $security_a_2 === "") {
        die("All secret questions and answers must be filled out.");
    }

    if (password_verify($password, $hash)){

//Make confirmation dialog to change secret questions and password!
//Secret questions cannot be empty!
//Make the entire page return echos via AJAX.

        $data = '<?xml version="1.0"?>' . 
        "<questions>" .
        "<q1>" . $security_q_1 . "</q1>" .
        "<a1>" . password_hash($security_a_1, PASSWORD_DEFAULT) . "</a1>" .
        "<q2>" . $security_q_2 . "</q2>" .
        "<a2>" . password_hash($security_a_2, PASSWORD_DEFAULT) . "</a2>" .
        "<q3>" . $security_q_3 . "</q3>" .
        "<a3>" . password_hash($security_a_3, PASSWORD_DEFAULT) . "</a3>" .
        "</questions>";


        file_put_contents($up_one_level . "secret_questions.xml", $data);
        echo "Your secret questions have been updated.";
    } else {
        echo "Password incorrect";
    }
} else {
    die("Permission denied.");
}



?>