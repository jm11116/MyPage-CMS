<?php
    session_start();
    $_SESSION['success'] = 0;
    header('Location: index.php');
?>