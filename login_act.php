<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");

$data=new stdClass();


$username=$_POST["username"];
$password = $_POST["password"];

$fields = array(
   "password1" => $password,
   "username" => $username
);
check_input_fields($fields,$data);

if($data->error){
    echo json_encode($data);
    exit();
}

$hashed_password=nic_hash($password);
$sql = "select * from users where username=? and password=?";
$sth = $db->prepare($sql);
$sth->execute(array($username,$hashed_password));

if($result = $sth->fetchObject()){
    start_session(1209600);
    $_SESSION["uid"]=$result->uid;
    $_SESSION["username"]=$result->username;
    $data->message="login success".$_SESSION["uid"].$_SESSION["username"];
    remote_log("[登入成功] ".$result->uid.".".$result->school." ".$result->department." ".$result->realname);
    $data->redirect="main";
}else{
    $data->error="登入失敗: 無此帳號或密碼錯誤";
    remote_log("[登入失敗] ".$username." 無此帳號或密碼錯誤");
}
echo json_encode($data);

?>
