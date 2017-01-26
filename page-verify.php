<?php 

require_once( "php/settings/db_connect.php");
require_once( "php/settings/functions.php");
require_once( "php/settings/game_attribute.php");
require_once("php/settings/PHPMailerAutoload.php");

testing();

start_session(1209600);

if(!isset($_SESSION[ "uid"])){

    header( 'Location: ../login' ) ;

}



$uid=$_SESSION["uid"];

$sql = "select * from users where uid=?";

$sth = $db->prepare($sql);

$sth->execute(array($uid));

$result = $sth->fetchObject();

$veri=$result->veri_state;





if($veri==1){

    header( "refresh:2; url=../main" ); 

    get_header();

    echo "您的帳號已經啟用";

    get_footer();

    exit();

}



$email=$result->email;

$hash_veri=$hash_veri = hash("sha256",$new_veri).$uid;

$username=$result->username;

//////////////////////////////////////////////////////////////////////////////////

$link="https://nuic2017.com/verification/?v=".$hash_veri;

$message=$username." 您好:<br>感謝您的註冊 <a href='https://nuic2017.com'>2017大資盃</a> 帳號<br>請點選 <a href='".$link."'>認證連結</a> 以啟用您的帳號<br><br>若無法直接點擊，請複製以下網址並貼至網址列<br>".$link."<br><br>若是您並未註冊 2017大資盃，請直接忽略此信件<br><br>若有任何問題，請聯絡<br>email:<a href='mailto:nuic2017nctu@gmail.com'>nuic2017nctu@gmail.com</a><br>Facebook:<a href='https://www.facebook.com/nuic2017'>https://www.facebook.com/nuic2017</a><br><br>2017大資盃工作團隊<br><img src='https://nuic2017.com/wp-content/themes/vantage/sponsor/sponsor4.jpg'>";








if(sendmail($email,"[2017大資盃] 使用者帳戶啟用",$message)){

    $sql = "UPDATE `users` SET veri_code=? WHERE username=?";

    $sth = $db->prepare($sql);

    $sth->execute(array($hash_veri,$username));

    header( "refresh:2; url=../main" ); 

    get_header();

    echo "認證信已寄出，請收取email以啟用帳號";

    get_footer();

    exit();

}else{

    header( "refresh:2; url=../main" ); 

    get_header();

    echo "信件寄送失敗，請再試一次，若有任何問題請與管理員聯絡";

    get_footer();

    exit();

}

//////////////////////////////////////////////////////////////////////////////////







?>

