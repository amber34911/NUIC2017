<?php



require_once("php/settings/db_connect.php");

require_once("php/settings/functions.php");

require_once("php/settings/game_attribute.php");

start_session(1209600);

$adminid=$_SESSION["adminid"];

$sql="select * from auth where admin=?";

$sth=$db->prepare($sql);

$sth->execute(array($adminid));

if($result = $sth->fetchObject()){

    $permit=$result->permit;

    $adminname=$result->adminname;

}else{

    echo "permission denied";

    exit();

}

if($permit{0}!=3){

    echo "permission denied";

    exit();

}



$category=$_GET["category"];



$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");



if(!in_array($category,$myarr)){

    echo "error";

    exit();

}



$filename=($category==""?"全部選手":($attr[$category]["chinese"])."選手").".csv";



header('Content-Type: text/csv; charset=utf-8');

header("Content-Disposition: attachment; filename=$filename");

$sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";

$sth=$db->prepare($sql);

$sth->execute(array("data",$adminname." download ".$filename,get_ip()));
remote_log("[下載資料] $adminname 下載了 $filename");
if($category==""){

    $sql="select teams.tid,teams.teamname,teams.category,players.realname,players.super,players.pic_head,month(players.birthday) bmonth,day(players.birthday) bday from teams join players on players.tid=teams.tid where teams.success=1 order by teams.tid,players.teamleader desc,players.pid";

    $sth=$db->prepare($sql);

    $sth->execute(array());

}else{

    $sql="select teams.tid,teams.teamname,teams.category,players.realname,players.super,players.pic_head,month(players.birthday) bmonth,day(players.birthday) bday from teams join players on players.tid=teams.tid where teams.category=? and teams.success=1 order by teams.tid,players.teamleader desc,players.pid";

    $sth=$db->prepare($sql);

    $sth->execute(array($category));

}



    echo "tid,隊名,項目(中文),項目(代號),姓名,體資體保,@生日,@大頭貼\n";

while($result=$sth->fetchObject()){

    echo $result->tid.",";

    echo $result->teamname.",";

    echo $attr[$result->category]["chinese"].",";

    echo $result->category.",";

    echo $result->realname.",";

    echo ($result->super?"體資生":"").",";
    
    if($result->bmonth==1 && $result->bday>15 &&$result->bday<23){
		echo "/birthday.png,";
    }else{
		echo ",";
    }

    echo "/".$result->category."/".$result->tid."/".$result->pic_head."\n";

}





/*echo mb_convert_encoding("tid,隊名,項目(中文),項目(代號),姓名,@大頭貼\n", "UTF-16", "UTF-8");;

while($result=$sth->fetchObject()){

    echo mb_convert_encoding($result->tid.",", "UTF-16", "UTF-8");

    echo mb_convert_encoding($result->teamname.",", "UTF-16", "UTF-8");

    echo mb_convert_encoding($attr[$result->category]["chinese"].",", "UTF-16", "UTF-8");

    echo mb_convert_encoding($result->category.",", "UTF-16", "UTF-8");

    echo mb_convert_encoding($result->realname.",", "UTF-16", "UTF-8");

    echo mb_convert_encoding("/".$result->category."/".$result->tid."/".$result->pic_head."\n", "UTF-16", "UTF-8");

}*/

?>