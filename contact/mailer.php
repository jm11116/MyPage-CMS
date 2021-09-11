<?php

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $admin = simplexml_load_file(dirname(__DIR__, 1) . "/admin/admin_settings.xml");
    $captcha_en = strtolower($admin->captcha_enabled);

    if (isset($_POST['g-recaptcha-response']) && $captcha_en === "true"){
        $api_url = "https://www.google.com/recaptcha/api/siteverify?" . 
        "secret=" . "6Lcj4wIaAAAAANy4EpzBHLZFnrm9xPgE5pznH_WH" .
        "&response=" . htmlspecialchars($_POST['g-recaptcha-response']);
        $json = json_decode(file_get_contents($api_url));
    } elseif (empty($_POST['g-recaptcha-response']) && $captcha_en === "true"){
        session_start();
        $_SESSION['captcha_error'] = true;
        header('Location: /pages/contact.php');
    }

    if ($json->success === true){

        function getUserIP() {
            if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            }
            $client  = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $remote  = $_SERVER['REMOTE_ADDR'];
            if(filter_var($client, FILTER_VALIDATE_IP)) {
                $ip = $client;
            } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
                $ip = $forward;
            } else {
                $ip = $remote;
            } return $ip;
        }

        $ip = getUserIP();

        //Mail will only send if error count by end of script is zero.
        $error_count = 1;

        //Get information from form:
        $name = stripslashes(htmlspecialchars($_POST['name']));
        $email_raw = stripslashes(htmlspecialchars($_POST['email']));
        $email = filter_var($email_raw, FILTER_SANITIZE_EMAIL);
        $message = stripslashes(htmlspecialchars($_POST['message']));

        //Set session variables so form content doesn't get wiped:
        session_start();
        $_SESSION["name"] = $name;
        $_SESSION["email"] = $email;
        $_SESSION["message"] = $message;

        $empty_count = 0;
        $empty_result = null;
        $empty_array = [];

        //Is anything empty?
        if (empty($name)){
            array_push($empty_array, "name");
            $empty_count++;
        }
        if (empty($email)){
            array_push($empty_array, "email");
            $empty_count++;
        }
        if (empty($message)){
            array_push($empty_array, "message");
            $empty_count++;
        }

        //Email valid?
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_valid = 1;
        } else {
            $email_valid = 0;
        }

        //Put all the errors into a string to send in a query string back to the form:
        if ($empty_count > 0){
            for ($i = 0; $i <= $empty_count; $i++){
                if ($i == ($empty_count - 1) && $empty_count > 1) {
                    $empty_result .= ' and ' . $empty_array[$i];
                } elseif ($i == $empty_count){ //End of loop.
                    if (!empty($email) && $email_valid == 1){
                        header('Location: ../pages/contact.php?empty=' . $empty_result . '&emptyNum=' .$empty_count . "#error");
                    } elseif (!empty($email) && $email_valid == 0){
                        header('Location: ../pages/contact.php?email=invalid&empty=' . $empty_result . '&emptyNum=' .$empty_count . "#error");
                    } elseif (empty($email)){
                        header('Location: ../pages/contact.php?empty=' . $empty_result . '&emptyNum=' .$empty_count . "#error");
                    }
                } elseif ($empty_count == 1 || $empty_count == 2){
                    $empty_result .= $empty_array[$i];
                } else {
                    $empty_result .= $empty_array[$i] . ', ';
                }
            }
        } elseif ($empty_count == 0 && $email_valid == 0){
            header('Location: ../pages/contact.php?email=invalid#error');
        } else {
            //Gather information
            $site_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . $subdir . "/site_info.xml");
            $return_page = "../pages/contact.php";
            date_default_timezone_set($site_xml->timezone);
            $to_email = $site_xml->email;
            $from = "mail@" . $admin->domain_name;
            $spam_from = "spam@" . $admin->domain_name;
            $date = date('D d M Y');
            $time =  date('h:i a');
            $ip_info_link = "https://whatismyipaddress.com/ip/" . $ip;

            //Collate information:
            $subject = "Message from ".$admin->domain_name." contact form";

            $message_data = "Name: ".$name."\nTime: ".$time."\nDate: ".$date."\nEmail: ".$email."\nIP Address: ".$ip."\nGet IP Information: ".$ip_info_link."\nMessage: ".$message;

            //Clear session variables:
            $_SESSION["name"] = "";
            $_SESSION["email"] = "";
            $_SESSION["message"] = "";

            //Check against spam list:
            $keywords = explode("\n", file_get_contents("blocklist.txt"));
            $raw_msg = strtolower($message);
            $bad_words = [];
            foreach ($keywords as $keyword) {
                if (strpos($raw_msg, $keyword) != false){
                    array_push($bad_words, $keyword);
                }
            }
            if (count($bad_words) > 3){
                mail($to_email, $subject, $message_data, 'From:'.$spam_from);
            } else {
                mail($to_email, $subject, $message_data, 'From:'.$from);
            }

            echo "Your message has been sent!<br>";
            echo "<a href='".$return_page."'><button>Back</button></a>";
            }

            $_SESSION["submitted"] = true;

    } else {
        session_start();
        $_SESSION['captcha_error'] = true;
        header('Location: /pages/contact.php');
    }
}

?>