<!DOCTYPE html>
<html lang="en">
<head><?php include "../header.php"; ?></head>
<body>

<div id="actual_content">
<center><h1 class="page_heading"><?php echo $page_title ?></h1></center>

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

<?php include "../footer.php" ?>

</body>
</html>