<?php
//header('Content-Type: application/json; charset=utf-8');
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/game_attribute.php");
start_session(1209600);
$uid=$_SESSION["uid"];


$data=new stdClass();
// 檢查POST欄位
if($_FILES["pic_inf"]["error"]>0){
    $data->error=$_FILES["pic_inf"]["error"];
    echo json_encode($data);
    exit();
}else{
    $upload_dir = 'infpic/';
    $ori_name=$_FILES["pic_inf"]["name"];
    $ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $ori_name);
    $hashed_name=hash("sha256",$ori_name.$now->format("Y-m-d H:i:s"));
    $type = extract_file_type($_FILES["pic_inf"]["tmp_name"]);
    
    //echo preg_match('/image\/([Pp][Nn][Gg]|[Jj][Pp][Ee]?[Gg])/',$type);
    if(preg_match('/image\/([Jj][Pp][Ee]?[Gg])/',$type)==0){
        $data->error="錯誤: 違法的檔案格式";
        echo json_encode($data);
        exit();
    }
    $hashed_name2 = preg_replace('/.*([Pp][Nn][Gg]|[Jj][Pp][Ee]?[Gg])$/',$hashed_name.'.${1}',$ori_name);
    $move_state=move_uploaded_file($_FILES["pic_inf"]["tmp_name"],$upload_dir.$hashed_name2);
    if(!$move_state){
        $data->error="發生錯誤，請再試一次";
        echo json_encode($data);
        exit();
    }
    $sql="INSERT INTO `money_inf_pic` (original_name,hashed_name,ext_name)
         VALUES (?,?,?)";
    $sth=$db->prepare($sql);
    $result=$sth->execute(array($ori_name,$hashed_name,$ext));
    
    if($result){
        $data->message=$hashed_name;
    }else{
        $data->error="產生代碼錯誤";
        echo json_encode($data);
        exit();
    }
    
}

echo json_encode($data);
?>
