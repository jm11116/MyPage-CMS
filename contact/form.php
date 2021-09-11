<? session_start(); ?>

<?
if (empty($_SESSION["submitted"])){
    $_SESSION["name"] = "";
    $_SESSION["email"] = "";
    $_SESSION["message"] = "";
}
?>

<p class="contact_error" style="text-align:center;margin-top:25px;margin-bottom:25px;color:red;font-size:1em;">
<?

if ($_SERVER['QUERY_STRING'] && $_SESSION["submitted"] === true){
    decodeQuery();
}

function decodeQuery(){
    parse_str($_SERVER['QUERY_STRING']); //Makes variables from the key of query, so '?empty=' will become $empty, and it will hold the value.
        if (isset($empty)){
            if ($emptyNum == 1){
                echo ucfirst($empty) . ' field cannot be blank!<br>';
            } else
            echo ucfirst($empty) . ' fields cannot be blank!<br>';
        }
        if ($email == "invalid"){
            echo "Email is invalid!";
        }
    }

$_SESSION["submitted"] = false;

if (isset($_SESSION['captcha_error']) && $_SESSION['captcha_error'] === true){
  echo "You must complete the captcha!";
  unset($_SESSION['captcha_error']);
}

?>
</p>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<form action="../contact/mailer.php" method="post" id="contact_form">

<label for="name">Your name: </label><br>
<input type="text" style="width:100%;" name="name" value="<? echo $_SESSION["name"]; ?>" autocomplete="off"></input><br><br>

<label for="name">Your email: </label><br>
<input type="text" style="width:100%;" name="email" value="<? echo $_SESSION["email"]; ?>" autocomplete="off"></input><br><br>

<label for="name">Your message: </label><br>
<textarea name="message" style="width:100%;" cols="50" rows="10"><? echo $_SESSION["message"]; ?></textarea><br>
<br>
<div class="g-recaptcha" style="display:inline-block;margin:auto;text-align:center;margin-left:5px;" data-sitekey="6Lcj4wIaAAAAAAxHUZZZdy_NmkriZkJqZ8NqBU4p" data-callback='onSubmit'></div>

<center><button type="submit">Submit</button></center>

</form>