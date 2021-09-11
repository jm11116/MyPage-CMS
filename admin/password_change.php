<?php

if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['old_password']) && empty($_POST['new_password'])){
    echo "Both fields must be filled out.";
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

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['old_password']) && isset($_POST['new_password']) && !empty($_POST['old_password']) && !empty($_POST['new_password']) && passwordRuleCheck($_POST['new_password']) === true){
    changePassword();
} elseif ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['old_password']) && isset($_POST['new_password']) && !empty($_POST['old_password']) && !empty($_POST['new_password']) && passwordRuleCheck($_POST['new_password']) === false){
    echo "Your password must include at least one upper case letter, one lower case letter, one number, and be a minimum of eight characters long!";
}

function changePassword(){
    require dirname(__DIR__, 2) . "/user.php";
    if (password_verify($_POST['old_password'], $hash) === TRUE){
        $new_contents = "<?php \$hash = '" . password_hash($_POST['new_password'], PASSWORD_DEFAULT) . "'; ?>";
        file_put_contents(dirname(__DIR__, 2) . "/user.php", $new_contents);
        echo "Your password has been changed successfully.";
    } else {
        echo "Old password is incorrect.";
    }
}


?>