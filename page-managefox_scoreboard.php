<?php

require_once( "php/settings/db_connect.php");

require_once( "php/settings/functions.php");

require_once( "php/settings/game_attribute.php");

testing();

start_session(1209600);

$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");

$adminid=$_SESSION["adminid"];
$category=$_GET["category"];
$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");
if(!in_array($category,$myarr)){
    echo "error";
    exit();
}
$sql="select * from auth where admin=?";
$sth=$db->prepare($sql);
$sth->execute(array($adminid));
if($result = $sth->fetchObject()){
    $permit=$result->permit;
}else{
    echo "permission denied";
    exit();
}

//get_header();
$now_str=date_format($now,"Y-m-d H:i:s");






?>

<!DOCTYPE html>

<html lang="zh-tw">

<head>

    <meta charset="UTF-8">
    
    <title>view player</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
    <script>
        $(document).ready(function(){
            $(".match").on("click",function(e){
                e.preventDefault();
                if($(this).data("state")==0){
                    if(confirm("比賽開始?")){
                        var that=$(this);
                        $.ajax({
                            type: "POST",
                            url: "<?php echo get_template_directory_uri(); ?>/managefox_scoreboard_act.php",
                            data: {
                                "type":"start",
                                "id":$(this).data("id")
                            },
                            success: function(data){
                                if(data.error){
                                    alert(data.error);
                                }else{
                                    console.log(data.message);
                                    that.addClass("playing");
                                    that.data("state","1");
                                    //that.append($("<span>playing</span>"));
                                }

                            },
                            error:function(e){
                                console.log(e);
                            },
                            dataType:"json"
                        });
                    }
                }else if($(this).data("state")==1){
                    var scoreA,scoreB;
                    var teamA=$(this).find(".first").text();
                    var teamB=$(this).find(".second").text();
                    if((scoreA=prompt(teamA+"的分數是?"))!=null &&(scoreB=prompt(teamB+"的分數是?"))!=null){
                        var that=$(this);
                        $.ajax({
                            type: "POST",
                            url: "<?php echo get_template_directory_uri(); ?>/managefox_scoreboard_act.php",
                            data: {
                                "type":"end",
                                "id":$(this).data("id"),
                                "scoreA":scoreA,
                                "scoreB":scoreB
                            },
                            success: function(data){
                                if(data.error){
                                    alert(data.error);
                                }else{
                                    console.log(data.message);
                                    that.removeClass("playing");
                                    that.addClass("over");
                                    that.data("state","2");
                                    //that.append($("<span>playing</span>"));
                                }

                            },
                            error:function(e){
                                console.log(e);
                            },
                            dataType:"json"
                        });
                        
                    }
                    
                }
            });
        });
    </script>
    <style>
        .container{
            text-align:center;
            
        }
        .match{
            position:relative;
            display:inline-block;
            width:39%;
            height:120px;
            margin:3%;
            border-radius:5px;
            border:1px rgba(0,0,0,0.7) solid;
            text-align:center;
            font-size:20px;
            cursor:pointer;
            background-color:aquamarine;
            box-shadow:2px 2px 5px rgba(0,0,0,0.8);
            transition:all linear 0.1s;
        }
        .match:active{
            transform:translate(3px,3px);
            box-shadow:2px 2px 2px rgba(0,0,0,0.8);
        }
        
        .scheduled_time{
            
            font-size:15px;
                
        }
        .scheduled_time,.pair.number,.category{
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        hr{
            margin:5px 10px;
            border-color:cornflowerblue;
            
        }
        .playing{
            background-color:coral;
            
        }
        .over{
            background-color:gray;
        }
        .number{
            position:absolute;
            right:10px;
            bottom:5px;
            color:gray;   
            font-size:15px;
        }
        .category{
            position:absolute;
            left:10px;
            bottom:5px;
            color:gray;
            font-size:15px;
        }



    </style>

</head>

<body>

<div class="container">


<?php
$sql="
select T.id,T.number,T.scheduled_time,A.teamname teamnameA,B.teamname teamnameB,T.start_time,T.end_time from (select * from tournament where category=?) T
left join (select * from mapping where category=?) A on T.teamA=A.code
left join (select * from mapping where category=?) B on T.teamB=B.code
where T.start_time is not Null and T.end_time is Null and T.number!=0 order by T.scheduled_time,T.round,T.number;
";

$sth=$db->prepare($sql);

$sth->execute(array($category,$category,$category));
$i=0;
while($result = $sth->fetchObject()):?>
    
    <div class="match <?php echo (is_null($result->start_time)?'':'playing');?>" data-id="<?php echo $result->id; ?>" data-state="<?php echo (!is_null($result->start_time))+(!is_null($result->end_time));?>">
        
        <div class="scheduled_time"><?php echo $result->scheduled_time;?></div>
        <div class="pair">
            <span class="teamname first"><?php echo (is_null($result->teamnameA)?"NULL":$result->teamnameA);?></span><hr>
            <span class="teamname second"><?php echo (is_null($result->teamnameB)?"NULL":$result->teamnameB);?></span>
            
        </div>
        <div class="category"><?php echo $attr[$category]["chinese"]; ?></div>
        <div class="number"><?php echo $result->number;?></div>
    </div>
    
<?php $i++;endwhile;?>



<?php
$sql="
select T.id,T.number,T.scheduled_time,A.teamname teamnameA,B.teamname teamnameB,T.start_time,T.end_time from (select * from tournament where category=?) T
left join (select * from mapping where category=?) A on T.teamA=A.code
left join (select * from mapping where category=?) B on T.teamB=B.code
where T.start_time is Null and T.end_time is Null and T.number!=0 order by T.scheduled_time,T.round,T.number;
";

$sth=$db->prepare($sql);

$sth->execute(array($category,$category,$category));
$i=0;
while($result = $sth->fetchObject()):?>
    
    <div class="match <?php echo (is_null($result->start_time)?'':'playing');?>" data-id="<?php echo $result->id; ?>" data-state="<?php echo (!is_null($result->start_time))+(!is_null($result->end_time));?>">
        
        <div class="scheduled_time"><?php echo $result->scheduled_time;?></div>
        <div class="pair">
            <span class="teamname first"><?php echo (is_null($result->teamnameA)?"NULL":$result->teamnameA);?></span><hr>
            <span class="teamname second"><?php echo (is_null($result->teamnameB)?"NULL":$result->teamnameB);?></span>
        </div>
        <div class="category"><?php echo $attr[$category]["chinese"]; ?></div>
        <div class="number"><?php echo $result->number;?></div>
        
    </div>
    
<?php $i++;endwhile;?>






</div>
</body>

</html>

