<?php
require_once( "php/settings/db_connect.php");
require_once( "php/settings/functions.php");
require_once( "php/settings/game_attribute.php");

$data=new stdClass();
$tid=$_POST["tid"];

if(preg_match('/\D/', $tid)){
    $data->error="team not exist!";
    echo json_encode($data);
    exit();
}

$sql="select * from teams where tid=? and success=1";
$sth=$db->prepare($sql);
$sth->execute(array($tid));
if(!$result=$sth->fetchObject()){
    $data->error="team not exist!";
    echo json_encode($data);
    exit();
}
$sql="select * from players where tid=?";
$sth=$db->prepare($sql);
$sth->execute(array($tid));
while($result=$sth->fetchObject()){
    $data->message.="<div class='single_player'>".$result->realname."</div>";
}

echo json_encode($data);
?>



