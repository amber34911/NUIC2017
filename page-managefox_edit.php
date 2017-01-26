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

    if($permit{0}!=3){

        echo "permission denied";

        exit();

    }

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

            $('#edit_form').ajaxForm({

                 beforeSubmit: function(){

                    return confirm("確定修改?");

                 },

                 dataType: 'json',

                 success: function (data) {

                     if(data.error){

                        alert(data.error);

                     }else{

                        alert(data.message);

                        location.reload();

                     }

                 },

                 error:function(){

                     alert("error");

                 }

             });

            $("input[type='text']").on("change",function(){

                $(this).siblings(".changed").val("1");

            });

            

            $("input[type='checkbox']").on("change",function(){

                $(this).siblings(".checkboxvalue").val($(this).is(":checked")?"1":"0");

                $(this).siblings(".changed").val("1");

            });

            $(".single_player").on("change", "input[type=file]", function () {

                var x = $(this).next();

                var reader = new FileReader();

                var file = $(this)[0].files[0];

                if(file){

                    reader.readAsDataURL(file);

                    reader.onload = function (e) {

                        x.css({"background-image":"url("+e.target.result+")"});

                    };

                }else{

                    x.css({"background-image":"url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"});

                }

                $(this).siblings(".changed").val("1");

            });

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
            $(".delete").on("click",function(e){

                e.preventDefault();

                var pid=$(this).data("pid");

                if(confirm("Are you sure you want to delete pid "+pid+"?")){

                    $.ajax({

                        type: "POST",

                        url: "<?php echo get_template_directory_uri(); ?>/managefox_deleteplayer_act.php",

                        data: {"pid":pid},

                        success: function(data){

                            if(data.error){

                                alert(data.error);

                            }else{

                                alert("delete success");

                            }

                        },

                        error:function(){

                            alert("failed");

                        },

                        dataType:"json"

                    });

                }else{

                    alert("failed");

                }

                location.reload();

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

            cursor:pointer;

            display:inline-block;

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

        label>input[type=file] {

            opacity:0;

            position:absolute;

            width:10px;

        }





    </style>

</head>

<body>

   <?php if($permit{0}==3):?>

    <div><a href="../managefox_team?tid=<?php echo $tid;?>">view</a></div>

    <div><a href="../managefox_add?tid=<?php echo $tid;?>">add player</a></div>

    <?php endif;?> 

    <form action="<?php echo get_template_directory_uri()?>/managefox_edit_act.php" method="POST" id="edit_form" enctype="multipart/form-data">

    

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

            <label>

                <input type='file' name="pic_head[]" data-validation="required extension" data-validation-allowing="jpg" />

                <div class="pic_head preview" style="background-image:url(<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_head;?>)"></div>

                <input type="hidden" name="pic_head_change[]" value="0" class="changed">

            </label>

            <label>

                <input type='file' name="pic_front[]" data-validation="required extension" data-validation-allowing="jpg" />

                <div class="pic_front preview" style="background-image:url(<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_front;?>)"></div>

                <input type="hidden" name="pic_front_change[]" value="0" class="changed">

            </label>

            <label>

                <input type='file' name="pic_back[]" data-validation="required extension" data-validation-allowing="jpg" />

                <div class="pic_back preview" style="background-image:url(<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_back;?>)"></div>

                <input type="hidden" name="pic_back_change[]" value="0" class="changed">

            </label>

            <label>

                <input type='file' name="pic_second[]" data-validation="required extension" data-validation-allowing="jpg" />

                <div class="pic_second preview" style="background-image:url(<?php echo get_template_directory_uri()."/upload/".$category."/".$tid."/".$result->pic_second;?>)"></div>

                <input type="hidden" name="pic_second_change[]" value="0" class="changed">

            </label>

            

            

        </div>

        <div class="inf">

            <div class="pid">pid: 

                <?php echo $result->pid;?>

                <input type="hidden" name="pid[]" value="<?php echo $result->pid;?>">

            </div>

            <div class="realname">姓名: 

                <input type="text" name="realname[]" value="<?php echo $result->realname;?>">

                <input type="hidden" name="realname_change[]" value="0" class="changed">

            </div>

            <div class="stu_num">學號: 

                <input type="text" name="stu_num[]" value="<?php echo $result->stu_num?>">

                <input type="hidden" name="stu_num_change[]" value="0" class="changed">

            </div>

            <div class="birthday">生日: 

                <input type="text" name="birthday[]" value="<?php echo $result->birthday;?>">

                <input type="hidden" name="birthday_change[]" value="0" class="changed">

            </div>

            <div class="id_num">身分證字號:

                <input type="text" name="id_num[]" value="<?php echo $result->id_num;?>">

                <input type="hidden" name="id_num_change[]" value="0" class="changed">

            </div>

            <div class="cellphone">手機: 

                <input type="text" name="cellphone[]" value="<?php echo $result->cellphone;?>">

                <input type="hidden" name="cellphone_change[]" value="0" class="changed">

            </div>

            <div class="super">體資: 

                <input type="checkbox" <?php echo ($result->super=="1"?"checked":"");?>>

                <input type="hidden" name="super[]" value="<?php echo $result->super; ?>" class="checkboxvalue">

                <input type="hidden" name="super_change[]" value="0" class="changed">

            </div>

            <div class="foreign">非台籍: 

                <input type="checkbox" <?php echo ($result->foreigner=="1"?"checked":"");?>>

                <input type="hidden" name="foreign[]" value="<?php echo $result->foreigner; ?>" class="checkboxvalue">

                <input type="hidden" name="foreign_change[]" value="0" class="changed">

            </div>

            <div class="country">國籍: 

                <input type="text" name="country[]" value="<?php echo $result->country;?>">

                <input type="hidden" name="country_change[]" value="0" class="changed">

            </div>

            <div class="gender">性別: 

                <input type="text" name="gender[]" value="<?php echo $result->gender;?>">

                <input type="hidden" name="gender_change[]" value="0" class="changed">

            </div>

            <div class="passport_name">護照姓名: 

                <input type="text" name="passport_name[]" value="<?php echo $result->passport_name;?>">

                <input type="hidden" name="passport_name_change[]" value="0" class="changed">

            </div>

            <a href="" class="rotate" data-pid="<?php echo $result->pid;?>">旋轉大頭貼 <?php echo $result->pid;?></a><br/>
            <a href="" class="delete" data-pid="<?php echo $result->pid;?>">delete <?php echo $result->pid;?></a>

        </div>

        <div class="number">#<?php echo $counter;?></div>

        <div style="clear:both;"></div>

    </div>

    

<?php endwhile;?>  

<br>

<input type="hidden" name="tid" value="<?php echo $tid;?>">

<input type="submit" value="edit">

</form> 

<div class="footer">

<a href="" onclick="javascript:window.history.go(-1);">返回</a>

</div>



</body>

</html>

