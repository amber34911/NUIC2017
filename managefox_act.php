<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/game_attribute.php");
require_once("php/settings/PHPMailerAutoload.php");
start_session(1209600);
$data = new stdClass();
$tid = $_POST["tid"];
$field = $_POST["field"];
$value = $_POST["value"];

$adminid=$_SESSION["adminid"];
$sql="select * from auth where admin=?";
$sth=$db->prepare($sql);
$sth->execute(array($adminid));
if($result = $sth->fetchObject()){
    $permit=$result->permit;
    $adminname=$result->adminname;
}else{
    $data->error.= "permission denied: invalid user";
    echo json_encode($data);
    exit();
}

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
    $uid=$result->uid;
    $teamname=$result->teamname;
    $category = $result->category;
    $success=$result->success;
    $money_num=$result->money_num;
    $data_checked=$result->data_checked;
}

# field check
$myarray = array("money_num", "data_checked", "success","delete");
$myarray2=array("匯款資料號碼","資料確認","報名確認");
if(!in_array($field, $myarray)){
    $data->error .= "error: undefined field";
    echo json_encode($data);
    exit();
}

# value check
if($field=="data_checked" || $field=="success"){
    if($value==1 || $value==0){}
    else{
        $data->error .= "error: undefined value";
    }
}
else{
    $fields = array(
        "number" => $value
    );
    check_input_fields($fields, $data);
}

if($data->error){
    echo json_encode($data);
    exit();
}
$sql = "select count(*) as success_num from teams where category=? and success=1 ";
$sth = $db->prepare($sql);
$sth->execute(array($category));
$result = $sth->fetchObject();
$success_num=$result->success_num;
if($field=="success"){
    if($value==1){
        if($money_num=="999"||$data_checked=="0"){
            $data->error .= "該隊資料或繳費尚未齊全";
            echo json_encode($data);
            exit();
        }
        if($success_num>=$attr[$category]["max_team"]){
            $data->error .= "該項目已額滿";
            echo json_encode($data);
            exit();
        }
        
    }else{
        $data->error .= "這個欄位不能取消勾選";
        echo json_encode($data);
        exit();
    }
    
}

# admin
if($permit{0}=="3"){
    
    if($field!="delete"){
		
        $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
        $sth=$db->prepare($sql);
        $sth->execute(array($field,$adminname." change ".$tid." 's ".$field." to ".$value,get_ip()));
        
        $sql = "update teams set ".$field."=? where tid=?";
        $sth=$db->prepare($sql);
        if(!$sth->execute(array($value, $tid))){
            $data->error .= "update error: database failed";
            echo json_encode($data);
            exit();
        }else{
			remote_log("[更改狀態] ".$adminname." 設定 $tid 為 報名成功");
            if($field=="success"){
                $sql = "select * from users where uid =?";
                $sth=$db->prepare($sql);
                $sth->execute(array($uid));
                $result = $sth->fetchObject();
                $target_addr=$result->email;
                $title="[2017大資盃] 報名成功通知";
                $message=$result->realname. " 您好<br>您的隊伍 ".$attr[$category]["chinese"]."-".$teamname." 已報名成功<br>請繼續關注粉絲專頁以及官方網站的公告<br><br>若有任何問題，請聯絡<br>email:<a href='mailto:nuic2017nctu@gmail.com'>nuic2017nctu@gmail.com</a><br>Facebook:<a href='https://www.facebook.com/nuic2017'>https://www.facebook.com/nuic2017</a><br><br>2017大資盃工作團隊<br><img src='https://nuic2017.com/wp-content/themes/vantage/sponsor/sponsor4.jpg'>";

                sendmail($target_addr, $title ,$message);
                
                $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
                $sth=$db->prepare($sql);
                $sth->execute(array("email",$adminname." send email to ".$target_addr." with title: ".$title." and content: ".$message,get_ip()));
            }
            $data->message="成功更新 ".$teamname." 隊的 ".$myarray2[array_search($field,$myarray)]." 至 ".$value;
            echo json_encode($data);
            exit();
        }
        
    }else{//delete team
        if($success=="1"){
            $data->error .= "該系隊已報名成功不能刪除";
            echo json_encode($data);
            exit();
        }
        
        $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
        $sth=$db->prepare($sql);
        $sth->execute(array($field,$adminname." delete ".$tid,get_ip()));
        
        
        
        //get category
        $sql = "select * from teams where tid=?";
        $sth = $db->prepare($sql);
        $sth->execute(array($tid));
        if($result = $sth->fetchObject()){
            $category=$result->category;
            $teamname=$result->teamname;
        }
        remote_log("[刪除隊伍] ".$adminname." 刪除 $category-$teamname ");
        //delete pic table
        
        $sql = "delete from pic where team=?";
        $sth = $db->prepare($sql);
        if($sth->execute(array($category."-".$tid))){
        }else{
            $data->error.="delete table picture error";
            echo json_encode($data);
            exit(); 
        }
        
        //delete player table
        $sql = "delete from players where tid=?";
        $sth = $db->prepare($sql);
        if($sth->execute(array($tid))){
        }else{
            $data->error.="delete table player error";
            echo json_encode($data);
            exit(); 
        }
        
        //delete team table
        $sql = "delete from teams where tid=?";
        $sth = $db->prepare($sql);
        if($sth->execute(array($tid))){
        }else{
            $data->error.="delete table team error";
            echo json_encode($data);
            exit(); 
        }
        //delete folder
        $escape_type = escape_directory_name($category);
        $escape_team = escape_directory_name($tid);
        $path = "$escape_type/$escape_team";
        $upload_dir = "upload/$path";
        $thumb_dir = "thumb/$path";
        rrmdir($upload_dir);
        rrmdir($thumb_dir);
        if($data->error){
            $data->error.="delete pic file error";
            echo json_encode($data);
            exit(); 
        }
        $data->message="delete ".$tid." success";
        echo json_encode($data);
        exit(); 
        
            
       
    
    
    
    
    
    
    
    
    
    }
    /*
    if($sth->rowCount()===0){
        $data->error .= "update error: database failed";
        echo json_encode($data);
        exit();
    }*/
}
# success
else if($permit{0}=="2"){
    if($field!="success"){
        $data->error .= "permission denied: invalid field update";
        echo json_encode($data);
        exit();
    }
    else{
		remote_log("[更改狀態] ".$adminname." 設定 $tid 為 資料確認");
        $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
        $sth=$db->prepare($sql);
        $sth->execute(array($field,$adminname." change ".$tid." 's ".$field." to ".$value,get_ip()));
        
        $sql = "update teams set ".$field."=? where tid=?";
        $sth=$db->prepare($sql);
        if(!$sth->execute(array($value, $tid))){
            $data->error .= "update error: database failed";
            echo json_encode($data);
            exit();
        }else{
            $data->message="成功更新 ".$teamname." 隊的 ".$myarray2[array_search($field,$myarray)]." 至 ".$value;
            echo json_encode($data);
            exit();
        }
        /*
        if($sth->rowCount()===0){
            $data->error .= "update error: database failed";
            echo json_encode($data);
            exit();
        }*/
    }
}
# 總務
else if($permit{0}=="1"){
    if($field!="money_num"){
        $data->error .= "permission denied: invalid field update";
        echo json_encode($data);
        exit();
    }
    else{
		remote_log("[更改狀態] ".$adminname." 設定 $tid 為 繳費成功");
        $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
        $sth=$db->prepare($sql);
        $sth->execute(array($field,$adminname." change ".$tid." 's ".$field." to ".$value,get_ip()));
        
        $sql = "update teams set ".$field."=? where tid=?";
        $sth=$db->prepare($sql);
        if(!$sth->execute(array($value, $tid))){
            $data->error .= "update error: database failed";
            echo json_encode($data);
            exit();
        }else{
            $data->message="成功更新 ".$teamname." 隊的 ".$myarray2[array_search($field,$myarray)]." 至 ".$value;
            echo json_encode($data);
            exit();
        }
        /*
        if($sth->rowCount()===0){
            $data->error .= "update error: database failed";
            echo json_encode($data);
            exit();
        }*/
    }
}
# 系隊
else if($permit{0}=='0'){
    $c_array=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");
    if($c_array[$permit{1}]!=$category){
        $data->error .= "permission denied: invalid category";
        echo json_encode($data);
        exit();
    }
    else{
		remote_log("[更改狀態] ".$adminname." 設定 $tid 為 資料確認");
        $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
        $sth=$db->prepare($sql);
        $sth->execute(array($field,$adminname." change ".$tid." 's ".$field." to ".$value,get_ip()));
        
        $sql = "update teams set ".$field."=? where tid=?";
        $sth=$db->prepare($sql);
        if(!$sth->execute(array($value, $tid))){
            $data->error .= "update error: database failed";
            echo json_encode($data);
            exit();
        }else{
            $data->message="成功更新 ".$teamname." 隊的 ".$myarray2[array_search($field,$myarray)]." 至 ".$value;
            echo json_encode($data);
            exit();
        }
        
        /*
        if($sth->rowCount()===0){
            $data->error .= "update error: database failed";
            echo json_encode($data);
            exit();
        }
        */
    }
}
else{
    $data->error .= "permission denied: undefined permit";
    echo json_encode($data);
    exit();
}

?>
