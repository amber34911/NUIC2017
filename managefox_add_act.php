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
$reg_player=$result->reg_player;
$path = "$category/$tid/";
$upload_dir = "upload/$path";
$thumb_dir = "thumb/$path";

if($_POST["foreign"]==1){
        $passport_name=$_POST["passport_name"];
        $country=$_POST["country"];
        $gender=$_POST["gender"];
        if(!preg_match('/\S/', $passport_name)){
            $data->error.="無法辨識的'護照上姓名'欄位\n";
        }
        if(!preg_match('/\S/',$country)){
            $data->error.="無法辨識的'國籍'欄位\n";
        }
        if(!preg_match('/\S/',$gender)){
            $data->error.="無法辨識的'性別'欄位\n";
        }
        $fields = array(
           "date" => $_POST["birthday"],

        );
        check_input_fields($fields,$data);
    }else{
        $passport_name="";
        $country="";
        $gender="";
        $fields = array(
           "date" => $_POST["birthday"],
           "local_id_num" => $_POST["id_num"]
        );
        check_input_fields($fields,$data);
    }

if($data->error){
    echo json_encode($data);
    exit();
}
if($_FILES["pic_head"]["error"]){
    $data->error="file error";
    echo json_encode($data);
    exit();
}else{
    $type = extract_file_type($_FILES["pic_head"]["tmp_name"]);
    $filename = $_FILES["pic_head"]["name"]; 
    if(preg_match('/image\/([Jj][Pp][Ee]?[Gg])/',$type)==0){

        $data->error .= "違法的檔案格式($type)"; 
        echo json_encode($data);
		exit();
    }
    $new_name = picture_name_hash($category,$tid,$_POST["id_num"],$_POST["realname"],"head");
    make_picture_entry($db,"$category-$tid",$filename,$new_name);
    @mkdir($upload_dir,0777,true);
    @mkdir($thumb_dir,0777,true);
    create_thumbnail($_FILES["pic_head"]["tmp_name"],$upload_dir.$new_name,1200);
    create_thumbnail($_FILES["pic_head"]["tmp_name"],$thumb_dir.$new_name,200);
    $name_head=$new_name;
}
if($_FILES["pic_front"]["error"]){
    $data->error="file error";
    echo json_encode($data);
    exit();
}else{
    $type = extract_file_type($_FILES["pic_front"]["tmp_name"]);
    $filename = $_FILES["pic_front"]["name"]; 
    if(preg_match('/image\/([Jj][Pp][Ee]?[Gg])/',$type)==0){

        $data->error .= "違法的檔案格式($type)"; 
        echo json_encode($data);
		exit();
    }
    $new_name = picture_name_hash($category,$tid,$_POST["id_num"],$_POST["realname"],"front");
    make_picture_entry($db,"$category-$tid",$filename,$new_name);
    @mkdir($upload_dir,0777,true);
    @mkdir($thumb_dir,0777,true);
    create_thumbnail($_FILES["pic_front"]["tmp_name"],$upload_dir.$new_name,1200);
    create_thumbnail($_FILES["pic_front"]["tmp_name"],$thumb_dir.$new_name,200);
    $name_front=$new_name;
}
if($_FILES["pic_back"]["error"]){
    $data->error="file error";
    echo json_encode($data);
    exit();
}else{
    $type = extract_file_type($_FILES["pic_back"]["tmp_name"]);
    $filename = $_FILES["pic_back"]["name"]; 
    if(preg_match('/image\/([Jj][Pp][Ee]?[Gg])/',$type)==0){

        $data->error .= "違法的檔案格式($type)"; 
        echo json_encode($data);
		exit();
    }
    $new_name = picture_name_hash($category,$tid,$_POST["id_num"],$_POST["realname"],"back");
    make_picture_entry($db,"$category-$tid",$filename,$new_name);
    @mkdir($upload_dir,0777,true);
    @mkdir($thumb_dir,0777,true);
    create_thumbnail($_FILES["pic_back"]["tmp_name"],$upload_dir.$new_name,1200);
    create_thumbnail($_FILES["pic_back"]["tmp_name"],$thumb_dir.$new_name,200);
    $name_back=$new_name;
}
if($_FILES["pic_second"]["error"]){
    $data->error="file error";
    echo json_encode($data);
    exit();
}else{
    $type = extract_file_type($_FILES["pic_second"]["tmp_name"]);
    $filename = $_FILES["pic_second"]["name"]; 
    if(preg_match('/image\/([Jj][Pp][Ee]?[Gg])/',$type)==0){

        $data->error .= "違法的檔案格式($type)"; 
        echo json_encode($data);
		exit();
    }
    $new_name = picture_name_hash($category,$tid,$_POST["id_num"],$_POST["realname"],"second");
    make_picture_entry($db,"$category-$tid",$filename,$new_name);
    @mkdir($upload_dir,0777,true);
    @mkdir($thumb_dir,0777,true);
    create_thumbnail($_FILES["pic_second"]["tmp_name"],$upload_dir.$new_name,1200);
    create_thumbnail($_FILES["pic_second"]["tmp_name"],$thumb_dir.$new_name,200);
    $name_second=$new_name;
}
$sql = "insert into players(tid,realname,stu_num,birthday,id_num,cellphone,super,foreigner,passport_name,country,gender,teamleader,pic_head,pic_front,pic_back,pic_second) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$sth = $db->prepare($sql);
$sth->execute(
array($_POST["tid"],$_POST["realname"],$_POST["stu_num"],$_POST["birthday"],$_POST["id_num"],$_POST["cellphone"],$_POST["super"],$_POST["foreign"],$passport_name,$country,$gender,0,$name_head,$name_front,$name_back,$name_second)
);
remote_log("[新增選手] ".$adminname." 新增一位選手(".$_POST["realname"].")至 tid=".$_POST["tid"]);
$reg_player++;
$sql = "UPDATE teams SET `reg_player` = ? WHERE `teams`.`tid` = ?";
$sth = $db->prepare($sql);
$sth->execute(array($reg_player,$tid));




$data->message = var_export(array($_POST["tid"],$_POST["realname"],$_POST["stu_num"],$_POST["birthday"],$_POST["id_num"],$_POST["cellphone"],$_POST["super"],$_POST["foreign"],$passport_name,$country,$gender,0,$name_head,$name_front,$name_back,$name_second), true);

echo json_encode($data);
exit();
?>
