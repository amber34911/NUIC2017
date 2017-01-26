<?php
//header('Content-Type: application/json; charset=utf-8');
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/game_attribute.php");
start_session(1209600);
$uid=$_SESSION["uid"];
$action=$_POST["action"];
$tid=$_POST["tid"];
$money_inf=$_POST["money_inf"];

$data=new stdClass();
// 檢查POST欄位

$fields = array(
   "team_id" => $tid,
   "addinf_action" => $action,
   "money_inf" => $money_inf
);
check_input_fields($fields,$data);
if($data->error){
    $sql="select * from teams where tid=?";
    $sth=$db->prepare($sql);
    $sth->execute(array($tid));
    $result = $sth->fetchObject();
    $data->ori_money_inf=$result->money_inf;
    echo json_encode($data);
    exit();
}

# check tid is associated with uid
$sql = "select uid from teams where tid=?";
$sth = $db->prepare($sql);
$sth->execute(array($tid));
$result = $sth->fetchObject();
if($result->uid!=$uid){
    $data->error .= "非法的隊伍 請從官方網站更新匯款資訊";
    echo json_encode($data);
    exit();
}


$teams=array();


if($action=="init"){
    $sql="select * from teams where uid=?";
    $sth=$db->prepare($sql);
    $sth->execute(array($uid));
    while($result = $sth->fetchObject()){
        $team=new stdClass();
        $team->uid=$result->uid;
        $team->tid=$result->tid;
        $team->category=$result->category;
        $team->teamname=$result->teamname;
        $team->money_inf=$result->money_inf;
        $team->money_num=$result->money_num;
        $team->timestamp=$result->timestamp;
        array_push($teams,$team);
    }
    $data->teams=$teams;
    
}else if($action="update"){
    $sql = "select * from money_inf_pic where hashed_name=?";
    $sth=$db->prepare($sql);
    $sth->execute(array($money_inf));
    if($result=$sth->fetchObject()){
        $sql = "UPDATE teams SET money_inf = ? ,money_inf_timestamp=CURRENT_TIME() WHERE tid = ? ";
        $sth=$db->prepare($sql);
        $sth->execute(array($money_inf,$tid));

        $sql = "select * from teams where tid=?";
        $sth=$db->prepare($sql);
        $sth->execute(array($tid));
        if($result=$sth->fetchObject()){
            if($result->money_inf==$money_inf){
                $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
                $sth=$db->prepare($sql);
                $sth->execute(array("edit money inf",$tid." 's money_inf was changed to ".$money_inf,get_ip()));
                				remote_log("[匯款成功] User:".$uid." 成功更新匯款資訊(tid=".$tid.")");
                $data->message="成功更新匯款資訊 ";
                $data->message.=$attr[$result->category]["chinese"]." - ";
                $data->message.=$result->teamname." - ";
                $data->message.=$result->money_inf;
            }
        }
    }else{
        $data->error="找不到代碼，請選取圖片後按下產生代碼並複製";
        $sql="select * from teams where tid=?";
        $sth=$db->prepare($sql);
        $sth->execute(array($tid));
        $result = $sth->fetchObject();
        $data->ori_money_inf=$result->money_inf;
        echo json_encode($data);
        exit();
    }
}
 
echo json_encode($data);
?>
