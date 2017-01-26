<?php

require_once("php/settings/db_connect.php");

require_once("php/settings/functions.php");

require_once("php/settings/game_attribute.php");
require_once("php/settings/PHPMailerAutoload.php");
start_session(1209600);

$data=new stdClass();
$success_counter=0;
$error_counter=0;


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

if($permit{0}!=3){

    $data->error.= "permission denied";

    echo json_encode($data);

    exit();

}





$category=$_POST['category'];

$content=$_POST['content'];

$title=$_POST["title"];





# category check

$c_array=array("b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");

if(in_array($category,$c_array)){
	
    $sql = "select * from users join teams on teams.uid=users.uid where teams.category=? and success=1 ";

    $sth = $db->prepare($sql);

    $sth->execute(array($category));
	$mail_counter=0;
    while($result = $sth->fetchObject()){
		$mail_counter++;
		if($mail_counter%30==29){
			sleep(10);
		}
        $uid=$result->uid;

        $tid=$result->tid;

        $target_addr=$result->email;

        $target_user=$result->realname;

        $target_category=$attr[$result->category]["chinese"];

        $target_teamname=$result->teamname;

        $full_teamname=$target_category."-".$target_teamname;

        //$title="[2017大資盃] ".$full_teamname." 第二次領隊會議通知";

        
        $header=$full_teamname." ".$target_user." 您好:<br> ";
		
        $message = $header.nl2br($content);

        $message=$message."<br>若有任何問題，請聯絡<br>email:<a href='mailto:nuic2017nctu@gmail.com'>nuic2017nctu@gmail.com</a><br>Facebook:<a href='https://www.facebook.com/nuic2017'>https://www.facebook.com/nuic2017</a><br><br>2017大資盃工作團隊<br><img src='https://nuic2017.com/wp-content/themes/vantage/sponsor/sponsor4.jpg'>";

        

        //$data->message.= $title."\n".$message;

        

        
		
        if(sendmail($target_addr, $title ,$message)){
			
            $sql2 = "insert into auth_log(eventtype,state,ip) values(?,?,?)";

            $sth2=$db->prepare($sql2);

            $sth2->execute(array("email",$adminname." send email to ".$target_addr." with title: ".$title." and content: ".$message,get_ip()));

            

            $sql2 = "insert into email_log(adminname,tid,uid,email,title,message) values(?,?,?,?,?,?)";

            $sth2 = $db->prepare($sql2);

            $sth2->execute(array($adminname,$tid,$uid,$target_addr,$title,$message));
			


            $data->message.="信件已成功寄出 ".$full_teamname."\n";
			$success_counter+=1;


        }else{
			$error_counter+=1;
            $data->error.="error: failed mail sending to ".$full_teamname."\n";
		}
	
    }
	$data->message.="success:".(string)$success_counter."\n";
	$data->message.="error:".(string)$error_counter."\n";
	$data->message.="total:".(string)($error_counter+$success_counter)."\n";
	$data->error.="success:".(string)$success_counter."\n";
	$data->error.="error:".(string)$error_counter."\n";
	$data->error.="total:".(string)($error_counter+$success_counter)."\n";
	
    echo json_encode($data);

}

else{

    $data->error .= "permission denied: invalid category";

    echo json_encode($data);

    exit();

}

?>

