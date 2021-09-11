<?php
session_start();
if (empty($_SESSION['success']) || $_SESSION['success'] != 1){
  die("<div style='display:block;margin:auto;text-align:center;width:100%;margin-top:30vh;font-size:1.2em;'>You are not logged in. Click <a href='index.php'>here</a> to go to login page.</div>");
}
?>

<div class="grid-flex">

<div id="home" class="grid-item" style="flex-grow: 100;">

<h2>Home</h2>

<p style="text-align:justify;">MyPage CMS is an easy-to-use content management system written in JavaScript, jQuery, and PHP by John Micallef. It allows you to create a responsive website within minutes, simply by uploading the MyPage CMS app to your hosting server's root folder. It comes with a range of eye-catching themes that can be changed with just a click, each of which can be easily customized and edited with beginner-level knowledge of CSS, and which are pictured below. It also provides server-level tracking features, free and out of the box.</p>

<p style="text-align:justify;">MyPage CMS was designed with clients in mind. The editor itself is so simple that a web design client will be easily able to edit the content of their website. This presents an alternative to Wordpress, which can be so packed with features, clutter, and noise that making changes can be daunting for non-tech-savvy clients. Uploading changes to the website is instant – no FTP required – just hit 'Save'!</p>



<br>




</div>


<div id="site_info" class="grid-item" style="flex-grow: 100;">

<h2>Site Info</h2>

<div id="site_info_container">

<?php $admin = simplexml_load_file("admin_settings.xml");?>
<?php $site_info = simplexml_load_file(dirname(__DIR__, 1) . "/site_info.xml");?>

<?php
if ($site_info->site_enabled == "enabled"){
  echo "<p>Website status: <i><b style='color:green';>Enabled</b></i>.</p>";
} else {
  echo "<p>Website status: <i><b style='color:red';>Disabled - re-enable in settings</b></i>.</p>";
}
?>

<p>Your website's domain is: <i><b><?php echo $admin->domain_name;?></b></i>.</p>
<p>Your web hosting provider is: <i><b><?php echo $admin->hosting_provider;?></b></i>.</p>
<p>Your website was made by: <i><b><?php echo $admin->creator;?></b></i>.</p>
<p>For support, please email: <i><b><?php echo $admin->support_email;?></b></i>.</p>
<p>For help, please visit: <i><b><?php echo $admin->app_website;?></b></i>.</p>
<p>Other information: <i><b><?php echo $admin->misc;?></b></i>.</p>

</div>

</div>

</div>