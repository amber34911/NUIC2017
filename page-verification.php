<?php
require_once( "php/settings/db_connect.php");
require_once( "php/settings/functions.php");
require_once( "php/settings/game_attribute.php");
testing();
start_session(1209600);
//header( "refresh:2; url=../login" ); 
$in=$_GET["v"];

preg_match('/[a-z0-9]{64,}/', $in,$out);
if($out[0]!=$in){
    header( "refresh:2; url=../login" );
    get_header(); 
    echo "帳號啟用失敗";
    get_footer();
    exit();
}
if (check_have_critical_character($_field)){
    header( "refresh:2; url=../login" );
    get_header(); 
    echo "帳號啟用失敗";
    get_footer();
    exit();
    
}
$uid=substr($in,64-strlen($in));
$in=substr($in,0,64);
preg_match('/[0-9]{0,4}/', $uid,$out);
if($out[0]!=$uid){
    header( "refresh:2; url=../login" );
    get_header(); 
    echo "帳號啟用失敗";
    get_footer();
    exit();
}

$sql = "select * from users where uid=?";
$sth = $db->prepare($sql);
$sth->execute(array($uid));
if(!$result = $sth->fetchObject()){
    header( "refresh:2; url=../login" );
    get_header(); 
    echo "帳號啟用失敗";
    get_footer();
    exit();
}else{
    if($result->veri_state==1){
        header( "refresh:2; url=../login" );
        get_header(); 
        echo "帳號已成功啟用";
        get_footer();
        exit();
    }
}

$sql = "update users SET veri_state = ?, veri_code = ? WHERE uid = ?";
$sth = $db->prepare($sql);
$sth->execute(array("1","",$uid));
if($sth->rowCount()){
    header( "refresh:2; url=../login" );
    get_header(); 
    echo "帳號已成功啟用";
    get_footer();
    exit();
}else{
    header( "refresh:2; url=../login" );
    get_header(); 
    echo "帳號啟用失敗";
    get_footer();
    exit();
}

?>
