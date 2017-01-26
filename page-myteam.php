<?php 

require_once( "php/settings/db_connect.php");

require_once( "php/settings/functions.php");

require_once( "php/settings/game_attribute.php");

testing();

start_session(1209600);

if(!isset($_SESSION[ "uid"])){

    header( 'Location: ../login' ) ;

}

$uid=$_SESSION["uid"];

get_header(); ?>

<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>

<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>

<script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script> 

<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form-validator.js"></script>



<script>

    var myLanguage = {

        requiredFields: '尚未新增匯款資訊'

    };



    $(document).ready(function(){

        var simple=0;

        if(typeof window.FileReader=="undefined"){

            simple=1-simple;

            $(".preview").addClass("hidepreview");

            $("#infpic_form input[type=file]").addClass("showfile");

            $(".simple").addClass("hidepreview");

            $(".code").removeAttr("readonly");

         }

        $.validate({

            language: myLanguage,

            errorMessagePosition: $('#message_position'),

            form: '#infpic_form',

            modules: 'file',

            onValidate: function(form){

                $('#message_position').text("");

            }

        });

 

        $(".edit_inf").data("state","edit").on("click",function(){

            inputbox=$(this).parent().find("input[type=text]");

            if($(this).data("state")=="edit"){

                $(this).text("Save");

                $(this).data("state","save");

                inputbox.prop( "disabled",false);

            }else if($(this).data("state")=="save"){

                $(this).text("Edit");

                $(this).data("state","edit");

                inputbox.prop( "disabled",true);

                $.ajax({

                    type: "POST",

                    url: "<?php echo get_template_directory_uri(); ?>/bddinf_act.php",

                    data: {

                        "action":"update",

                        "tid":$(this).data("tid"),

                        "money_inf":inputbox.val()

                    },

                    success: function(data){



                        if(data.error){

                            alert("更新匯款資料錯誤");

                            $("<input type='text' disabled></input>").val(data.ori_money_inf).insertAfter(inputbox);

                            inputbox.remove();

                            $(".server_message").text(data.error).addClass("server_error");

                        }else{

                            inputbox.attr("value",data.money_inf);

                            $(".server_message").text(data.message).removeClass("server_error");

                        }

                    },

                    dataType:"json"

                });

            }

        });



        

        $('#infpic_form').ajaxForm({

            beforeSubmit:function(){

                $("#infpic_form").find("input[type=submit]").attr('disabled','disabled');

                $(".code").val("loading...");

            },

            dataType: 'json',

            success: function (data) {

                if (data.error) {

                    $("#infpic_form").find("input[type=submit]").removeAttr('disabled');

                    alert(data.error);

                    $(".code").val("please try again...");

                    if(data.redirect){

                        redirect(data.redirect);

                    }

                } else {

                    $("#infpic_form").find("input[type=submit]").attr('disabled','disabled');

                    $(".code").val(data.message);

                }

            },

            error:function(){

                alert("error occored");

                $(".code").val("please try again...");

            }

        });

            

        $("#infpic_form").on("change", "input[type=file]", function () {

            if(!simple){

                var x = $(this).next();

                var reader = new FileReader();

                var file = $(this)[0].files[0];

                reader.readAsDataURL(file);

                reader.onload = function (e) {

                    x.css({"background-image":"url("+e.target.result+")"});

                };

            }

            $("#infpic_form").find("input[type=submit]").removeAttr('disabled');

        });

        $(".code").click(function(){

            this.select();

        });

        $("td>input[type=text]").click(function(){

            $(this).select();

        });

        $(".simple").on("click",function(e){

            e.preventDefault();

            simple=1-simple;

            $(".preview").addClass("hidepreview");

            $("#infpic_form input[type=file]").addClass("showfile");

            $(this).addClass("hidepreview");

            $(".code").removeAttr("readonly");



        });

    });

    

</script>

<style>

    .nav {

        width: 173px;

        float: left;

      

    }

    .team {

        width:73%;

        width: calc(100% - 200px);

        float: right;



        

    }

    .form-error{

        color:rgb(230,50,50);

    }



    @media screen and (max-width: 800px){

        .nav {

            width: 100%;

            float: none;

            text-align:center;

        }

        .team {

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



        .team {

            width: 100%;

        }

        

        .nav a[href*=category] li{

            display:block;

            width:100%;

            padding:5px 5px;

        }

        

    }

    @media screen and (max-width: 519px){

        .code{

            width:100%!important;

        }

        

        

    }

    .myteams{

        width:100%;

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

    .preview{

            width:110px;

            height:110px;

	    margin-right: 21px;
            background-size:contain;

            background-repeat: no-repeat;

            background-position:center center;

            cursor:pointer;

            display:inline-block;

        }

    label>input[type=file] {

        display: none;

    }

    .code{

        width:400px;

    }

    #infpic_form{

        box-shadow:0px 0px 2px rgb(200,200,200);

    }

    .preview_box,.inf_box{

        float:left;

    }

    .inf_box{

    	width: 75%;
	margin-top: 21px;
        margin-right:21px;

    }

    .simple_p{

        text-align:center;

    }

    .hidepreview{

        display:none!important;

    }

    .showfile{

        display:inline-block!important;

    }

    .edit_inf {
	margin: 10px;
    }
    .inf_back {
	margin-right: 20px;
	float: right;
    }

</style>



<div class="main">

    <div class="nav">

        <?php get_menu();?>

    </div>

    <div class="team">

       <h2>我的隊伍</h2>

       <hr>

        <table id="myteams" <?php echo (isset($_SESSION[ "uid"])? "": "style='display:none;'") ?>>



            <?php 

            $first=1;

            $sql="select * from teams where uid=?" ;

            $sth=$db->prepare($sql);

            $sth->execute(array($uid));

            while($result = $sth->fetchObject()){

                if ($first==1){ $first=0; echo "

                    <tr>

                        <th>報名項目</th>

                        <th>隊伍名稱</th>

                        <th>匯款資訊</th>

                        <th>資料確認</th>

                        <th>繳費確認</th>

                        <th>報名確認</th>

                    </tr>";

                }

                $t=new DateTime($result->timestamp);

                echo "<tr>";

                echo "<td>".$attr[$result->category]["chinese"]."</td>";

                echo "<td><a href='../viewteam?tid=".$result->tid."'>".$result->teamname."</a></td>";

                // echo "<td>".$t->format("Y-m-d")."</td>";

                echo "<td><input type='text' disabled value=".$result->money_inf." ><button class='edit_inf' data-tid='".$result->tid."'>Edit</button></td>";

                echo "<td>".($result->data_checked==1?'已驗證':'尚未確認')."</td>";

                echo "<td>".($result->money_num!=999?'已繳費':'尚未確認')."</td>";

                echo "<td>".($result->success==1?'報名成功':'尚未確認')."</td>";

                echo "</tr>"; }

                if ($first==1){

                    echo "尚未新增隊伍";

                } 

            ?>

        </table>

        <?php if($first!=1){ ?>

        <div class="server_message"></div>

        <div class="generator">

            <form action="<?php echo get_template_directory_uri(); ?>/bddinf_pic_act.php" id="infpic_form" method="POST" enctype="multipart/form-data">

                <div class="preview_box">

                    <label>

                        <input type='file' name="pic_inf" data-validation="required extension" data-validation-allowing="jpg"/>

                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>

                    </label>

                    <span id="message_position" style="text-align: center;"></span>

                    <div class="simple_p"><a class="simple">關閉預覽</a></div>

                </div>

                <div class="inf_box">

                    欲<span style="color:red;">新增匯款資訊</span>，請先點選左邊圖示選取匯款收據照片，按下送出後將代碼全選複製，<br>

                    再點選隊伍旁的 Edit 鈕，貼上代碼後再按 Save 即可<br>

                    <input type="submit"  value="產生代碼"><input type="text" class="code" readonly >

                </div>

                <div class="clear"></div>

           </form>

           

        </div>

        <?php } ?>

        <br/>

        <a href='../main' class="inf_back">返回</a>

    </div>

    <div class="clear"></div>

</div>



<?php



	get_footer();

?>

