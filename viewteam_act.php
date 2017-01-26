<?php
//header('Content-Type: application/json; charset=utf-8');
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/game_attribute.php");
$data=new stdClass();
start_session(1209600);
$uid=$_SESSION["uid"];
$tid=$_POST["tid"];
$fields = array(
      "number" => $tid
   );
   check_input_fields($fields,$data);
    if($data->error!=""){
        $data->error="您並不是該隊聯絡人";
        echo json_encode($data);
        exit();
    }

// 檢查是否為該隊聯絡人
$sql="select * from teams where tid=?";
$sth=$db->prepare($sql);
$sth->execute(array($tid));
$result = $sth->fetchObject();
$teamname=$result->teamname;
$category=$result->category;
if($result->uid!=$uid){
    $data->error="您並不是該隊聯絡人";
    echo json_encode($data);
    exit();
}
//拿選手資料
$players=array();

$sql="select * from players where tid=?";
$sth=$db->prepare($sql);
$sth->execute(array($tid));
$number=0;
while($result = $sth->fetchObject()){
    $player=new stdClass();
    $player->pid=$result->pid;
    $player->realname=$result->realname;
    $player->stu_num=$result->stu_num;
    $player->birthday=$result->birthday;
    $player->id_num=$result->id_num;
    $player->cellphone=$result->cellphone;
    $player->superman=$result->super;
    $player->foreigner=$result->foreigner;
    $player->country=$result->country;
    $player->gender=$result->gender;
    $player->passport_name=$result->passport_name;
    $player->pic_head=$result->pic_head;
    $player->pic_front=$result->pic_front;
    $player->pic_back=$result->pic_back;
    $player->pic_second=$result->pic_second;
    
    array_push($players,$player);
    $number++;
}
$data->players=$players;
$data->teamname=$teamname;
$data->category=$category;
$data->category_chinese=$attr[$category]["chinese"];
$data->tid=$tid;
$data->howmuch="應繳金額為 ".$attr[$category]["price"]."+500+(41*".$number.")+200=".($attr[$category]["price"]+500+(41*$number)+200);
  
 
echo json_encode($data);
?>
