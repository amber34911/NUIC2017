<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/userdb.php");
start_session(1209600);
$data = new stdClass();


$adminid=$_SESSION["adminid"];
$sql="select * from auth where admin=?";
$sth=$db->prepare($sql);
$sth->execute(array($adminid));
if($result = $sth->fetchObject()){
    $permit=$result->permit;
    $adminname=$result->adminname;
    if($permit{0}!=3){
        $data->error.= "permission denied: invalid user";
        echo json_encode($data);
        exit();
    }
}else{
    $data->error.= "permission denied: invalid user";
    echo json_encode($data);
    exit();
}
//get player info
$pid=$_POST["pid"];
$sql="select * from players where pid=?";
$sth=$db->prepare($sql);
$sth->execute(array($pid));
$result = $sth->fetchObject();

$tid=$result->tid;
$realname=$result->realname;
$pic_head=$result->pic_head;
$pic_front=$result->pic_front;
$pic_back=$result->pic_back;
$pic_second=$result->pic_second;
$teamleader=$result->teamleader;


//get tid info
$sql="select * from teams where tid=?";
$sth=$db->prepare($sql);
$sth->execute(array($tid));
$result = $sth->fetchObject();

$category=$result->category;
$reg_player=$result->reg_player;

$path = "$category/$tid/";
$upload_dir = "upload/$path";
$thumb_dir = "thumb/$path";

if(!unlink($upload_dir.$pic_head)){
    $data->error="delete upload head fail";
    echo json_encode($data);
    exit();
}
if(!unlink($upload_dir.$pic_front)){
    $data->error="delete upload front fail";
    echo json_encode($data);
    exit();
}
if(!unlink($upload_dir.$pic_back)){
    $data->error="delete upload back fail";
    echo json_encode($data);
    exit();
}
if(!unlink($upload_dir.$pic_second)){
    $data->error="delete upload second fail";
    echo json_encode($data);
    exit();
}
if(!unlink($thumb_dir.$pic_head)){
    $data->error="delete thumb head fail";
    echo json_encode($data);
    exit();
}
if(!unlink($thumb_dir.$pic_front)){
    $data->error="delete thumb front fail";
    echo json_encode($data);
    exit();
}
if(!unlink($thumb_dir.$pic_back)){
    $data->error="delete thumb back fail";
    echo json_encode($data);
    exit();
}
if(!unlink($thumb_dir.$pic_second)){
    $data->error="delete thumb second fail";
    echo json_encode($data);
    exit();
}
$sql = "delete from players where pid=?";
$sth = $db->prepare($sql);
$sth->execute(array($pid));

$sql = "delete from pic where hashed_name=?";
$sth = $db->prepare($sql);
$sth->execute(array($pic_head));
$sql = "delete from pic where hashed_name=?";
$sth = $db->prepare($sql);
$sth->execute(array($pic_front));
$sql = "delete from pic where hashed_name=?";
$sth = $db->prepare($sql);
$sth->execute(array($pic_back));
$sql = "delete from pic where hashed_name=?";
$sth = $db->prepare($sql);
$sth->execute(array($pic_second));

$reg_player--;
$sql = "UPDATE teams SET `reg_player` = ? WHERE `teams`.`tid` = ?";
$sth = $db->prepare($sql);
$sth->execute(array($reg_player,$tid));




remote_log("[刪除選手 ".$adminname." 刪除 $pid ");
$data->message ="delete $pid successfully";

echo json_encode($data);
exit();
?>
