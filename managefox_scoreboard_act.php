<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/userdb.php");
require_once("php/settings/game_attribute.php");
start_session(1209600);
$data = new stdClass();


$adminid=$_SESSION["adminid"];
$sql="select * from auth where admin=?";
$sth=$db->prepare($sql);
$sth->execute(array($adminid));
if($result = $sth->fetchObject()){
    $permit=$result->permit;
    $adminname=$result->adminname;
}else{
    $data->error.= "permission denied: invalid user";
    echo json_encode($data);
    exit();
}

// get type
$type=$_POST["type"];
if($type !="start" && $type!="end") {
    $data->error.= "permission denied: invalid type";
    echo json_encode($data);
    exit();
}

$now_str=date_format($now,"Y-m-d H:i:s");

// get tournament id
$id=$_POST["id"];

// get details of the match
$sql="SELECT * FROM tournament WHERE `id`=?";
$sth=$db->prepare($sql);
$sth->execute(array($id));
$result=$sth->fetchObject();
$category=$result->category;
$start_time=$result->start_time;
$end_time=$result->end_time;

// check whether the user can edit this match (admin can edit all; others can edit only their own category)
$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");
if($permit{0}!=3 && substr($category,-3)!=substr($myarr{$permit{1}},-3)){
    $data->error.= "permission denied: invalid category";
    echo json_encode($data);
    exit();
}

// start act
if($type == "start") {
    // check whether the tournament's end time had ben updated
    if ($start_time != null){
        $data->error.="Update $id failed. This match had been updated.";
        echo json_encode($data);
        exit();
    }
    $sql="UPDATE tournament SET `start_time`=? WHERE `id`=?";
    $sth=$db->prepare($sql);
    if($sth->execute(array($now_str, $id))){
        $data->message ="Update $id successfully.";
    }else{
        $data->error.= "Update $id failed.";
        echo json_encode($data);
        exit();
        
    }
}
// end act
else if ($type == "end"){
    // check whether the tournament's end time had ben updated
    if ($end_time != null){
        $data->error.="Update $id failed. This match had been updated.";
        echo json_encode($data);
        exit();
    }
    // check whether the input is valid(integer only)
    $scoreA=$_POST["scoreA"];
    $scoreB=$_POST["scoreB"];
    if (!(is_int($scoreA) || ctype_digit($scoreA)) || !(is_numeric($scoreB) || ctype_digit($scoreB))){
        $data->error.="Update $id failed. Invalid input.";
        echo json_encode($data);
        exit();
    }


    $sql="UPDATE tournament SET `end_time`=?, `scoreA`=?, `scoreB`=? WHERE `id`=?";
    $sth=$db->prepare($sql);
    if($sth->execute(array($now_str, $scoreA, $scoreB, $id))){
        $sql="select * from tournament where id = ?";
        $sth=$db->prepare($sql);
        $sth->execute(array($id));
        $result= $sth->fetchObject();
        if($scoreA<$scoreB){
            $loser=$result->teamA;
            $winner=$result->teamB;
        }else{
            $loser=$result->teamB;
            $winner=$result->teamA;
        }
        $winner_go=$result->winner_go;
        $loser_go=$result->loser_go;
        //winner
        if($winner_go!=0){
            $sql="select * from tournament where number = ? and round!=0 and category=?";
            $sth=$db->prepare($sql);
            $sth->execute(array($winner_go,$category));
            $result= $sth->fetchObject();
            if($result->teamA==""){
                $sql="UPDATE tournament SET `teamA`=? WHERE number = ? and round!=0 and category=?";
            }else{
                $sql="UPDATE tournament SET `teamB`=? WHERE number = ? and round!=0 and category=?";
            }
            $sth=$db->prepare($sql);
            $sth->execute(array($winner,$winner_go,$category));
        }
        //loser
        if($loser_go!=0){
            $sql="select * from tournament where number = ? and round!=0 and category=?";
            $sth=$db->prepare($sql);
            $sth->execute(array($loser_go,$category));
            $result= $sth->fetchObject();
            if($result->teamA==""){
                $sql="UPDATE tournament SET `teamA`=? WHERE number = ? and round!=0 and category=?";
            }else{
                $sql="UPDATE tournament SET `teamB`=? WHERE number = ? and round!=0 and category=?";
            }
            $sth=$db->prepare($sql);
            $sth->execute(array($loser,$loser_go,$category));
        }
        $data->message ="Update $id successfully";
    }else{
        $data->error.= "Update $id failed";
        echo json_encode($data);
        exit();
    }
}


echo json_encode($data);
exit();
