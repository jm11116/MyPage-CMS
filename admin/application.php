<!DOCTYPE HTML>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--JavaScript warning-->
    <noscript>
        <p id="javascript_warning">JavaScript must be enabled!</p>
        <style>#main, #title_container{ display:none; }</style>
    </noscript>
    <!--Application favicons-->
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="icons/favicon-16x16.png">
    <link rel="manifest" href="icons/site.webmanifest">
    <!--Load external JS-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script   src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"   integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="   crossorigin="anonymous"></script>
    <!--Fonts and styles-->
    <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed:500,700" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,800,800i" rel="stylesheet" type="text/css"/>
    <link href="styles.css" rel="stylesheet" type="text/css"/>
    <?php require_once "application_settings.php"; ?>
    <?php require_once "login_check.php"; ?>
    <title>MyPage CMS</title>
</head>

<body>
    <div id="title_container">
    <h1>MyPage CMS</h1>
    <div id="navbar">
        <a href="javascript:void(0);" id="home_link" class="section_link">Home</a>
        <a href="javascript:void(0);" id="editor_link" class="section_link">Editor</a>
        <a href="javascript:void(0);" id="blog_link" class="section_link">Blog</a>
        <a href="javascript:void(0);" id="tracking_link" class="section_link">Tracking</a>
        <a href="javascript:void(0);" id="uploads_link" class="section_link">Uploads</a>
        <a href="javascript:void(0);" id="settings_link" class="section_link">Settings</a>
        <a href="../pages/" target="_blank" id="view_link" >View</a>
        <a href="logout.php" id="view_link" >Logout</a>
    </div>
    </div>
    <div id="main"></div>

<script src="js/application.js"></script>

<div class="bottom_spacer"></div>

</body>

</html>