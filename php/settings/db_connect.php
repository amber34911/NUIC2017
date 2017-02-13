<?php

/*include this file to connect to db 
example usage:

$item = $_POST["name"];
$context = $_POST["context"];
$team = $_POST["team"];

$sql = "INSERT INTO `team_test` (name, context, team)" . " VALUES(?, ?, ?)";
$sth = $db->prepare($sql);
$sth->execute(array($item, $context, $team));
while($result = $sth->fetchObject()){
    exho $result->name
}
*/

$db_host = "localhost";//資料庫位址
$db_name = "";//資料庫名稱
$db_user = "";//使用者名稱
$db_password = "";//使用者密碼
$dsn = "mysql:host=$db_host;dbname=$db_name";
$db = new PDO($dsn, $db_user, $db_password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));



?>
