<?php
$message=$_POST["message"];
$ip=$_POST["ip"];
$time=$_POST["time"];
$file = fopen("nuiclog.txt","a");
fwrite($file,$time."\t".$message."\t".$ip.PHP_EOL);
fclose($file);
?>