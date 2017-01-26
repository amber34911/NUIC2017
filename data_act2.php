<?php



require_once("php/settings/db_connect.php");

require_once("php/settings/functions.php");

require_once("php/settings/game_attribute.php");





$category=$_GET["category"];

if(isset($_GET["cols"])){

    $cols=$_GET["cols"];

}else{

    $cols=8;

}





$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");



if(!in_array($category,$myarr)){

    echo "error";

    exit();

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Document</title>

    <style>

        table{

            border-collapse: collapse;

        }

        td{

            border:1px solid black;

            min-width:50px;

            

        }

    </style>

</head>

<body>

    <h2><?php echo $attr[$category]["chinese"];?></h2>

<?php



$teamname="";

//$filename=($category==""?"全部選手":($attr[$category]["chinese"])."選手").".csv";



//header('Content-Type: text/csv; charset=utf-8');

//header("Content-Disposition: attachment; filename=$filename");

//$sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";

//$sth=$db->prepare($sql);

//$sth->execute(array("data",$adminname." download ".$filename,get_ip()));

if($category==""){

    $sql="select tid,teamname from teams  where teams.success=1 order by teams.tid";

    $sth=$db->prepare($sql);

    $sth->execute(array());

}else{

    $sql="select tid,teamname from teams where teams.category=? and teams.success=1 order by teams.tid";

    $sth=$db->prepare($sql);

    $sth->execute(array($category));

}
while($result=$sth->fetchObject()){
	echo $result->teamname."<br>";
	$sql2="select realname,super from players where tid=? order by teamleader desc ,pid asc";

    $sth2=$db->prepare($sql2);

    $sth2->execute(array($result->tid));
    $players=array();
    while($result2=$sth2->fetchObject()){
		array_push($players,$result2->realname.($result2->super==1?"*":""));
    }
    echo join("｜",$players);
    echo "<br>";

}






?>

</body>

</html>