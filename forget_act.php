<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/PHPMailerAutoload.php");
$data=new stdClass();

$username=$_POST['username'];
$realname=$_POST['realname'];
$cellphone=$_POST['cellphone'];
$email=$_POST['email'];

//check for blank
if(!(preg_match('/\S/',$username)&&
     preg_match('/^\S*$/',$realname)&&
     preg_match('/\S/',$cellphone)&&
     preg_match('/\S/',$email)
    )){
    //$data->error.="blank field detected\n";
    $data->error.="尚有空白欄位\n";
}

$fields = array(
   "username" => $username,
   "realname" => $realname,
   "cellphone" => $cellphone,
   "email" => $email
);
check_input_fields($fields,$data);

$sql = "SELECT `username`, `realname`, `cellphone`, `email` FROM `users` WHERE `username` = ?";
$sth = $db->prepare($sql);
$sth->execute(array($username));

if( $result = $sth->fetchObject() ){ // valid username
    if($result->realname != $realname || $result->cellphone != $cellphone || $result->email != $email){
        $data->error.="使用者帳戶資料不符";
    }
    else{
        $new_password = random_password();
        $hash_password = nic_hash($new_password); 
        
        $message="使用者 " .$username ." 忘記密碼<br>真實姓名: " .$realname ."<br>手機: " .$cellphone ."<br> E-mail: " .$email ."<br>您的新密碼: ". $new_password ."<br><br>若有任何問題，請聯絡<br>email:<a href='mailto:nuic2017nctu@gmail.com'>nuic2017nctu@gmail.com</a><br>Facebook:<a href='https://www.facebook.com/nuic2017'>https://www.facebook.com/nuic2017</a><br><br>2017大資盃工作團隊<br><img src='https://nuic2017.com/wp-content/themes/vantage/sponsor/sponsor4.jpg'>";

        
        if(sendmail($email,"[2017大資盃] 使用者忘記密碼",$message)){
			remote_log("[重設密碼] $username 重設密碼");
            $sql = "UPDATE `users` SET password=? WHERE username=?";
            $sth = $db->prepare($sql);
            $sth->execute(array($hash_password,$username));

            $data->message="新密碼已寄出，請前往信箱確認\n若十分鐘後仍未收到系統來信，請與管理員聯絡";
            $data->redirect="login"; // 之後可能要導入另一個轉信成功的畫面???

            // if the mail sent failed -> GG
        }else{
            $data->error="信件寄送失敗，請再試一次，若有任何問題請與管理員聯絡";
        }
    }
}
else{ // invalid username
    $data->error.="無此使用者，若有任何問題請與管理員聯絡\n";  
}


if($data->error){
    echo json_encode($data);
    exit();
}
echo json_encode($data);
//echo '<meta http-equiv="refresh" content="2;url=http://yahoo.com" />';
?>
