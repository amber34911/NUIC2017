<?php
//echo var_dump($_POST);
//echo var_dump($_FILES);
//exit();

require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/game_attribute.php");
start_session(1209600);
$data=new stdClass();
if(!$_SESSION["uid"] || !$_POST["category"]){
    //$data->error.="please log in first";
    $data->error.="抱歉，您無權檢視此頁面\n請先登入";
    echo json_encode($data);
    exit();
}
$uid=$_SESSION["uid"];
$sql = "select * from users where uid=?";
$sth = $db->prepare($sql);
$sth->execute(array($uid));
$result = $sth->fetchObject();
$veri=$result->veri_state;
if($veri!=1){
    $data->error.="抱歉，帳號尚未啟用\n請先收取認證信後，再進行報名手續";
    echo json_encode($data);
    exit();
}
//set up session[uid] and post category
$category=$_POST["category"];
$teamname=$_POST["teamname"];

if(!infirst()&&!insecond()){
    $data->error="現在並非報名時間";
    $data->redirect="../main";
    echo json_encode($data);
    exit();
}

$sql = "select count(*) as c from teams where uid=? and category=?";
$sth = $db->prepare($sql);
$sth->execute(array($uid,$category));
$result = $sth->fetchObject();
if(($result->c)!=0){
    if(insecond()){
        $data->error="請從官方網站報名";
        $data->redirect="../main";
        echo json_encode($data);
        exit();
    }else{
        $data->error="您已在此項目報名過一隊\n若欲報名第二隊，請等候第二階段";
        $data->redirect="../myteam";
        echo json_encode($data);
        exit();
    }
}

$sql = "select count(*) as c from teams where success=1 and category=?";
$sth = $db->prepare($sql);
$sth->execute(array($category));
$result = $sth->fetchObject();
if(($result->c)>=$attr[$category]["max_team"]){
    $data->error="抱歉，該項目報名隊數已額滿，謝謝您的參與";
    $data->redirect="../main";
    echo json_encode($data);
    exit();
}


if(!preg_match('/\S/',$teamname)){
    $data->error.="無法辨識的'隊伍名稱'欄位\n";
}

$fields = array(
   "game_category" => $category,
   "teamname" => $teamname
);
check_input_fields($fields,$data);

$num=count($_POST["realname"]);
if($num<$attr[$category]["min_player"]||$num>$attr[$category]["max_player"]){
    if($num<$attr[$category]["min_player"]){
        $data->error="隊員人數小於下限";
    }else{
        $data->error="隊員人數超過上限";
    }
    echo json_encode($data);
    exit();
}
$passport_name=array();
$country=array();
$super_count = 0;
//checking empty fields
for($i=0;$i<$num;$i++){
    if(!preg_match('/\S/',$_POST["realname"][$i])){
        $data->error.="無法辨識的'姓名'欄位\n";
    }
    if(!preg_match('/\S/',$_POST["stu_num"][$i])){
        $data->error.="無法辨識的'學號'欄位\n";
    }
    if(!preg_match('/\S/',$_POST["birthday"][$i])){
        $data->error.="無法辨識的'生日'欄位\n";
    }
    if(!preg_match('/\S/',$_POST["id_num"][$i])){
        $data->error.="無法辨識的'身份證字號'欄位\n";
    }
    if($_POST["foreign"][$i]==1){
        $passport_name[$i]=$_POST["passport_name"][$i];
        $country[$i]=$_POST["country"][$i];
        $gender[$i]=$_POST["gender"][$i];
        if(!preg_match('/\S/', $passport_name[$i])){
            $data->error.="無法辨識的'護照上姓名'欄位\n";
        }
        if(!preg_match('/\S/',$country[$i])){
            $data->error.="無法辨識的'國籍'欄位\n";
        }
        if(!preg_match('/\S/',$gender[$i])){
            $data->error.="無法辨識的'性別'欄位\n";
        }
        $fields = array(
            "id_num" => $_POST["id_num"][$i]
        );
        check_input_fields($fields,$data);
    }else{
        $passport_name[$i]="";
        $country[$i]="";
        $gender[$i]="";
        $fields = array(
            "local_id_num" => $_POST["id_num"][$i]
        );
		check_input_fields($fields,$data);
    }
    $fields = array(
      "realname" => $_POST["realname"][$i] ,
      "stu_num" => $_POST["stu_num"][$i],
      "date" => $_POST["birthday"][$i],
      "cellphone" => $_POST["cellphone"][$i],
      "addteam_super" => $_POST["super"][$i],
      "foreign"=>$_POST["foreign"][$i],
      "passport_name" =>$passport_name[$i],
      "country" => $country[$i],
      "gender"=>$gender[$i]
    );
    check_input_fields($fields,$data);
    if($data->error!=""){
        $data->error.="on player ";
        $data->error.=$i+1;
        $data->error.="\n";
        echo json_encode($data);
        exit();
    }
    if($_POST["super"][$i]!=0) $super_count++;
}


//checking empty leader cellphone
if(!preg_match('/\S/',$_POST["cellphone"][0])){
    $data->error.="隊長的'手機'欄位為必填欄位\n";
}
//check for duplicate idnum
if(array_unique($_POST["id_num"]) != $_POST["id_num"]){
    $data->error.="重複的'身份證字號'欄位\n";
}
// check for super player
if($count > $attr[$category]["max_super"]){
    $data->error.="體資/體保生超過上限";
}

//checking empty pics
if(count($_FILES["pic_head"]["name"])!=$num){
    $data->error.="number of pic head not match(".count($_FILES["pic_head"]["name"])."\n";
}
if(count($_FILES["pic_front"]["name"])!=$num){
    $data->error.="number of pic front not match(".count($_FILES["pic_front"]["name"])."\n";
}
if(count($_FILES["pic_back"]["name"])!=$num){
    $data->error.="number of pic back not match(".count($_FILES["pic_back"]["name"])."\n";
}
if(count($_FILES["pic_second"]["name"])!=$num){
    $data->error.="number of pic second not match(".count($_FILES["pic_second"]["name"])."\n";
}
if($data->error!=""){
    echo json_encode($data);
    exit();
}

//check for exist team

$sql = "select * from teams where teamname=? and category=?";
$sth = $db->prepare($sql);
$sth->execute(array($teamname,$category));
if($result = $sth->fetchObject()){
    $data->error="隊伍名稱已有人使用\n";
    echo json_encode($data);
    exit();
}







try{

    for($i=0;$i<$num;$i++){

        echo $i;
        $type = get_mime_type($_FILES["pic_head"]["tmp_name"][$i]);
        echo $type.",";
        echo $_FILES["pic_head"]["size"][$i].",";
        $type = get_mime_type($_FILES["pic_front"]["tmp_name"][$i]);
        echo $type.",";
        echo $_FILES["pic_front"]["size"][$i].",";
        $type = get_mime_type($_FILES["pic_back"]["tmp_name"][$i]);
        echo $type.",";
        echo $_FILES["pic_back"]["size"][$i].",";
        $type = get_mime_type($_FILES["pic_second"]["tmp_name"][$i]);
        echo $type.",";
        echo $_FILES["pic_second"]["size"][$i]."\n";

    }
} catch(PDOException $e) {
  //echo $e->getMessage();
}





echo json_encode($data);

?>
