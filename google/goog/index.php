<?php
global $output_title, $output_body, $output_nav;
$output_title = '';
$output_body = '<h1>enarion.net Google Client API demos</h1>
<p>Please click on one of the entries in the navigation bar <u>at the top</u>. You need a Google (user) account with access to the appropriate service, otherwise an error will occur.</p>
<div class="alert alert-info">We do not store the login credentials nor the data being displayed. This is just a simple demo page.</div>';

$output_nav = '<li><a href="ga_adsense.php">Adsense</a></li>'."\n";
$output_nav .= '<li><a href="ga_adwords.php">Adwords</a></li>'."\n";
$output_nav .= '<li><a href="ga_analytics.php">Analytics</a></li>'."\n";
include("output.php");
?>