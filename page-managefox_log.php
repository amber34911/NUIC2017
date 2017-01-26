<?php
require_once( "php/settings/db_connect.php");
require_once( "php/settings/functions.php");
require_once( "php/settings/game_attribute.php");
testing();
start_session(1209600);

//get_header();


/*
first digit = 0(系隊)second digit 1~7("b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc")
07->系壘
first digit = 1(總務)
first digit = 2(success)
first digit = 3(管理員)
 */
$adminid=$_SESSION["adminid"];
$sql="select * from auth where admin=?";
$sth=$db->prepare($sql);
$sth->execute(array($adminid));
if($result = $sth->fetchObject()){
    $permit=$result->permit;
}else{
    echo "permission denied";
    exit();
}

$category=$_GET["category"];

$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");

if(!in_array($category,$myarr)){
    echo "error";
    exit();
}
$request_cat= array_search($category,$myarr);
//echo $request_cat;
//
?>

<!DOCTYPE html>
<html lang="zh-tw">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />
    <title>email log</title>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
    <style>
       
        table{
            border-collapse: collapse;
            
        }
        tr:nth-child(even){
            background-color:#eee;
        }
        tr:hover{
            background-color:#ddd;
        }
        td,th{
            border:1px solid rgba(0,0,0,0.3);
            padding:2px 20px 2px 2px;
            vertical-align:middle;
        }
        th{
            text-align:left;
            background-repeat: no-repeat;
            background-position: center right;
            cursor: pointer;
        }
        
    </style>
</head>
<body>
    <h2><a href="../managefox">返回</a></h2>
    <br>
    <table>
    <thead><tr>
        <th>寄件人</th>
        <th>收件隊伍</th>
        <th>收件信箱</th>
        <th>信件標題</th>
        <th>信件內容</th>
        <th>寄件時間</th>
    </tr></thead>
    <tbody>
<?php
$sql="select * from email_log order by timestamp desc limit 20";
$sth=$db->prepare($sql);
$sth->execute(array(1));

while($result = $sth->fetchObject()){
    $sql_team = "select * from teams where tid=?";
    $sth_team = $db->prepare($sql_team);
    $sth_team->execute(array($result->tid));
    if($team = $sth_team->fetchObject()){
?>
    <tr>
        <td><?php echo $result->adminname; ?></td>
        <td><?php echo $team->teamname; ?></td>
        <td><?php echo $result->email; ?></td>
        <td><?php echo $result->title; ?></td>
        <td><?php echo $result->message; ?></td>
        <td><?php echo $result->timestamp; ?></td>
    </tr>
<?php
    }
}

?>
    </tbody>
    
</body>
</html>
