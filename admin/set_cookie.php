<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    setcookie("tracking_block", "1", time() + (86400 * 30), "/");
    echo "Visits from this browser won't be tracked for 30 days, or until you clear cookies.";
}

?>