<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/PHPMailerAutoload.php");
$data=new stdClass();
$username = $_POST['username'];
$password1 = $_POST['password1'];
$password2 = $_POST['password2'];
$realname = $_POST['realname'];
$school = $_POST['school'];
$department = $_POST['department'];
$cellphone = $_POST['cellphone'];
$email = $_POST['email'];
//check for blank

if(!(preg_match('/\S/',$username)&&
     preg_match('/\S/',$password1)&&
     preg_match('/\S/',$password2)&&
     preg_match('/\S/',$realname)&&
     //preg_match('/^\S*$/',$realname)&&
     preg_match('/\S/',$school)&&
     preg_match('/\S/',$department)&&
     preg_match('/\S/',$cellphone)&&
     preg_match('/\S/',$email)
    )){
    //$data->error.="blank field detected\n";
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

$sql = "select * from users where username=?";
$sth = $db->prepare($sql);
$sth->execute(array($username));
//check for existing user
if($result = $sth->fetchObject()){
    //$data->error.="user existed\n";
    $data->error.="註冊失敗: 使用者名稱已有人使用\n";
}
if($data->error){
    echo json_encode($data);
    exit();
}
$hashed_password=nic_hash($password1);
$sql = "INSERT INTO users(username,password,school,department,cellphone,email,realname) VALUES(? ,? ,? ,? ,? ,? ,?)";
$sth = $db->prepare($sql);
$sth->execute(array($username,$hashed_password,$school,$department,$cellphone,$email,$realname));
$uid=$db->lastInsertId();
////////////////////////////////////////////////////////////////////////////////////////
$new_veri = random_password();
$hash_veri = hash("sha256",$new_veri).$uid ;
$link="https://nuic2017.com/verification/?v=".$hash_veri;
$message=$username." 您好:<br>感謝您的註冊 <a href='https://nuic2017.com'>2017大資盃</a> 帳號<br>請點選 <a href='".$link."'>認證連結</a> 以啟用您的帳號<br><br>若無法直接點擊，請複製以下網址並貼至網址列<br>".$link."<br><br>若是您並未註冊 2017大資盃，請直接忽略此信件<br><br>若有任何問題，請聯絡<br>email:<a href='mailto:nuic2017nctu@gmail.com'>nuic2017nctu@gmail.com</a><br>Facebook:<a href='https://www.facebook.com/nuic2017'>https://www.facebook.com/nuic2017</a><br><br>2017大資盃工作團隊<br><img src='https://nuic2017.com/wp-content/themes/vantage/sponsor/sponsor4.jpg'>";




if(sendmail($email,"[2017大資盃] 使用者帳戶啟用",$message)){

    $sql = "UPDATE `users` SET veri_code=? WHERE username=?";
    $sth = $db->prepare($sql);
    $sth->execute(array($hash_veri,$username));

    $data->message="註冊成功: 請收取認證email以啟用帳號";
    $data->redirect="login"; // 之後可能要導入另一個轉信成功的畫面???
	remote_log("[註冊成功] ".$uid.".".$school." ".$department." ".$realname);
    // if the mail sent failed -> GG
}else{
    $data->error="錯誤: 信件寄送失敗，請再試一次，若有任何疑問請聯絡網站管理員";
    remote_log("[註冊失敗] 寄信失敗");
}
////////////////////////////////////////////////////////////////////////////////////////
echo json_encode($data);
?>
