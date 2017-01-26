<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/game_attribute.php");
testing();
get_header();
$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof");
?>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
<script>
$(document).ready(function(){
    $(".view:even").addClass("even");
    $("tr:first-child th").css({"font-size":"1em"});
    $(".tab_text").on( "click",function(e) {
        e.preventDefault();
        $(".view").css({"display":"none"});
        $(".category_box").css({"display":"none"});
        $(".tab_text").removeClass("select");
        $(this).addClass("select");
        var mystr=$(this).prop("id");
        var category=mystr.substr(0,mystr.search("_handler"));
        if(category=="all"){
            $(".category_box.all").css({"display":"block"});
            $(".view").css({"display":"table-row"});
        }else{
            $("."+category).css({"display":"table-row"});
            $(".category_box."+category).css({"display":"block"});
        }
    });

});

</script>
<style>
.tab_text{
    padding:5px 20px;
    border-radius:5px;
    margin:0px;
    cursor:pointer;
    transition:background-color 0.2s ease;
}
.tab_text:hover{
    background-color:#eeeeee;
}
.select{
    color:#F33F3F;
    font-size:20px;
}
.even{
    background-color:rgb(240,240,240);
}
table tr:hover{
    background-color:rgb(200,200,200);
}
.mycontainer{
    position:relative;
}

th{
    width:19%;
}
.unfinished{
    color:rgb(120,120,120);
}
.remain,.viewing,.done{
    display:inline-block;
    padding:5px;
}
.category_box{
    display:none;
}
.category_box.all{
    display:block;
}
</style>
    <span id="all_handler" class="tab_text select">全部</span>|
    <span id="b_bas_handler" class="tab_text">男籃</span>|
    <span id="g_bas_handler" class="tab_text">女籃</span>|
    <span id="b_vol_handler" class="tab_text">男排</span>|
    <span id="g_vol_handler" class="tab_text">女排</span>|
    <span id="tab_handler" class="tab_text">桌球</span>|
    <span id="bad_handler" class="tab_text">羽球</span>|
    <span id="sof_handler" class="tab_text">壘球</span>
    
<hr>
<div class= "mycontainer">
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
            echo '<div class="category_box '.key($attr).'" >';
            //echo    '<div class="title_box" >'.array_values($attr)[$i]["chinese"].'</div>';
            echo    '<div class="remain">尚餘'.(array_values($attr)[$i]["max_team"]-(0+$number[key($attr)])>0?array_values($attr)[$i]["max_team"]-(0+$number[key($attr)]):0).'/'.array_values($attr)[$i]["max_team"].'隊</div>';
            echo    '<div class="viewing">審核中: '.(0+$number2[key($attr)]).'</div>';
            echo    '<div class="done">已報名成功: '.(0+$number[key($attr)]).'</div>';
            echo '</div>';
            next($attr);
        }
        echo '<div class="category_box all" >';
        echo    '<div class="viewing">審核中: '.(0+array_sum($number2)).'</div>';
        echo    '<div class="done">已報名成功: '.(0+array_sum($number)).'</div>';
        echo '</div>';


    ?>
        <table>
           <tr>
            <th>項目</th>
            <th>隊伍</th>
            <th>資料確認</th>
            <th>繳費確認</th>
            <th>報名確認</th>
            </tr>
            <?php 
                for($i=1;$i<9;$i++){
                    $sql="select * from teams where category=?";
                    $sth=$db->prepare($sql);
                    $sth->execute(array($myarr[$i]));
                    while($result = $sth->fetchObject()){
                        echo "<tr class='view ".$myarr[$i].($result->success=="1"?"":" unfinished")."'>";
                        echo "<td>".$attr[$result->category]["chinese"]."</td>";
                        echo "<td>".$result->teamname."</td>";
                        echo "<td>".($result->data_checked=="1"?"已確認":"尚未確認")."</td>";
                        echo "<td>".($result->money_num!="999"?"已確認":"尚未確認")."</td>";
                        echo "<td>".($result->success=="1"?"已確認":"尚未確認")."</td>";
                        echo "</tr>";
                    }
                }
            ?>
        </table>
     </div>

</div>
<?php
get_footer();
?>