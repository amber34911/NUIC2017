<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/userdb.php");
start_session(1209600);
$data = new stdClass();


$adminid=$_SESSION["adminid"];
$sql="select * from auth where admin=?";
$sth=$db->prepare($sql);
$sth->execute(array($adminid));
if($result = $sth->fetchObject()){
    $permit=$result->permit;
    $adminname=$result->adminname;
    /*
    if($permit{0}!=3){
        $data->error.= "permission denied: invalid user";
        echo json_encode($data);
        exit();
    }*/
    
}else{
    $data->error.= "permission denied: invalid user";
    echo json_encode($data);
    exit();
}
//get player info
$pid=$_POST["pid"];
$sql="select * from players where pid=?";
$sth=$db->prepare($sql);
$sth->execute(array($pid));
$result = $sth->fetchObject();

$category=$result->category;
$tid=$result->tid;
$pic_head = $result->pic_head;


$sql="select * from teams where tid=?";
$sth=$db->prepare($sql);
$sth->execute(array($tid));
$result = $sth->fetchObject();

$category=$result->category;

// File and angle
$filename = "./upload/".$category."/".$tid."/".$pic_head;
$angle = 90;

header('Content-type: image/jpeg');

// Load
$source = imagecreatefromjpeg($filename);

// Rotate
$rotate = imagerotate($source, $angle, 0);

// Output
imagejpeg($rotate, $filename);

// Free memory
imagedestroy($source);
imagedestroy($rotate);


//thumb
$filename = "./thumb/".$category."/".$tid."/".$pic_head;

// Load
$source = imagecreatefromjpeg($filename);

// Rotate
$rotate = imagerotate($source, $angle, 0);

// Output
imagejpeg($rotate, $filename);

// Free memory
imagedestroy($source);
imagedestroy($rotate);

$data->message = $filename;

echo json_encode($data);
exit();
?>
