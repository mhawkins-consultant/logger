<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
  <title>Log Analyzer</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="style.css" type="text/css" />
</head>

<body style="background-color:#EBEBEB">
<?php
function abbreviate_number($num) {
    if($num >= 1000) {
        $units = array('', 'KB', 'MB', 'GB', 'TB');
        $log = floor(log($num, 1000));
        $pow = pow(1000, $log);
        return round($num / $pow, 2) . $units[$log];
    } else return $num;
}
session_start();
if(isset($_REQUEST['submit'])){
    $_SESSION['path']=$_POST['path'];
    if(isset($_POST['file'])) $_SESSION['file']=$_POST['file'];
    $_SESSION['lines']=$_POST['lines'];
    if(strlen($_SESSION['include'])>0) $_SESSION['include']=$_POST['include'];
    else $_SESSION['include']="/.*/";
    if(strlen($_SESSION['exclude'])>0) $_SESSION['exclude']=$_POST['exclude'];
    else $_SESSION['exclude']="/\b\B/"; // don't match anything
    $_SESSION['refresh']=$_POST['refresh'];
    if(isset($_POST['reverse'])){ $_SESSION['reverse']=" checked "; $reverse=true;
    }else{ $_SESSION['reverse']=""; $reverse=false; }
}else{
  if(!isset($_SESSION['path'])) $_SESSION['path']='/var/log/network/*';
  if(!isset($_SESSION['file'])) $_SESSION['file']='example.log';
  if(!isset($_SESSION['lines'])) $_SESSION['lines']='5000';
  if(!isset($_SESSION['include'])) $_SESSION['include']='/./';
  if(!isset($_SESSION['exclude'])) $_SESSION['exclude']="/\b\B/"; // don't match anything
  if(!isset($_SESSION['refresh'])) $_SESSION['refresh']='600';

  if(!isset($_SESSION['reverse'])){ $_SESSION['reverse']=""; $reverse=false; }
  elseif($_SESSION['reverse']==" checked ") $reverse=true;
  else { $_SESSION['reverse']=""; $reverse=false; }
}
?>
<script language="JavaScript">
<!--
var timer = <?php if (isset($_SESSION['refresh'])) echo $_SESSION['refresh']; else echo "60"; ?>;
var tim1 = setInterval('countDown()', 1000);
var tim2 = setInterval('refreshPage()', <?php echo ($_SESSION['refresh']*1000); ?>);
function countDown() {
	if(--timer<=600){
		document.getElementById("countDown").innerHTML = " " + timer;
	}else{
		document.getElementById("countDown").innerHTML = " " + Math.round(timer/60) + " min";
		if(timer%2==0){
			document.getElementById("countDown").style.color = "Red";
		} else {
			document.getElementById("countDown").style.color = "LightGreen";
		}
	}
}
function refreshPage() {
  ShowDialog();
  window.location.assign("index.php");
}
function ShowDialog() {
	document.getElementById("WaitDialog").style.display = "inline";
}
function resetTimer() {
	clearTimeout(timer); clearTimeout(tim1); clearTimeout(tim2);
	document.getElementById("countDown").innerHTML = "";
	document.getElementById("apply").className = "red";
	document.getElementById("refresh").className = "red";
}
function resetPath() {
	resetTimer();
	document.getElementById("path").value = "/var/log/network/*";
}
function resetInclude() {
	resetTimer();
	document.getElementById("include").value = "/./";
}
function resetExclude() {
	resetTimer();
	document.getElementById("exclude").value = "/\\b\\B/";
}
-->
</script>

<form id="form" name="form" method="post" onsubmit="ShowDialog(this.form)">
<table>
<tr><td colspan='10' class='green'>Logging Analyser version 0.1e (Login)</td></tr>

<tr><td colspan='10' style="height:1px"><div id="WaitDialog" style="text-align: center">
	<div style="margin-top: 10px; color: red">
		<img	src="rainbow_spinner.gif"/><br>
		<b>Please wait while the log file is parsed. Click <a href="reset.php">HERE</a> to cancel and try again.</b>
	</div>
</div>
</td></tr>

</table>
<table id="OptionsTable" style="display:none">

<?php
echo '<tr><td class="lgreen">';
echo "Path: <input class='input' type='text' name='path' id='path' value='".$_SESSION['path']."' oninput='resetTimer()' onkeypress='resetTimer()'>";
echo '<button type="button" onclick="resetPath()">Reset</button>';
echo "</td>";
?>

<td class="lgreen">
Include Filter (Regex):
<input class="input" type="text" name="include" id="include" maxlength="200" size="50" value='<?php echo $_SESSION['include'] ?>' oninput='resetTimer()' onkeypress='resetTimer()'>
<button type="button" onclick='resetInclude()'>Reset</button>
</td>


<?php
if(isset($_SESSION['lines'])) $lines=$_SESSION['lines'];
else $lines=25;
$displayLines = array( "10","25","50","100","250","500","1000","2500","5000","10000","25000","50000","100000","250000","500000");
?>
<td class='lgreen'>
Display Lines:
 <select name="lines" onchange='resetTimer()'>
<?php 
foreach($displayLines as $displayLine){
  echo '<option value="'.$displayLine.'" ';
  if($lines==$displayLine) echo "selected";
  echo '>'.$displayLine.'</option>';
}
echo " </select>";
echo "</td>";
?>

<?php
echo '<td class="lgreen" id="refresh">';
echo "Refresh: ";
echo "<select name='refresh' onfocus='resetTimer()'>";
for($count=10;$count<60;$count+=10){
    if($_SESSION['refresh']!=$count) echo "<option value='".$count."'>".$count." sec</option>";
    else echo "<option value='".$count."' selected>".$count." sec</option>";
}
for($count=60;$count<=600;$count+=60){
    if($_SESSION['refresh']!=$count) echo "<option value='".$count."'>".($count/60)." min</option>";
    else echo "<option value='".$count."' selected>".($count/60)." min</option>";
}
for($count=900;$count<=3000;$count+=900){
    if($_SESSION['refresh']!=$count) echo "<option value='".$count."'>".($count/60)." min</option>";
    else echo "<option value='".$count."' selected>".($count/60)." min</option>";
}
if($_SESSION['refresh']!=3600) echo "<option value='3600'>1 hour</option>";
else echo "<option value='3600' selected>1 hour</option>";
if($_SESSION['refresh']!="86400") echo "<option value='86400'>1 Day</option>";
else echo "<option value='86400' selected>1 Day</option>";
echo "</select><span style='color:red;' id='countDown'></span></td>";
?>

</tr>
<tr>

<?php 
$fileList = glob($_SESSION['path']);
if($fileList!==false&&count($fileList)>0){
    echo "<td class='lgreen'>Select File: ";
    echo "<select name='file' onfocus='resetTimer()'>";
    echo "<option value='-' selected>-</option><br>";
    $chosenfile="-";
    foreach($fileList as $filename){
        // set linecount to number of lines in file TBD move this into its own php and run it outside
//        $linecount = 0;
//        $handle = fopen($filename, "r");
//        while(!feof($handle)){
//            $line = fgets($handle);
//            $linecount++;
//        }
//        fclose($handle);
        //Use the is_file function to make sure that it is not a directory or null.
        if(is_file($filename)){
            if($_SESSION['file']!==$filename) echo "<option value='".$filename."'>".$filename." (".abbreviate_number(filesize($filename)).")</option><br>";
            else{
                echo "<option value='".$filename."' selected>".$filename." (".abbreviate_number(filesize($filename)).")</option><br>";
                $chosenfile=$filename;
            }
        }
    }
    echo "</select></td>";
}else echo "<td class='lgreen'>No files found!</td>";
?>



<td class="lgreen">
Exclude Filter (Regex):
<input class="input" type="text" name="exclude" id="exclude" maxlength="200" size="50" value='<?php echo $_SESSION['exclude'] ?>' oninput='resetTimer()' onkeypress='resetTimer()'>
<button type="button" onclick='resetExclude()'>Reset</button>
</td>

<td class="lgreen">
Show Newest First:
<input type="checkbox" name="reverse" <?php echo $_SESSION['reverse']; ?> onchange='resetTimer()'>
</td>

<?php
$linecount = 1;
if(isset($chosenfile)&&$file = fopen($chosenfile, "r")) {
  unset($array);
  if(!$reverse){ // do file in order (oldest to newest)
    $matchcount=0;
    while(!feof($file)) {
      $textperline = fgets($file);
      $linecount++;
      if(preg_match($_SESSION['include'],$textperline)&&!preg_match($_SESSION['exclude'],$textperline)){
        $matchcount++;
        if($matchcount<=$lines) $array[] = $textperline;
      }
    }
    fclose($file);
  }else{ // do file in reverse by finding all matching lines first, then reversing, then truncating
    $matchcount=0;
    while(!feof($file)) {
      $textperline = fgets($file);
      $linecount++;
      if(preg_match($_SESSION['include'],$textperline)&&!preg_match($_SESSION['exclude'],$textperline)){
        $matchcount++;
        $array[] = $textperline;
      }
    }
    fclose($file);
    $array = array_reverse($array);
    if($matchcount>$lines) $array = array_slice($array, 0, $lines + 1, true);
    else $array = array_slice($array, 0, $matchcount+1, true);
  }
}
?>

<?php
echo '<td class="lgreen" id="apply">';
echo '<input class="submit" type="submit" name="submit" value="Apply">';
echo "</td>";
?>

</tr>
</table>
<table>
<?php
echo"<tr><td colspan='10' class='spacer'>";
if($linecount>0) echo " Lines Processed:".$linecount;
if(isset($matchcount)) echo " - Matched Lines:".$matchcount;
echo "</td></tr>";
?>

<?php

if(isset($array)){
  $count=0;
  foreach($array as $textperline){
    $count++;
    if($count%100==0) echo "<tr colspan='10'><td style='background-color: Aquamarine'>--- Line:[".$count."] ---</td></tr>";
    echo "<tr colspan='10'>";
    echo "<td style='text-align: left; ";
    if($count%2==0) echo "background-color: GhostWhite;";
    else  echo "background-color: Gainsboro;";
    echo "'>[".$count."]: ".$textperline."</td></tr>";
  }
}
?>
</table>
<table>
<?php // footer
  echo"<tr><td colspan='10' class='spacer'></td></tr>";
  echo"<tr><td class='header' colspan='10'>Wantegrity INC. Logging Analyzer Copyright and all rights reserved (2008-".date("Y").")</td></tr>";
?>
</table>
</form>
<script language="JavaScript">
<!--
  	document.getElementById("WaitDialog").style.display = "none";
	document.getElementById("OptionsTable").style.display = "inline-table";
-->
</script>

</body>

</html>

