<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/game_attribute.php");
start_session(1209600);
$data = new stdClass();


$adminid=$_SESSION["adminid"];
$sql="select * from auth where admin=?";
$sth=$db->prepare($sql);
$sth->execute(array($adminid));
if($result = $sth->fetchObject()){
    $permit=$result->permit;
    $adminname=$result->adminname;
    if($permit{0}!=3){
        $data->error.= "permission denied: invalid user";
        echo json_encode($data);
        exit();
    }
}else{
    $data->error.= "permission denied: invalid user";
    echo json_encode($data);
    exit();
}
//get team info
$tid=$_POST["tid"];
$sql="select * from teams where tid=?";
$sth=$db->prepare($sql);
$sth->execute(array($tid));
$result = $sth->fetchObject();
$category=$result->category;

$path = "$category/$tid/";
$upload_dir = "upload/$path";
$thumb_dir = "thumb/$path";

/*
$data->message.=var_export($_POST["pid"],true);
$data->message.=var_export($_POST["realname"],true);
$data->message.=var_export($_POST["realname_change"],true);
echo json_encode($data);
*/
$num=count($_POST["pid"]);
$count_head=0;
$count_front=0;
$count_back=0;
$count_second=0;


for($i=0;$i<$num;$i++){
    //echo "player".($i+1)." ";
    //get hashed file name
    $sql="select * from players where pid=?";
    $sth=$db->prepare($sql);
    $sth->execute(array($_POST["pid"][$i]));
    $result = $sth->fetchObject();
    $hashed_head=$result->pic_head;
    $hashed_front=$result->pic_front;
    $hashed_back=$result->pic_back;
    $hashed_second=$result->pic_second;
    /*
    //姓名
    if($_POST["realname_change"][$i]==1){
        //echo $_POST["realname"][$i].",";
    }
    //學號
    if($_POST["stu_num_change"][$i]==1){
        //echo $_POST["stu_num"][$i].",";
    }
    //生日
    if($_POST["birthday_change"][$i]==1){
        //echo $_POST["birthday"][$i].",";
    }
    //身分證字號
    if($_POST["id_num_change"][$i]==1){
        //echo $_POST["id_num"][$i].",";
    }
    //手機
    if($_POST["cellphone_change"][$i]==1){
        //echo $_POST["cellphone"][$i].",";
    }
    //體資
    if($_POST["super_change"][$i]==1){
        //echo $_POST["super"][$i].",";
    }
    //外籍
    if($_POST["foreign_change"][$i]==1){
        //echo $_POST["foreign"][$i].",";
    }
    //國籍
    if($_POST["country_change"][$i]==1){
        //echo $_POST["country"][$i].",";
    }
    //性別
    if($_POST["gender_change"][$i]==1){
        //echo $_POST["gender"][$i].",";
    }
    //護照姓名
    if($_POST["passport_name_change"][$i]==1){
        //echo $_POST["passport_name"][$i].",";
    }
    */
    $sql = "update players set realname=?,stu_num=?,birthday=?,id_num=?,cellphone=?,super=?,foreigner=?,country=?,gender=?,passport_name=? where pid=?";
    $sth=$db->prepare($sql);
    if(!$sth->execute(array($_POST["realname"][$i],$_POST["stu_num"][$i],$_POST["birthday"][$i],$_POST["id_num"][$i],$_POST["cellphone"][$i],$_POST["super"][$i],$_POST["foreign"][$i],$_POST["country"][$i],$_POST["gender"][$i],$_POST["passport_name"][$i], $_POST["pid"][$i]))){
        $data->error .= "update error: database failed";
        echo json_encode($data);
        exit();
    }
    
    //大頭貼
    if($_POST["pic_head_change"][$i]==1){
        //echo $_FILES["pic_head"]["name"][$count_head].",";
        if ($_FILES["pic_head"]["error"][$count_head] > 0){
            $data->error= "Error: " . $_FILES["pic_head"]["error"][$count_head];
            echo json_encode($data);
            exit();
        }
        $type = extract_file_type($_FILES["pic_head"]["tmp_name"][$count_head]);
        if(preg_match('/image\/([Jj][Pp][Ee]?[Gg])/',$type)==0){
            //echo "檔案格式錯誤！" . "<br />";   
            $data->error .= "錯誤: 違法的檔案格式 on player ".($i+1)."head ($type)";
            echo json_encode($data);
            exit();
        }
        create_thumbnail($_FILES["pic_head"]["tmp_name"][$count_head],$upload_dir.$hashed_head,1200);
        create_thumbnail($_FILES["pic_head"]["tmp_name"][$count_head],$thumb_dir.$hashed_head,200);
        $count_head++;
    }
    //學生證正面
    if($_POST["pic_front_change"][$i]==1){
        //echo $_FILES["pic_front"]["name"][$count_front].",";
        if ($_FILES["pic_front"]["error"][$count_front] > 0){
            $data->error= "Error: " . $_FILES["pic_front"]["error"][$count_front];
            echo json_encode($data);
            exit();
        }
        $type = extract_file_type($_FILES["pic_front"]["tmp_name"][$count_front]);
        if(preg_match('/image\/([Jj][Pp][Ee]?[Gg])/',$type)==0){
            //echo "檔案格式錯誤！" . "<br />";   
            $data->error .= "錯誤: 違法的檔案格式 on player ".($i+1)."front ($type)";
            echo json_encode($data);
            exit();
        }
        create_thumbnail($_FILES["pic_front"]["tmp_name"][$count_front],$upload_dir.$hashed_front,1200);
        create_thumbnail($_FILES["pic_front"]["tmp_name"][$count_front],$thumb_dir.$hashed_front,200);
        $count_front++;
        
    }
    //學生證反面
    if($_POST["pic_back_change"][$i]==1){
        //echo $_FILES["pic_back"]["name"][$count_back].",";
        if ($_FILES["pic_back"]["error"][$count_back] > 0){
            $data->error= "Error: " . $_FILES["pic_back"]["error"][$count_back];
            echo json_encode($data);
            exit();
        }
        $type = extract_file_type($_FILES["pic_back"]["tmp_name"][$count_back]);
        if(preg_match('/image\/([Jj][Pp][Ee]?[Gg])/',$type)==0){
            //echo "檔案格式錯誤！" . "<br />";   
            $data->error .= "錯誤: 違法的檔案格式 on player ".($i+1)."back ($type)";
            echo json_encode($data);
            exit();
        }
        create_thumbnail($_FILES["pic_back"]["tmp_name"][$count_back],$upload_dir.$hashed_back,1200);
        create_thumbnail($_FILES["pic_back"]["tmp_name"][$count_back],$thumb_dir.$hashed_back,200);
        $count_back++;
    }
    //第二證件正面
    if($_POST["pic_second_change"][$i]==1){
        //echo $_FILES["pic_second"]["name"][$count_second].",";
        if ($_FILES["pic_second"]["error"][$count_second] > 0){
            $data->error= "Error: " . $_FILES["pic_second"]["error"][$count_second];
            echo json_encode($data);
            exit();
        }
        $type = extract_file_type($_FILES["pic_second"]["tmp_name"][$count_second]);
        if(preg_match('/image\/([Jj][Pp][Ee]?[Gg])/',$type)==0){
            //echo "檔案格式錯誤！" . "<br />";   
            $data->error .= "錯誤: 違法的檔案格式 on player ".($i+1)."second ($type)";
            echo json_encode($data);
            exit();
        }
        create_thumbnail($_FILES["pic_second"]["tmp_name"][$count_second],$upload_dir.$hashed_second,1200);
        create_thumbnail($_FILES["pic_second"]["tmp_name"][$count_second],$thumb_dir.$hashed_second,200);
        $count_second++;
    }
    //echo "\n";
}
$data->message= "update successfully";
echo json_encode($data);
?>