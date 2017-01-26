<?php 
    require_once("php/settings/db_connect.php"); 
    require_once("php/settings/functions.php"); 
    require_once("php/settings/game_attribute.php"); 
    start_session(1209600); 
    $data=new stdClass();
    if(!isset($_SESSION["uid"])){
        header( 'Location: ../login' ) ; 
    }
    if(!beforechangedue()){
        $data->error="交換隊員時間已過，謝謝";
        $data->redirect="../main";
        echo json_encode($data);
        exit();
    }
    $num = count($_POST["pid"]);
    $pid_array = array(); 
    $super_pid = array();
    $super_tid = array();
    $super_super = array();
    $teamleader_array = array();
    # Sanity check for post data
    # number combine
    for($i=0; $i<$num ; $i++){
        $numbers .= $_POST["tid"][$i].$_POST["pid"][$i]; 
        array_push($pid_array, $_POST["pid"][$i]);
    }    
    $fields = array(
        "game_category" => $_POST["category"],
        "number" => $numbers
    );
    check_input_fields($fields,$data);
    if($data->error){
        echo json_encode($data);
        exit();
    }
    # Sanity Check Passed
    $uid = $_SESSION["uid"];
    $category = $_POST["category"];

    $sql = "select tid from teams where category=? and uid=? and success=1 order by tid asc";
    $sth = $db->prepare($sql);
    $sth->execute(array($category, $uid));
    $counter = 1;
    while($result = $sth->fetchObject()){
        if($counter == 1) $tid1 = $result->tid;
        else if ($counter == 2) $tid2 = $result->tid;
        $counter++;
    }
    # check tid & team_num 
    $team1_num = 0;
    $team2_num = 0;
    for($i=0; $i<$num; $i++){
        if($_POST["tid"][$i]==$tid1){
            $team1_num++;
        }
        else if($_POST["tid"][$i]==$tid2){
            $team2_num++;
        }
        else{
            $data->error .= "非法的隊伍 請從官方網站交換隊員";
            echo json_encode($data);
            exit();
        }
    }
    
    # check pid
    $player_num = 0;
    $sql = "select pid,super,tid,teamleader from players where tid=? or tid=?";
    $sth = $db->prepare($sql);
    $sth->execute(array($tid1, $tid2));
    while($result = $sth->fetchObject()){
        $key = array_search($result->pid, $pid_array);
        if($key===false){
            $data->error .= "非法的隊員 請從官方網站交換隊員";
            echo json_encode($data);
            exit();
        }
        else{
            unset($pid_array[$key]);
            #for checking max_super
            array_push($super_pid,$result->pid);
            array_push($super_tid,$result->tid);
            array_push($super_super,$result->super);
        }
        $player_num++;
    }

    # check team1_num, team2_num, total_num
    if( $player_num!=$num || count($pid_array)!=0){
        $data->error .= "隊員數量不符 請從官方網站交換隊員";
        echo json_encode($data);
        exit();
    }
    # check tid counter
    else if($counter!=3){
        $data->error .= "非法的隊伍數量 請從官方網站交換隊員";
        echo json_encode($data);
        exit();
    }
    else if($team1_num<$attr[$category]["min_player"]||$team2_num<$attr[$category]["min_player"]){
        $data->error .= "隊員人數小於下限";
        echo json_encode($data);
        exit();
    }
    else if($team1_num>$attr[$category]["max_player"]||$team2_num>$attr[$category]["max_player"]){
        $data->error .="隊員人數超過上限";
        echo json_encode($data);
        exit();
    }
    #checking for max_super
    $team1_super=0;
    $team2_super=0;
    for($i=0;$i<$num;$i++){
        $key=array_search($_POST["pid"][$i], $super_pid);
        if($super_super[$key]=="1"){
            if($_POST["tid"][$i]==$tid1){
                $team1_super++;
            }else{
                $team2_super++;
            }
        }
    }

    if($team1_super>$attr[$category]["max_super"]||$team2_super>$attr[$category]["max_super"]){
        $data->error .="體資生人數超過上限";
        echo json_encode($data);
        exit();
    }
    #check for teamleader
    $sql = "select pid,tid from players where teamleader=1 and (tid=? or tid=?)";
    $sth = $db->prepare($sql);
    $sth->execute(array($tid1, $tid2));
    while($result = $sth->fetchObject()){
        if($_POST["tid"][array_search($result->pid,$_POST["pid"])]!=$result->tid){
            $data->error .="隊長不能交換";
            echo json_encode($data);
            exit();
        }
    }


    for($i=0; $i<$num ; $i++){
        $tid = $_POST["tid"][$i];
        $pid = $_POST["pid"][$i];
        $sql="select * from players join teams on players.tid=teams.tid where pid=?";
        $sth = $db->prepare($sql);	
        $sth->execute(array($pid));
        $result = $sth->fetchObject();
        $old_tid=$result->tid;
        $old_teamname=$result->teamname;
        $pic_head=$result->pic_head;
        $pic_front=$result->pic_front;
        $pic_back=$result->pic_back;
        $pic_second=$result->pic_second;
        
        if($old_tid!=$tid){//team changed
            
            rename("upload/".$_POST["category"]."/".$old_tid."/".$pic_head,
                   "upload/".$_POST["category"]."/".$tid."/".$pic_head);
            rename("upload/".$_POST["category"]."/".$old_tid."/".$pic_front,
                   "upload/".$_POST["category"]."/".$tid."/".$pic_front);
            rename("upload/".$_POST["category"]."/".$old_tid."/".$pic_back,
                   "upload/".$_POST["category"]."/".$tid."/".$pic_back);
            rename("upload/".$_POST["category"]."/".$old_tid."/".$pic_second,
                   "upload/".$_POST["category"]."/".$tid."/".$pic_second);
            rename("thumb/".$_POST["category"]."/".$old_tid."/".$pic_head,
                   "thumb/".$_POST["category"]."/".$tid."/".$pic_head);
            rename("thumb/".$_POST["category"]."/".$old_tid."/".$pic_front,
                   "thumb/".$_POST["category"]."/".$tid."/".$pic_front);
            rename("thumb/".$_POST["category"]."/".$old_tid."/".$pic_back,
                   "thumb/".$_POST["category"]."/".$tid."/".$pic_back);
            rename("thumb/".$_POST["category"]."/".$old_tid."/".$pic_second,
                   "thumb/".$_POST["category"]."/".$tid."/".$pic_second);
            
        }
        
        $sql = "update players set tid=? where pid=? ";	
        $sth = $db->prepare($sql);	
        $sth->execute(array($tid,$pid));
        $str.= $pid." to ".$tid.",";
    }
    $sql = "insert into auth_log(eventtype,state,ip) values(?,?,?)";
    $sth=$db->prepare($sql);
    $sth->execute(array("exchange",$uid." change ".$str,get_ip()));
	remote_log("[交換選手] $uid 交換 $category 選手 ");
    $data->redirect="../main";
    echo json_encode($data);
    exit();
?>
