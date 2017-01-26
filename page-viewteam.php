<?php
require_once( "php/settings/db_connect.php");
require_once( "php/settings/functions.php");
require_once( "php/settings/game_attribute.php");
testing();
start_session(1209600); 
if(!isset($_SESSION["uid"])){
    header( 'Location: ../login' ) ; 
}
$uid=$_SESSION["uid"];
$data=new stdClass();
$tid=$_GET["tid"];
$fields = array(
      "number" => $tid
   );
   check_input_fields($fields,$data);
    if($data->error!=""){
        $tid=-1;
    }

get_header();

?>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery_ui/jquery-ui.js"></script>
    <script>
    
       $(document).ready(function () {

           
        
            $.ajax({
              type: "POST",
              url: "<?php echo get_template_directory_uri(); ?>/viewteam_act.php",
              data: {"tid":<?php echo $tid?>},
              success: function(data){
                  //alert(attr.bad.first_start.date);
                if(data.error){
                    alert(data.error);
                    redirect("../myteam");
                }else{
                    if(data.players.length>=1){
                        $(".players").append($("<h2>"+data.category_chinese+"-"+data.teamname+"</h2><hr>"));
                        
                        for(i=0;i<data.players.length;i++){
                            var inf=$("<div class='inf'></div>");
                            inf.html("姓名:"+data.players[i].realname+"<br>"+
                            "學號:"+data.players[i].stu_num+"<br>"+
                            "生日:"+data.players[i].birthday+"<br>"+
                            "體資/體保:"+(data.players[i].superman=="1"?"是":"否")+"&nbsp;&nbsp;"+
                            "外籍生:"+(data.players[i].foreigner=="1"?"是":"否")+"<br>"+
                            (data.players[i].foreigner=="1"?"護照號碼:":"身分證字號:")+data.players[i].id_num+"<br>"+
                            "手機:"+data.players[i].cellphone+"<br>"+
                                     (data.players[i].foreigner=="1"?
                            "國籍:"+data.players[i].country+"<br>"+
                            "性別:"+(data.players[i].gender=="m"?"男":data.players[i].gender=="f"?"女":"")+"<br>"+
                            "護照姓名:"+data.players[i].passport_name+"<br>":"")
                            );
                            var pic=$("<div class='pic'></div>");
                            var head=$("<a href='<?php echo get_template_directory_uri();?>/upload/"+data.category+"/"+data.tid+"/"+data.players[i].pic_head+"' style='display:inline-block'><div class='head' style='background-image:url(<?php echo get_template_directory_uri();?>/thumb/"+data.category+"/"+data.tid+"/"+data.players[i].pic_head+")'></div></a>");
                            var bottom=$("<div class='bottom'></div>");
                            
                            var front=$("<a href='<?php echo get_template_directory_uri();?>/upload/"+data.category+"/"+data.tid+"/"+data.players[i].pic_front+"' style='display:inline-block'><div class='front' style='background-image:url(<?php echo get_template_directory_uri();?>/thumb/"+data.category+"/"+data.tid+"/"+data.players[i].pic_front+")'></div></a>");
                            var back=$("<a href='<?php echo get_template_directory_uri();?>/upload/"+data.category+"/"+data.tid+"/"+data.players[i].pic_back+"' style='display:inline-block'><div class='back' style='background-image:url(<?php echo get_template_directory_uri();?>/thumb/"+data.category+"/"+data.tid+"/"+data.players[i].pic_back+")'></div></a>");
                            var second=$("<a href='<?php echo get_template_directory_uri();?>/upload/"+data.category+"/"+data.tid+"/"+data.players[i].pic_second+"' style='display:inline-block'><div class='second' style='background-image:url(<?php echo get_template_directory_uri();?>/thumb/"+data.category+"/"+data.tid+"/"+data.players[i].pic_second+")'></div></a>");
                            bottom.append(front);
                            bottom.append(back);
                            bottom.append(second);
                            pic.append(head);
                            pic.append(bottom);
                            var player=$("<div class='player'></div>");
                            player.append($("<div class='clear'></div>"));
                            player.append(pic);
                            player.append(inf);
                            player.append("<div class='clear'></div>")
                            $(".players").append(player);
                            
                    
                        }
                        $(".players").append("<div class='clear'></div><a href='../main'>返回</a>");
                        $(".howmuch").text(data.howmuch);
                    }else{
                        $(".players").html("No player data to display");
                    }
                }
              },
              dataType: "json"
            });

        });
    </script>
    <style>
        .player{
            float:left;
            width:250px;
            min-height:200px;
            position: relative;
            margin-bottom:20px;
        }
        .inf{
            position:absolute;
            width:140px;
            display:inline-block;
            margin-left:8px;
            
        }
        .pic{
            width:105px;
            display:inline-block;
            //float:left;
        }
        .bottom{
            margin-top:10px;
        }
        .bottom div{
            display:inline-block;
            margin:2px;
        }
        .bottom img{
            width:30px;
        }

        .clear{
            clear:both;
        }
        
        .nav {
            width: 173px;
            float: left;

        }
        .players {
            width:73%;
            width: calc(100% - 200px);
            float: right;


        }
        @media screen and (max-width: 800px){
            .nav {
                width: 100%;
                float: none;
                text-align:center;
            }
            .players {
                width: 100%;
                float:none;
            }
            .nav a[href*=category] li{
                display:inline-block;
                width:60px;
                padding:3px 5px;
            }
            ul{
                margin:0px;
            }

        }
        @media screen and (max-width: 545px){

            .players {
                width: 100%;
            }

            .nav a[href*=category] li{
                display:block;
                width:100%;
                padding:5px 5px;
            }

        }
        .mymenu{
            list-style: none;
        }
        .mymenu li{
            padding:3px 20px;
            transition:background-color 0.2s ease;
            border-top-right-radius:5px;
            border-bottom-right-radius:5px;


        }
        .mymenu a{
            text-decoration:none;
        }
        .mymenu li:hover{
            background-color:rgb(240,240,240);
            border-left:rgb(230,230,230) 5px solid;

        }
        .head{
            background-size:contain;
            width:105px;
            height:135px;
            background-repeat:no-repeat;
            background-position:center;
        }
        
        
        .front,.back,.second{
            background-size:contain;
            width:31px;
            height:31px;
            background-repeat:no-repeat;
            background-position:center;
        }
    </style>
   <!-- might need some result message for users -->
   <div class="main">
    <div class="nav">
        <?php get_menu();?>
    </div>
    <div class="players">
        <p style="color:red;">※繳費後請至 <a href="/myteam">我的隊伍</a> 填寫繳費資訊<br>
            <span class="howmuch"></span>
        </p>
        <span> </span>
        
    </div>
    <div class="clear"></div>
</div>
    
   
    
<?php   get_footer(); ?>
