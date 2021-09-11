<?php

function getIP(){
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = $_SERVER['HTTP_CLIENT_IP'];
    $forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];
    if (filter_var($client, FILTER_VALIDATE_IP)){
        $ip = $client;
    } elseif(filter_var($forward, FILTER_VALIDATE_IP)){
        $ip = $forward;
    } else {
        $ip = $remote;
    }
    return $ip;
}

function logActivity($type){
    $xml = simplexml_load_file(dirname(__DIR__, 1) . "/site_info.xml");
    $time = new DateTime(null, new DateTimeZone($xml->timezone)); //Doesn't work if declared globally, apparently.
    $current_time = $time->format('d/m/Y h:i:s A');
    if (file_exists("activity.txt")){
        fopen("activity.txt", "r+");
    } else {
        file_put_contents("activity.txt", "");
    }
    $file = fopen("activity.txt", "r+");
    $existing = file_get_contents("activity.txt");
    switch ($type) {
        case "login":
            fwrite($file, "Login from " . getIP() . " at " . $current_time . "\n");
            fwrite($file, $existing);
            break;
        case "failed":
            fwrite($file, "Failed login from " . getIP() . " at " . $current_time . "\n");
            fwrite($file, $existing);
            break;
    }
    fclose($file);
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['password']) && isset($_POST['h17']) && isset($_POST['g-recaptcha-response'])){

    $api_url = "https://www.google.com/recaptcha/api/siteverify?" . 
    "secret=" . "6Lcj4wIaAAAAANy4EpzBHLZFnrm9xPgE5pznH_WH" .
    "&response=" . htmlspecialchars($_POST['g-recaptcha-response']);
    $json = json_decode(file_get_contents($api_url));
    $admin = simplexml_load_file("admin_settings.xml");
    $captcha_en = strtolower($admin->captcha_enabled);
    if ($json->success != true && $captcha_en === "true"){
        session_start();
        $_SESSION['captcha_error'] = true;
        header('Location: index.php');
    } else {
        $password = $_POST['password'];
        $up_one_level = dirname(__DIR__, 2) . "/";
        require dirname(__DIR__, 2) . "/user.php";
        if (password_verify($password, $hash)){
            logActivity("login");
            session_start();
            $_SESSION['success'] = 1;
            header('Location: application.php');
        } else {
            logActivity("failed");
            session_start();
            $_SESSION['success'] = 0;
            session_start();
            $_SESSION['password_error'] = TRUE;
            header('Location: index.php');
        }

    }

}

?>