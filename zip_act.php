<?php
require_once( "php/settings/db_connect.php");
require_once( "php/settings/functions.php");
require_once( "php/settings/game_attribute.php");
start_session(1209600);
$sourcefolder="upload";
$zipname="zipfile/zipfile.zip";
$file_name="選手照片.zip";
$adminid=$_SESSION["adminid"];
$sql="select * from auth where admin=?";
$sth=$db->prepare($sql);
$sth->execute(array($adminid));
if($result = $sth->fetchObject()){
    $permit=$result->permit;
    $adminname=$result->adminname;
}else{
    echo "permission denied";
    exit();
}
if($permit{0}!=3){
    echo "permission denied";
    exit();
}
if(Zip($sourcefolder,$zipname)){
	remote_log("[下載資料] $adminname 下載了 $file_name");
    $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
    $sth=$db->prepare($sql);
    $sth->execute(array("zip",$adminname." download 選手照片.zip",get_ip()));
    $file_path = __dir__ ."/".$zipname;
    $file_size = filesize($file_path);
    ob_clean();
    ob_end_flush();
    header('Content-Description: File Transfer');
    header('Pragma: public');
    header('Expires: 0');
    header('Last-Modified: ' . gmdate('D, d M Y H:i ',filemtime($file_path)) . ' GMT');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . $file_size);
    header('Content-Disposition: attachment; filename="' .$file_name . '";');
    header('Content-Transfer-Encoding: binary');
    readfile($file_path);

}
echo "fail";


?>
