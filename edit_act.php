<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
start_session(1209600); 
$data=new stdClass();
$username = $_POST['username'];
$password1 = $_POST['password1'];
$password2 = $_POST['password2'];
$realname = $_POST['realname'];
$school = $_POST['school'];
$department = $_POST['department'];
$cellphone = $_POST['cellphone'];
$email = $_POST['email'];
$uid = $_SESSION["uid"];
//check for blank
if(!(preg_match('/\S/',$username)&&
     preg_match('/\S/',$realname)&&
     //preg_match('/^\S*$/',$realname)&&
     preg_match('/\S/',$school)&&
     preg_match('/\S/',$department)&&
     preg_match('/\S/',$cellphone)&&
     preg_match('/\S/',$email)
    )){
    $data->error.="尚有空白欄位\n";
}
$fields = array(
   "password1" => $password1,
   "password2" => $password2,
   "username" => $username,
   "realname" => $realname,
   "school" => $school,
   "department" => $department,
   "cellphone" => $cellphone,
   "email" => $email
);
check_input_fields($fields,$data);

if($data->error){
    echo json_encode($data);
    exit();
}

// shall we have an another page for users to change their password specifically?

if($password1==""){  // if password edition == empty, password = don't care
    
   remote_log("[修改資料] $username 修改個人資料");
    $sql = "UPDATE `users` SET school=? ,department=? ,cellphone=? ,email=? ,realname=? WHERE uid=?";
    $sth = $db->prepare($sql);
    $sth->execute(array($school,$department,$cellphone,$email,$realname,$uid));
    $_SESSION["username"] = $username;
    $data->message="success";
    $data->redirect="main";
    $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
    $sth=$db->prepare($sql);
    $sth->execute(array("edit user",$username." change his data to ".$school.",".$department.",".$cellphone.",".$email.",".$realname.",".$uid,get_ip()));
}
else{
    
    remote_log("[修改資料] $username 修改個人密碼");
    $hashed_password=nic_hash($password1);
    $sql = "UPDATE `users` SET password=? ,school=? ,department=? ,cellphone=? ,email=? ,realname=? WHERE uid=?";
    $sth = $db->prepare($sql);
    $sth->execute(array($hashed_password,$school,$department,$cellphone,$email,$realname,$uid));
    $_SESSION["username"] = $username;
    $data->message="success";
    $data->redirect="main";
    
    $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
    $sth=$db->prepare($sql);
    $sth->execute(array("edit pwd",
$username." change his data to ".$hashed_password.",".$school.",".$department.",".$cellphone.",".$email.",".$realname.",".$uid,get_ip()));
}
//success
echo json_encode($data);
?>
