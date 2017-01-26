<?php
require_once( "php/settings/db_connect.php");
require_once( "php/settings/functions.php");
require_once( "php/settings/game_attribute.php");
testing();
start_session(1209600);
if(!isset($_SESSION[ "uid"])){
    header( 'Location: ../login' ) ;
}
if(!beforechangedue()){
    header( "refresh:2; url=../main" );
    get_header();
    echo "交換隊員時間已過，謝謝";
    get_footer();
    exit();
}

$uid=$_SESSION["uid"];
$sql = "select uid,category,count(*) as c from teams where uid=? and success=1 group by category order by c desc";
$sth = $db->prepare($sql);
$sth->execute(array($_SESSION["uid"]));
$result = $sth->fetchObject();
if($result->c==2){
    $show_change=1;
}
if($show_change!=1){
    header( "refresh:2; url=../main" );
    get_header();
    echo "尚無可交換隊伍，謝謝";
    get_footer();
    exit();
}
get_header(); ?>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
<script>
    $(document).ready(function(){
        $(".change").on("click",function(){
            redirect("../exchange?category="+$(this).data("cat"));
        
        });
    
    });
</script>
<style>
    .nav {
        width: 173px;
        float: left;

    }
    .mycontent {
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
        .mycontent {
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

        .mycontent {
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

    table.teams td{
        vertical-align: middle;
    }


</style>
<div class="main">
    <div class="nav">
        <?php get_menu();?>
    </div>
    <div class="mycontent">
       <h2>交換隊員</h2>
       <p>
           以下僅列出可交換隊伍(同一項目兩隊)<br>
           完整隊伍請見 <a href="../myteam">我的隊伍</a>
       </p>
        <table class="teams">
            <tr>
                <td>項目</td>
                <td>隊伍</td>
                <td>隊員</td>
                <td>交換</td>
            </tr>
            <?php
            $ctrl =1;
            $sql="select e.uid,e.category,e.teamname,e.tid,count(*) as x from (select a.uid,a.category,b.teamname,b.tid from (select uid,category,count(*) as c from teams where uid= ? and success=1 group by category)as a join teams as b where c=2 and a.uid=b.uid and a.category=b.category)as e join players as f on e.tid=f.tid group by e.tid order by category" ;
            $sth=$db->prepare($sql);
            $sth->execute(array($uid));
            $number=array();
            while($result = $sth->fetchObject()){
                echo "<tr>";
                if($ctrl==1){
                    echo "<td rowspan='2'>".$attr[$result->category]["chinese"]."</td>";
                }
                echo     "<td>".$result->teamname."</td>";
                echo     "<td>".$result->x."</td>";
                if($ctrl==1){
                    echo "<td rowspan='2'><button class='change' data-cat='".$result->category."'>交換隊員</button></td>";
                }
                echo "</tr>";
                $ctrl=1-$ctrl;
            }

            ?>
        </table>
       
        <a href='../main'>返回</a>
    </div>
    <div class="clear"></div>
</div>




<?php  get_footer(); ?>