<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/game_attribute.php");

get_header();
$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");
?>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
<script>
$(document).ready(function(){
    $(".tab_text").on( "click",function(e) {
        e.preventDefault();
        $(".view").css({"display":"none"});
        $(".tab_text").removeClass("select");
        $(this).addClass("select");
        var mystr=$(this).prop("id");
        var category=mystr.substr(0,mystr.search("_handler"));
        if(category=="all"){
            $(".view").css({"display":"inline-block"});
        }else{
            $("."+category).css({"display":"inline-block"});
        }
    });
	$(".view").on("mouseover",function(){
        var that=$(this);
        if($(this).data("done")=="0"){
            that.data("done","1");
            $.ajax({
                type:"post",
                url:"<?php echo get_template_directory_uri(); ?>/get_player_act.php",
                data:{"tid":that.data("tid")},
                dataType:'json',
                success:function(data){
                    if(data.error){
                        that.data("done","0");
                        alert(data.error);
                        
                    }else{
                        var box=$("<div class='result'></div>");
                        box.html(data.message);
                        
                    }
                    that.append(box);
                },
                error:function(data){
                    that.data("done","0");
                    alert(data);
                }

            });
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
.mycontainer{
    position:relative;
}
.view{
    display:inline-block;
    float:left;
    width:130px;
    padding:10px;
    margin:10px;
    //background-color:rgba(0,0,0,0.2);
    //border-radius:5px;
    transition:all 0.2s ease;
    position:relative;
}
.view:hover{
    background-color: rgba(217, 83, 83, 1);
    cursor:pointer;
    color: #FFF;
}
.text_category {
    padding-left: 1px;
}
.text_teamname {
    font-size: 16px;
}
.view .result{
    position: absolute;
    left: 0%;
    top: 90%;
    display: none;
    width: 110px;
    background-color: rgba(54, 58, 63, 1);
    padding: 5px 25px 10px 15px;
   // border-bottom-left-radius: 5px;
    border-bottom-right-radius: 15px;
   // border-top-right-radius: 5px;
    z-index: 2;
    color: #FFFFFF;
}
.view:hover .result{   
    display:block;
}
.single_player{
    display: inline-block;
    float: left;
    width: 55px;
    text-align: center;
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


    ?>
       
	<?php 
		for($i=1;$i<8;$i++){
			$sql="select * from teams where category=? and success=1";
			$sth=$db->prepare($sql);
			$sth->execute(array($myarr[$i]));
			while($result = $sth->fetchObject()){
				echo "<div class='view ".$myarr[$i]."' data-tid='".$result->tid."' data-done='0'>";
				echo "<div class='text_category'>".$attr[$myarr[$i]]["chinese"]."</div>";
				echo "<div class='text_teamname'>".$result->teamname."</div>";
				echo "</div>";
			}
		}
	?>
	
     </div>

<?php
get_footer();
?>
