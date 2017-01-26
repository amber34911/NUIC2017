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



$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof");



if(!in_array($category,$myarr)){

    echo "error";

    exit();

}



$filename=($category==""?"全部選手":($attr[$category]["chinese"])."選手")."保險資料.csv";

header("Content-type: text/x-csv");

header("Content-Disposition: attachment; filename=$filename");

$sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";

$sth=$db->prepare($sql);

$sth->execute(array("insurance",$adminname." download ".$filename,get_ip()));
remote_log("[下載資料] $adminname 下載了 $filename");
if($category==""){

    $sql="select players.* from players join teams on players.tid=teams.tid where teams.success=1 order by teams.tid";

    $sth=$db->prepare($sql);

    $sth->execute(array());

}else{

    $sql="select players.* from players join teams on players.tid=teams.tid where teams.success=1 and teams.category=? order by teams.tid";

    $sth=$db->prepare($sql);

    $sth->execute(array($category));

}

echo "被保險人姓名,被保險人身分證統一編號,被保險人出生年月日,主約投保保額,身故受益人姓名,身故受益人與被保險人關係,被保險人國籍,被保險人性別代碼\n";

while($result=$sth->fetchObject()){

    echo ($result->foreigner?$result->passport_name:$result->realname).",";

    echo $result->id_num.",";

    $date=date_create($result->birthday);

    echo (date_format($date,"Y"))."/".date_format($date,"m")."/".date_format($date,"d").",";

    echo "100,";

    echo ",";

    echo ",";

    echo ($result->foreigner?$result->country:"").",";

    echo strtoupper($result->foreigner?$result->gender:"")."\n";

}

?>