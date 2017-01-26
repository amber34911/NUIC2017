<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/game_attribute.php");
$data=new stdClass();
$teamname=$_POST["teamname"];
$category=$_POST["category"];
$fields = array(
   "game_category" => $category,
   "teamname" => $teamname
);
check_input_fields($fields,$data);
if($data->error!=""){
    $data->teamname_check=false;
}
$sql = "select * from teams where teamname=? and category=?";
$sth = $db->prepare($sql);
$sth->execute(array($teamname,$category));
if($result = $sth->fetchObject()){
    $data->teamname_check=false;
    echo json_encode($data);
    exit();
}
$data->teamname_check=true;
echo json_encode($data);
exit();


?>