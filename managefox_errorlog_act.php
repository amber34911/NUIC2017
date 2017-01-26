<?php
    require_once("php/settings/db_connect.php");
    require_once("php/settings/functions.php");
    require_once("php/settings/userdb.php");
    start_session(1209600);
    $message = $_POST["message"];
    $uid = $_SESSION["uid"];
    if($message==""){
        $message="empty message";
    }
    if($uid==""){
        $uid="undefined uid";
    }
    $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
    $sth = $db->prepare($sql);
    $sth->execute(array("error",$uid." 報名出現錯誤: ".$message,get_ip()));

?>
