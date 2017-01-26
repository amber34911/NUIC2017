<?php
require_once( "php/settings/db_connect.php");
require_once( "php/settings/functions.php");
require_once( "php/settings/game_attribute.php");
testing();
start_session(1209600);
$adminid=$_SESSION["adminid"];
$sql="select * from auth where admin=?";
$sth=$db->prepare($sql);
$sth->execute(array($adminid));
if($result = $sth->fetchObject()){
    $permit=$result->permit;
    if($permit{0}!=3){
        echo "permission denied";
        exit();
    }
}else{
    echo "permission denied";
    exit();
}
$page=$_GET["myid"];
if($page==""||$page<0){
    $page=0;
}
$offset=$page*100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>view log</title>
</head>
<style>
    table{
        width:100%;
        border-collapse:collapse;
        table-layout:fixed;
        
    }
    tr:nth-child(even){
        background-color: #dddddd;
    }
    tr:hover{
        background-color: #bbbbbb;
    }
    th:nth-child(1){
        width:10%;
    }
    th:nth-child(2){
        width:70%;
    }
    th:nth-child(3){
        width:10%;
    }
    th:nth-child(4){
        width:10%;
    }
    td{
        overflow:hidden; 
        white-space:nowrap;
        text-overflow: ellipsis; 
    }
    a{
        font-size:28px;
    }
    .center{
        text-align:center;
        font-size:28px;
        display: block
    }
    .left{
        float:left;
    }
    .right{
        float:right;
    }
    .clear{
        clear:both;
        
    }
</style>
<body>
    <div><a href="../managefox">返回</a></div>
    <div>
        <a href="../managefox_viewlog/?myid=<?php echo $page-1;?>" class="left">&lt;&lt;</a>
        <a href="../managefox_viewlog/?myid=<?php echo $page+1;?>" class="right">&gt;&gt;</a>
        <span class="center"><?php echo $page;?></span>
    </div>
    <div class="clear"></div>
    <table>
        <th>eventtype</th>
        <th>state</th>
        <th>ip</th>
        <th>timestamp</th>
        <?php
        $sql="SELECT * FROM `auth_log` ORDER BY `auth_log`.`timestamp` DESC LIMIT $offset,100" ;
        $sth=$db->prepare($sql);
        $sth->execute(array());
        while($result = $sth->fetchObject()):?>
        <tr>
            <td><?php echo $result->eventtype;?></td>
            <td><?php echo htmlspecialchars($result->state, ENT_QUOTES, 'UTF-8');?></td>
            <td><?php echo $result->ip;?></td>
            <td><?php echo $result->timestamp;?></td>
        </tr>   
        <?php endwhile;?>
    </table>
</body>
</html>