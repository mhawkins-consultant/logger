<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
  <title>Log Analyzer</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="style.css" type="text/css" />
</head>

<body style="background-color:#EBEBEB">
<?php
session_start();
$_SESSION['path']='/var/log/network/*';
$_SESSION['file']='';
$_SESSION['lines']='100';
$_SESSION['include']='/00:00/'; // only include one minute after midnight
$_SESSION['exclude']="/\b\B/"; // don't exclude anything
$_SESSION['refresh']='30';
$_SESSION['reverse']="";
session_destroy();
?>
<table>
<tr><td colspan='10' class='green'>Logging Analyser version 0.1e</td></tr>
<td>
<h1>Processing of the log file was cancelled and all user settings were reset. Click <a href="index.php">HERE</a> to return to logger.</h1>
</td>
</table>
<table>
<?php // footer
  echo"<tr><td colspan='10' class='spacer'></td></tr>";
  echo"<tr><td class='header' colspan='10'>Wantegrity INC. Logging Analyzer Copyright and all rights reserved (2008-".date("Y").")</td></tr>";
?>
</table>

</body>

</html>

