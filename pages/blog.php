<!DOCTYPE html>
<html lang="en">
<head><?php include "../header.php"; ?></head>
<body>

<div id="actual_content">
<center><h1 class="page_heading"><?php echo $page_title ?></h1></center>

<br>
<div class="blog_contact_text"></div>
<div class="text_div">
<?php
//Use this method instead of file_get_contents to allow execution of PHP
    ob_start();
    include($txt_path);
    $output = ob_get_contents();
    ob_end_clean();
    echo nl2br($output);
?>
</div>
<br><br>

<?php include dirname(__DIR__, 1) . "/blog_nav.php"; ?>

<?php include "../footer.php" ?>

</body>
</html>