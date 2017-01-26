<?php 

require_once( "php/settings/db_connect.php");

require_once( "php/settings/functions.php");

require_once( "php/settings/game_attribute.php");

testing();

start_session(1209600);

if(!isset($_SESSION["uid"])){

    header( 'Location: ../login' );

}

$uid=$_SESSION["uid"];

get_header(); ?>

<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>

<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>

<script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>

<script>

    $(document).ready(function(){

        $(".category_box").on("click",function(){

            redirect("../reg/?category="+$(this).data("href"));

        });  

    });

    

</script>

<style>

    .nav {

        width: 173px;

        float: left;

      

    }

    .reg_inf {

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

        .reg_inf {

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



        .reg_inf {

            width: 100%;

        }

        

        .nav a[href*=category] li{

            display:block;

            width:100%;

            padding:5px 5px;

        }

        

    }

    @media screen and (max-width: 435px){

        .boxes{

            margin:0px!important;

        }

        .category_box{

            display:block!important;

            width:100%!important;

            height:100%!important;

            position:static!important;

            margin:20px 0px!important;

            padding:0px!important;

            border-radius:0px!important;

            box-shadow:none!important;

            

        }

        .title_box{

            border-radius:0px!important;

            box-shadow:none!important;

            position:static!important;

            margin:0px!important;

            padding:0px!important;

        }

        .center_box{

            position:static!important;

            margin:0px!important;

            padding:0px!important;

        }

        .left_top{

            display:inline!important;

            position:static!important;

            margin:0px!important;

            padding:0px!important;

        }

        .inner_box{

            display:inline!important;

            position:static!important;

            margin:0px!important;

            padding:0px!important;

            font-size:12px!important;

            line-height:1!important;

        

        }

        .bottom{

            display:inline!important;

            position:static!important;

            margin:0px!important;

            padding:0px!important;

            text-align:left!important;

        }

        .left_bottom{

            display:block!important;

            position:static!important;

            margin:0px!important;

            padding:0px!important;

            opacity:1!important;

            text-align:left!important;

        }

        .right_bottom{

            display:block!important;

            position:static!important;

            margin:0px!important;

            padding:0px!important;

            opacity:1!important;

            text-align:left!important;

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

    input[disabled]{

        background-color:rgb(240,240,240);

    }

    .server_error{

        color:rgb(230,50,50);

    }

    .boxes{

        padding:30px;

        

    }

    .category_box{

        display:inline-block;

        width:130px;

        height:130px;

        position:relative;

        border:1px solid rgb(230,230,230);

        margin:40px 20px;

        border-radius:5px;

        cursor:pointer;

        transition:all 0.2s ease;

    }

    .category_box:hover{

        color:rgb(60,60,60);

    }

    .title_box{

        border-radius:1px;

        position:absolute;

        top:-28px;

        left:-15px;

        font-size:30px;

        background-color:white;

        box-shadow:2px 2px 2px rgba(200,200,200,0.8);

        padding:3px 10px;

        line-height: 1.3;

    }

    .center_box{

        position:absolute;

        top:20px;

        bottom:0px;

        right:0px;

        left:0px;

        

    }

    .left_top{

        top:0px;

        left:0px;

        margin:5px;

        position:absolute;

    }

    .bottom{

        bottom:0px;

        right:0px;

        margin:5px;

        position:absolute;

        text-align:right;

    }

    .left_bottom{

        top:100%;

        left:0px;

        margin:5px;

        position:absolute;

        text-align:left;

        opacity:0;

        transition:opacity 0.2s ease;

    }

    .right_bottom{

        top:100%;

        right:0px;

        margin:5px;

        position:absolute;

        text-align:right;

        opacity:0;

        transition:opacity 0.2s ease;

    }

    .category_box:hover .left_bottom,.category_box:hover .right_bottom{

        opacity:1;

    }

    .inner_box{

        

        font-size: 50px;

        line-height: 95px;

        text-align: center;

        

    }

    @media screen and (max-width: 768px){

      #site-navigation div > ul > li > a  {

        padding:4px 6px;

      }

    }

</style>



<div class="main">

        

    <div class="nav">

        <?php get_menu();?>

    </div>

    <div class="reg_inf">

       

        <?php



        if(beforefirst()){

            echo "<h2>尚未開放報名</h2>";

            

        }elseif(infirst()){

            echo "<h2>開放報名中 (第一階段)</h2>";

        }elseif(afterfirst()&&beforesecond()){

            echo "<h2>資料整理中，請等待第二階段報名開始</h2>";

        }elseif(insecond()){

            echo "<h2>開放報名中 (第二階段)</h2>";

        }elseif(aftersecond()){

            echo "<h2>所有報名皆已結束</h2>";

        }

        echo "<p>第一階段開放時間  ".$first_start->format("Y-m-d")." → ".$first_end->format("Y-m-d")."<br>";

        echo "第二階段開放時間  ".$second_start->format("Y-m-d")." → ".$second_end->format("Y-m-d")."</p>";

        ?>

        <hr>

        <div class="boxes">

        <?php



        $sql="select category,count(*) as c from teams where success=1 group by category" ;

        $sth=$db->prepare($sql);

        $sth->execute(array($uid));

        $number=array();

        while($result = $sth->fetchObject()){

            $number[$result->category]=$result->c;

        }

        

        $sql="select category,count(*) as c from teams where success=0 group by category" ;

        $sth=$db->prepare($sql);

        $sth->execute(array($uid));

        $number2=array();

        while($result = $sth->fetchObject()){

            $number2[$result->category]=$result->c;

        }

       





        for($i=0;$i<7;$i++){

            echo '<div class="category_box" data-href="'.key($attr).'">';

            echo    '<div class="title_box" data-href="'.key($attr).'">'.array_values($attr)[$i]["chinese"].'</div>';

            echo    '<div class="center_box">';

            echo        '<div class="left_top">尚餘</div>';

            echo        '<div class="inner_box">'.(array_values($attr)[$i]["max_team"]-(0+$number[key($attr)])>0?array_values($attr)[$i]["max_team"]-(0+$number[key($attr)]):0).'</div>';

            //echo        '<div class="right_bottom">隊'.'  ('.(0+$number[key($attr)]).'/'.array_values($attr)[$i]["max_team"].')<br>';

            echo        '<div class="bottom">/'.array_values($attr)[$i]["max_team"].' 隊</div>';

            echo        '<div class="left_bottom">審核中: '.(0+$number2[key($attr)]).'</div>';

            echo        '<div class="right_bottom">已報名: '.(0+$number[key($attr)]).'</div>';

            echo    '</div>';

            echo '</div>';

            next($attr);

        }

        ?>

        </div>

    </div>

    <div class="clear"></div>

</div>



<?php



	get_footer();

?>

