<?php
  require_once("php/settings/db_connect.php");
  require_once("php/settings/functions.php");
  require_once("php/settings/userdb.php");
 
  $data=new stdClass();
  $id = $_POST['id'];
  $pw = $_POST['pw'];
  $dep = $_POST['dep'];
  if(login_chk($id,$pw,$dep)===false){
	  remote_log("[登入失敗] $id 登入失敗");
      $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
      $sth=$db->prepare($sql);
      $sth->execute(array("login",$adminname." wrong password",get_ip()));
      $data->error .= "帳號或密碼錯誤";
      echo json_encode($data);
      exit();

  }

  else{
    $sql = "select * from auth where admin=?";
    $sth = $db->prepare($sql);
    $sth->execute(array($id));     
    $result = $sth->fetchObject();
    if($result==false){
	  remote_log("[登入失敗] $id 登入失敗");
      $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
      $sth=$db->prepare($sql);
      $sth->execute(array("login",$adminname." not admin",get_ip()));
      $data->error .= "permission denied";
      echo json_encode($data);
      exit();
    }
    else{
		$adminname=$result->adminname;
		remote_log("[登入成功] $adminname 登入成功");
        $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
        $sth=$db->prepare($sql);
        $sth->execute(array("login",$adminname." logged in successfully",get_ip()));
        
        start_session();
        $_SESSION["adminid"]=$id;
        $data->redirect = "managefox_menu";
        echo json_encode($data);
    }
  }

?>
