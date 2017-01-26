<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/game_attribute.php");
$data=new stdClass();
start_session(1209600);
$category=$_POST["category"];
$round=$_POST["round"];
$myarr=array("b_bas","g_bas","b_vol","g_vol","tab","bad","sof");
if(!in_array($category,$myarr)){
    $data->error="category not found";
    echo json_encode($data);
    exit();
}

$results=array();
$sql="SELECT teamA,teamB,scoreA,scoreB FROM tournament WHERE round=? and category=? and scoreA IS NOT NULL and scoreB IS NOT NULL ORDER BY teamA";
$sth=$db->prepare($sql);
$sth->execute(array($round,$category));

while($result=$sth->fetchObject()){
    $match=new stdClass();
    $match->teamA=$result->teamA;
    $match->teamB=$result->teamB;
    $match->scoreA=$result->scoreA;
    $match->scoreB=$result->scoreB;
    array_push($results, $match);
}
$data->results=$results;
echo json_encode($data);
exit();

