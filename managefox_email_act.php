<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/PHPMailerAutoload.php");
start_session(1209600);
$data=new stdClass();

# admin check
$adminid = $_SESSION["adminid"];
$sql="select * from auth where admin=?";
$sth=$db->prepare($sql);
$sth->execute(array($adminid));
if($result = $sth->fetchObject()){
    $permit = $result->permit;
    $adminname = $result->adminname;
}else{
    $data->error.= "permission denied: invalid user";
    echo json_encode($data);
    exit();
}


$title=$_POST['title'];
$tid=$_POST['tid'];
$header=$_POST['header'];
$content=$_POST['content'];
$footer=$_POST['footer'];

 # tid check
$sql = "select * from teams where tid=? ";
$sth = $db->prepare($sql);
$sth->execute(array($tid));
$result = $sth->fetchObject();
if($result==false){
    $data->error .= "error: undefined tid";
    echo json_encode($data);
    exit();
}
else{
    $category = $result->category;
    $uid = $result->uid;
}

# category check
$c_array=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");

if($permit{0}=="3" ||$permit{0}=="1"||$permit{0}=="2"|| ($permit{0}=="0" && $c_array[$permit{1}]==$category)){
    $sql = "select * from users where uid=? ";
    $sth = $db->prepare($sql);
    $sth->execute(array($uid));
    if($result = $sth->fetchObject()){
        $target_addr=$result->email;
        $school=$result->school;
        $department=$result->department;
        $realname=$result->realname;
		remote_log("[寄送郵件] ".$adminname." 寄信給 $category-$school $department $realname");
        
        $message = nl2br($header ."\n". $content ."\n". $footer);
        
        
        
        if(sendmail($target_addr, $title ,$message)){
            $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
            $sth=$db->prepare($sql);
            $sth->execute(array("email",$adminname." send email to ".$target_addr." with title: ".$title." and content: ".$message,get_ip()));
            
            $sql = "insert into email_log(adminname,tid,uid,email,title,message) values(?,?,?,?,?,?)";
            $sth = $db->prepare($sql);
            $sth->execute(array($adminname,$tid,$uid,$target_addr,$title,$message));
                 
            $data->message="信件已成功寄出";
            echo json_encode($data);
            
            exit();

        }else{
            $data->error="error: failed mail sending";
            echo json_encode($data);
            exit();
        }
    }
    else{
        $data->error .= "error: uid not found";
        echo json_encode($data);
        exit();
    }
}
else{
    $data->error .= "permission denied: invalid permit";
    echo json_encode($data);
    exit();
}
?>
