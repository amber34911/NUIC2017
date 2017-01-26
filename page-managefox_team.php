<?php

require_once( "php/settings/db_connect.php");

require_once( "php/settings/functions.php");

require_once( "php/settings/game_attribute.php");

testing();

start_session(1209600);

$adminid=$_SESSION["adminid"];

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

$tid=$_GET["tid"];

# Sanity check for post data

# number combine

$fields = array(

    "number" => $tid

);

check_input_fields($fields,$data);

if($data->error){

    echo json_encode($data);

    exit();

}

# Sanity Check Passed

$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");

$sql="select * from teams join users on teams.uid=users.uid where tid=?";

$sth=$db->prepare($sql);

$sth->execute(array($tid));

if($result = $sth->fetchObject()){

    $teamusername=$result->realname;

    $school=$result->school;

    $department=$result->department;

    $cellphone=$result->cellphone;

    $email=$result->email;

    $category=$result->category;

    $reg_player=$result->reg_player;

    $data_checked=$result->data_checked;

    /*系隊項目

    if($permit{0}==0){

        if($myarr[$permit{1}]!=$category){

            echo "permission deny";

            exit();

        }

    }

    */

}else{

    echo "no such team";

    exit();

}





?>

<!DOCTYPE html>

<html lang="zh-tw">

<head>

    <meta charset="UTF-8">

    <title>view player</title>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
    <script>
        $(document).ready(function(){
            $(".rotate").on("click",function(e){

                e.preventDefault();

                var pid=$(this).data("pid");

                $.ajax({

                    type: "POST",

                    url: "<?php echo get_template_directory_uri(); ?>/rotate_act.php",

                    data: {"pid":pid},

                    success: function(data){

                        if(data.error){

                            alert(data.error);

                        }else{

                            //alert("rotate success");
                            location.reload();

                        }

                    },

                    error:function(){

                        alert("failed");

                    },

                    dataType:"json"

                });
            });
        });
    </script>
    <style>

    .single_player{

        position:relative;

        display:inline-block;

        width:650px;

        height:400px;

        margin:10px;

        box-shadow:0px 0px 2px 2px rgba(0,0,0,0.8);

        padding:5px;

        

    }

    .preview{

        



        width:180px;

        height:180px;

        background-size:contain;

        background-repeat:no-repeat;

        background-position:center;

    }

    .inf{

        float:right;

        height:400px;

        width:230px;

    }

    .myimg{

        float:left;

        width:370px;

        height:400px;

    }

    .myimg a{

        transition:all 0.2s ease 0.3s;

        

    }

    .myimg a:hover{

        transform:scale(2);

    }

    .number{

        position:absolute;

        bottom:0px;

        right:0px;

        opacity:0.3;

        font-family:"微軟正黑體",san-serif;

        font-size:50px;

    }

    .datac{

        transform:scale(4);

    }





    </style>

</head>

<body>

    









        

   

<?php

    $counter=0;

    $sql="select * from players where tid=?";

    $sth=$db->prepare($sql);

    $sth->execute(array($tid));

    while($result = $sth->fetchObject()):

    $counter++;

?>

    

    <div class="single_player">

       <div class="myimg">

            <a href="<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_head;?>" style="display:inline-block;"><div class="pic_head preview" style="background-image:url(<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_head;?>)"></div></a>

            <a href="<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_front;?>" style="display:inline-block;"><div class="pic_front preview" style="background-image:url(<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_front;?>)"></div></a>

            <a href="<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_back;?>" style="display:inline-block;"><div class="pic_back preview" style="background-image:url(<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_back;?>)"></div></a>

            <a href="<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_second;?>" style="display:inline-block;"><div class="pic_second preview" style="background-image:url(<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_second;?>)"></div></a>

        </div>

        <div class="inf">

            <div class="pid">pid: <?php echo $result->pid;?></div>

            <div class="realname">姓名: <?php echo $result->realname;?></div>

            <div class="stu_num">學號: <?php echo $result->stu_num?></div>

            <div class="birthday">生日(民國): <?php echo (substr($result->birthday,0,4)-1911).substr($result->birthday,4);?></div>

            <div class="id_num"><?php echo ($result->foreigner=="1"?"護照號碼: ":"身分證字號: ").$result->id_num;?></div>

            <div class="cellphone">手機: <?php echo $result->cellphone?></div>

            <div class="super">體資: <input type="checkbox" disabled <?php echo ($result->super=="1"?"checked":"");?>></div>

            <div class="foreign">非台籍: <input type="checkbox" disabled <?php echo ($result->foreigner=="1"?"checked":"");?>></div>

            <?php if($result->foreigner=="1"):?>

            <div class="country">國籍: <?php echo $result->country;?></div>

            <div class="gender">性別: <?php echo $result->gender;?></div>

            <div class="passport_name">護照姓名: <?php echo $result->passport_name;?></div>
            
            <?php endif;?>
            <button class="rotate" data-pid="<?php echo $result->pid;?>">旋轉大頭貼 <?php echo $result->pid;?></button><br/>

        </div>

        <div class="number">#<?php echo $counter;?></div>

        <div style="clear:both;"></div>

    </div>

    

<?php endwhile;?>   

<div class="footer">

<a href="" onclick="javascript:window.history.go(-1);">返回</a>

</div>



</body>

</html>

