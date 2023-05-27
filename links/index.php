<?php
require_once('include_files/init.php');
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//$url = 'https://www.10dakot.co.il/recipe/%D7%98%D7%97%D7%99%D7%A0%D7%94/';
//$depth = 2;
//$all_links = getAllLinksInUrl($url, $depth);
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <title><?php echo 'links'; ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css?ver=<?php echo $css_time; ?>" />
</head>
<body class="">
<header class="">
</header>
<img src="images/loading.gif" class="downloading-image" id="downloading-image" />
<div class="main-div">
    <div class="main-inner">
        <div class="inputs-div">
            <label class="labels">לינק:</label>
            <input class="inputs" type="text" id="url" />
        </div>
        <div class="inputs-div">
            <label class="labels">עומק חיפוש:</label>
            <input class="inputs" type="text" id="depth" />
        </div>
        <div><button id="send_link">חפש</button></div>
        <table class="main-table" id="links_table">
            <thead>
            <tr>
                <th>#</th>
                <th>לינק מקושר</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="js/script.js?ver=<?php echo $js_time; ?>"></script>
</body>
</html>
