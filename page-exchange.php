<?php 
require_once( "php/settings/db_connect.php");
require_once( "php/settings/functions.php");
require_once("php/settings/game_attribute.php");
testing();
start_session(1209600);
if(!isset($_SESSION["uid"])){
    header( 'Location: ../login' ) ;
}
if(!beforechangedue()){
    header( "refresh:2; url=../main" );
    get_header();
    echo "交換隊員時間已過，謝謝";
    get_footer();
    exit();
}
get_header();
$uid=$_SESSION["uid"];
$category=$_GET["category"];
$games=array("b_bas","g_bas","b_vol","g_vol","bad","tab","sof","soc");
if(!in_array($category,$games)){
    header( "refresh:1; url=../main" ); 
    echo "請重新選擇欲報名項目";
    get_footer();
    exit();
}
else{
    $sql = "select count(tid) from teams where category=? and uid=?";	
    $sth = $db->prepare($sql);	
    $sth->execute(array($category,$uid));
    $count=$sth->fetchColumn();
    if($count!=2){
        header( "refresh:1; url=../main" );
        echo "尚未報名第二隊\n 若欲更改隊員資料，請與管理員聯絡\n";
        get_footer();
        exit();
    }
    else{
       echo "<h2>".$attr[$category]["chinese"]."隊員交換</h2>";
       echo "<p>每隊人數上限: ".$attr[$category]["max_player"]."人,下限: ".$attr[$category]["min_player"]."人</p>";
    }
}
//$u_id=1;
?>
   
   
    <link rel="stylesheet" href="/js/jquery_ui/jquery_ui.css">
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery_ui/jquery-ui.js"></script>
    <script>
    $(document).ready(function () {
        $('#exchange_form').ajaxForm({
             beforeSubmit:function(){
                 var a=$(".check").eq(0).val();
                 var b=$(".check").eq(1).val();
                 if(parseInt(a)><?php echo $attr[$category]["max_player"] ?>){
                     alert("隊員人數不得超出<?php echo $attr[$category]["max_player"] ?>人");
                 }
                 else if(parseInt(a)<<?php echo $attr[$category]["min_player"] ?>){
                     alert("隊員人數不得低於<?php echo $attr[$category]["min_player"]?>人");
                     return false;
                 }
                 else{
                 }
                 
                 if(parseInt(b)><?php echo $attr[$category]["max_player"] ?>){
                     alert("隊員人數不得超出<?php echo $attr[$category]["max_player"] ?>人");
                     return false;
                 }
                 else if(parseInt(b)<<?php echo $attr[$category]["min_player"] ?>){
                     alert("隊員人數不得低於<?php echo $attr[$category]["min_player"]?>人");
                     return false;
                 }
                 else{
                 }
                
                 var c=$("#team1").find(".super").length;
                 var d=$("#team2").find(".super").length;
                 if(c><?php echo $attr[$category]["max_super"] ?> || d><?php echo $attr[$category]["max_super"]?>){
                     alert("體資/體保人數不得超出<?php echo $attr[$category]["max_super"]?>人");
                     return false;
                 }
                 else{
                 }
             },
             dataType: 'json',
             success: function(data) {
                 if (data.error) {
                     alert(data.error);
                     if(data.redirect){
                        redirect(data.redirect);
                     }
                 } else {
                     alert("成功送出\n請再次確認資料是否正確，若不正確，請立即與管理員聯絡");
                     if(data.redirect){
                        redirect(data.redirect);
                     }
                 }
             }
         });
         $("#team1, #team2").sortable({
             items: ".single_player:not(.teamleader)",
             opacity: 0.5,
             connectWith: ".team",
             receive: function(event, ui){
                 var player = ui.item.attr("id"); // player id
                 var player_input = ui.item.children("input[type=hidden]").eq(0);

                 var team = ui.item.parent().attr("tid");  // object team tid
                 var former = ui.item.parent().parent().siblings().children(".team").attr("tid");  // former team tid
                
                 var team_check = document.getElementById(team+"_check");
                 team_check.value = parseInt(team_check.value)+1;
                
                 var former_check = document.getElementById(former+"_check");
                 former_check.value = parseInt(former_check.value)-1;
                 
                 player_input.attr("value", team); // update input value

                 if(parseInt(team_check.value)><?php echo $attr[$category]["max_player"] ?>){
                     $('#'+team+'_msg').html("隊員人數不得超出<?php echo $attr[$category]["max_player"] ?>人");
                 }
                 else if(parseInt(team_check.value)<<?php echo $attr[$category]["min_player"] ?>){
                     $("#"+team+"_msg").html("隊員人數不得低於<?php echo $attr[$category]["min_player"]?>人");
                 }
                 else{
                     $('#'+team+'_msg').html("");
                 }
                 
                 if(parseInt(former_check.value)><?php echo $attr[$category]["max_player"] ?>){
                     $('#'+former+'_msg').html("隊員人數不得超出<?php echo $attr[$category]["max_player"] ?>人");
                 }
                 else if(parseInt(former_check.value)<<?php echo $attr[$category]["min_player"] ?>){
                     $("#"+former+"_msg").html("隊員人數不得低於<?php echo $attr[$category]["min_player"]?>人");
                 }
                 else{
                     $('#'+former+'_msg').html("");
                 }

                 var team_super = $('[tid="'+team+'"]').find(".super").length;
                 if(team_super > <?php echo $attr[$category]["max_super"]; ?>){
                     $("#"+team+"_smsg").html("體資/體保人數不得超出<?php echo $attr[$category]["max_super"]; ?>人");
                 }
                 else{
                     $("#"+team+"_smsg").html("");
                 }
                 var former_super = $('[tid="'+former+'"]').find(".super").length;
                 if(former_super > <?php echo $attr[$category]["max_super"]; ?>){
                     $("#"+former+"_smsg").html("體資/體保人數不得超出<?php echo $attr[$category]["max_super"]; ?>人");
                 }
                 else{
                     $("#"+former+"_smsg").html("");
                 }
                 
             },
         });
         $("#team1 b, #team2 b").disableSelection();
         
         
     });
    </script>
    <style>
        
        .player_name{
            text-align:center;
        }
        
        .box{
            float:right;
            width:47%;
            //margin:10px;
            padding:10px;
            box-shadow:0px 0px 2px 2px rgba(0,0,0,0.3);
            border-radius:10px;
        }
        .box:first-child{
            float:left;
        }

        .single_player {
            width: 100px;
            transition:opacity 0.2s ease;
            opacity:0.8;
            margin: 5px;
            padding: 10px;
            box-shadow:1px 1px 5px rgba(0,0,0,0.1);
            display:inline-block;
        }
        .msg, .smsg {
            color: rgb(230,50,50);
        }
        .preview{
            width:100px;
            height:100px;
            background-size:contain;
            background-repeat:no-repeat;
            background-position:center;
            margin:0 auto;
        }
        .teamleader{
            color:red;
        }
    </style>
    <p>紅色姓名為隊長<br>
    *標記者為體資/體保生
    </p>
    
    <form action="<?php echo get_template_directory_uri(); ?>/exchange_act.php" method="POST" id="exchange_form">
<?php
    $sql = "select tid,teamname from teams where category=? and uid=? order by tid asc";	
    $sth = $db->prepare($sql);	
    $sth->execute(array($category,$uid));
    for($i=1; $i<=2 ; $i++){
        $result=$sth->fetchObject();
        $sql_count = "select count(pid) from players where tid=?";
        $sth_count = $db->prepare($sql_count);
        $sth_count->execute(array($result->tid));
        ($i==1)?$team1_player = $sth_count->fetchColumn():$team2_player = $sth_count->fetchColumn();
?>
       <div class="box">
           <span class="mytitle"><b>隊伍: <?php echo $result->teamname;?></b></span>
       
        <div class="team" id="team<?php echo $i;?>" tid="<?php echo $result->tid;?>">
            
<?php
        $tid=$result->tid;
        $sql_player = "select pid,realname,pic_head,super,teamleader from players where tid=?";
        $sth_player = $db->prepare($sql_player);
        $sth_player->execute(array($tid));
        while($player = $sth_player->fetchObject()){
?>
            <div class="single_player<?php echo ($player->teamleader==1?" teamleader":"");?>" id="<?php echo $player->pid;?>" > 
                <div class="preview" style="background-image:url(<?php echo get_template_directory_uri()."/thumb/".$category."/".$tid."/".$player->pic_head;?>)"></div>
                <div class="player_name <?php echo ($player->super==="1")?"super":" "; ?>">
                    <?php if($player->super==="1") echo "*";
                          echo $player->realname; 
                    ?>
                </div>
                <input type="hidden"  name="tid[]" value="<?php echo $result->tid;?>">
                <input type="hidden" name="pid[]" value="<?php echo $player->pid;?>">
                <input type="hidden"  name="super[]" value="<?php echo $player->super;?>">
            </div>
<?php
        }
?>
        <input type="hidden" name="team<?php echo $i; ?>_num" class="check" id="<?php echo $result->tid?>_check" value="<?php echo ($i==1)?$team1_player:$team2_player; ?>" >
        
        </div>
        <span class="msg" id="<?php echo $result->tid; ?>_msg"></span><br />
        <span class="smsg" id="<?php echo $result->tid; ?>_smsg"></span>
        </div>
        
<?php
    }
?>
    <input type="hidden" name="category" value=<?php echo $category;?>>
    <div style="clear:both"></div>
    <br>
    <input type="submit" value="送出">
    </form>
    <a href="../change">返回</a>
     
<?php get_footer(); ?>
