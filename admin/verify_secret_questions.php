<?php

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

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['g-recaptcha-response'])){

    $api_url = "https://www.google.com/recaptcha/api/siteverify?" . 
    "secret=" . "6Lcj4wIaAAAAANy4EpzBHLZFnrm9xPgE5pznH_WH" .
    "&response=" . htmlspecialchars($_POST['g-recaptcha-response']);
    $json = json_decode(file_get_contents($api_url));
    $admin = simplexml_load_file("admin_settings.xml");
    $captcha_en = strtolower($admin->captcha_enabled);
    if ($json->success != true && $captcha_en === "true"){
        session_start();
        $_SESSION['captcha_error'] = true;
        header('Location: forgot_password.php');
    } else {
        $xml = simplexml_load_file(dirname(__DIR__, 2) . "/" . "secret_questions.xml");
        $a1 = htmlspecialchars($_POST['security_a_1']);
        $a2 = htmlspecialchars($_POST['security_a_2']);
        $a3 = htmlspecialchars($_POST['security_a_3']);
        $new_password = $_POST['new_password'];
        if (password_verify($a1, $xml->a1) && password_verify($a2, $xml->a2) && password_verify($a3, $xml->a3) && passwordRuleCheck($new_password)){
            $new_contents = "<?php \$hash = '" . password_hash($new_password, PASSWORD_DEFAULT) . "'; ?>";
            file_put_contents(dirname(__DIR__, 2) . "/user.php", $new_contents);
            echo "Your password has been changed successfully. You may now log in.<br><br>";
            echo "<a href='index.php'>Back to Login</button></a>";
        } elseif (password_verify($a1, $xml->a1) && password_verify($a2, $xml->a2) && password_verify($a3, $xml->a3) && !passwordRuleCheck($new_password)){
            session_start();
            $_SESSION['password_error'] = true;
            header("Location: forgot_password.php");
        } elseif (!password_verify($a1, $xml->a1) || !password_verify($a2, $xml->a2) || !password_verify($a3, $xml->a3)){
            session_start();
            $_SESSION['questions_error'] = true;
            header("Location: forgot_password.php");
        }
    }
}

?>