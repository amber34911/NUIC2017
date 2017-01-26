<?php 

require_once("php/settings/game_attribute.php");

require_once("php/settings/db_connect.php");

require_once("php/settings/functions.php");

testing();

$category=$_GET["category"];



start_session(1209600);

if(!isset($_SESSION["uid"])){

    header( 'Location: ../login' ) ; 

}

$uid=$_SESSION["uid"];

$sql = "select * from users where uid=?";

$sth = $db->prepare($sql);

$sth->execute(array($uid));

$result = $sth->fetchObject();

$veri=$result->veri_state;

if($veri!=1){

    header( "refresh:2; url=../main" ); 

    get_header();

    echo "尚未啟用帳號，無法參與報名";

    get_footer();

    exit();

}

$games=array("b_bas","g_bas","b_vol","g_vol","bad","tab","sof","soc");

$target="";

if(!in_array($category,$games)){

    header( "refresh:1; url=../main" ); 

    get_header();

    echo "無此項目，請重新選擇欲報名項目";

    get_footer();

    exit();

}else{

    $sql = "select count(*) as c from teams where uid=? and category=?";

    $sth = $db->prepare($sql);

    $sth->execute(array($uid,$category));

    $result = $sth->fetchObject();
/*
    if(beforefirst()){

        get_header();

        echo "第一階段尚未開放報名<br>";

        echo "<a href='../main'>返回</a>";

        get_footer();

        exit();

    

    }elseif(infirst()){

        if($result->c>0){

            get_header();

            echo "您第一階段已經在此項目報名過一隊，請等待第二階段<br>";

            echo "<a href='../main'>返回</a>";

            get_footer();

            exit();

        }

    }elseif(afterfirst()&&beforesecond()){

        get_header();

        echo "第一階段報名結束，第二階段尚未開放報名<br>";

        echo "<a href='../main'>返回</a>";

        get_footer();

        exit();

    }elseif(insecond()){

        if($result->c==1){

            $target=2;

        }elseif($result->c==2){

            get_header();

            echo "您已經在此項目報名兩隊，無法繼續報名<br>";

            echo "<a href='../main'>返回</a>";

            get_footer();

            exit();

        }

    

    }elseif(aftersecond()){

        $target=3;

    }
*/
    $sql = "select count(*) as c from teams where success=1 and category=?";

    $sth = $db->prepare($sql);

    $sth->execute(array($category));

    $result = $sth->fetchObject();

    if($result->c>=$attr[$category]["max_team"]){

        get_header();

        echo "已報名隊數已達上限，無法繼續報名";

        get_footer();

        exit();

    }

    get_header();

    echo "<h2>".$attr[$category]["chinese"]."報名</h2>";

    echo "<p>每隊人數上限: ".$attr[$category]["max_player"]."人,下限: ".$attr[$category]["min_player"]."人</p>";

}



?>

   

   

    <link rel="stylesheet" href="/js/jquery_ui/jquery_ui.css">

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery_ui/jquery-ui.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form-validator.js"></script>

    <script>

    var myLanguage = {

         requiredFields: ' 尚有未填的必填欄位',   

         badInt: '  手機格式不符',

         lengthTooLongStart: ' 輸入不得超過',

         lengthBadEnd: '字元',

         badAlphaNumeric: ' 限填英數字及',

         badAlphaNumericExtra: ''

    };

    function checkID(id){

        

        //id=id.toUpperCase();

        tab = "ABCDEFGHJKLMNPQRSTUVXYWZIO";

        A1 = new Array (1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,3,3,3,3,3,3 );

        A2 = new Array (0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5 );

        Mx = new Array (9,8,7,6,5,4,3,2,1,1);

        if ( id.length != 10 ) return false;

        i = tab.indexOf( id.charAt(0) );

        if ( i == -1 ) return false;

        sum = A1[i] + A2[i]*9;

        for ( i=1; i<10; i++ ) {

            v = parseInt( id.charAt(i) );

            if ( isNaN(v) ) return false;

            sum = sum + v * Mx[i];

        }

        if ( sum % 10 != 0 ) return false;

        return true;

    };

    $(document).ready(function () {

         var simple=0;

        //the total_player counter I maintain

         var total_player=<?php echo $attr[$category]["min_player"] ?>;//initialize

        // set the initialized value into form

         var player_check = document.getElementById("total_player");

         player_check.value = total_player;

         

         var flag = true;

         if(typeof window.FileReader=="undefined"){

              simple=1-simple;

              $(".preview").toggle();

              $("input[type=file]").toggleClass("block");

              $(".box").toggleClass("preview-box");

              $(".simple").parent().remove();

         }

         numbering();

         $.formUtils.addValidator({

             name: 'teamname',

             validatorFunction: function(value, element, params){

                 var teamname = $(element).val();

                 var category = "<?php echo $category; ?>";

                 var check = false;

                 $.ajax({

                     type: 'POST',

                     async: false,

                     url: "<?php echo get_template_directory_uri();?>/teamname_check_act.php",

                     dataType: 'json',

                     data: { 'teamname': teamname, 'category': category },

                     success: function(data){

                         if(data.teamname_check===true) check=true;

                     }

                });

                return check;

             },

             errorMessage: '  此隊伍名稱已有人使用',

             errorMessageKey: 'badTeamname'

         });

         $.formUtils.addValidator({

             name: 'super_count',

             validatorFunction: function(value, element, params){

                 var count=0;

                 var selector = $(element).parent().siblings().find("input[name|='super[]']");

                 $(selector).each(function(index, item){

                     if($(item).val()==="1") count++;

                 });

                 return count <= <?php echo $attr[$category]["max_super"]; ?>;

             },

             errorMessage: '  體資/體保生不得超過<?php echo $attr[$category]["max_super"]; ?>人',

             errorMessageKey: 'badSuper'

         });

         $.formUtils.addValidator({

             name: 'unique',

             validatorFunction: function(value, element, params){

                 var prefix = params;

                 var matches = new Array();

                 var selector = $(element).parent().parent().siblings().find("input[name|='id_num[]']");

                 $(selector).each(function(index, item){

                     if(value == $(item).val()){

                         matches.push(item);

                     }

                 });

                 return matches.length === 0;

             },

             errorMessage: '  身份證字號不可重複',

             errorMessageKey: 'notUnique'

         });

         $.formUtils.addValidator({

             name: 'identity',

             validatorFunction: function(value, element, params){

                 if($(element).siblings('.foreign').is(':checked')){

                    return true;

                 }

                 else return checkID(value);

             },

             errorMessage: '  身份證字號格式不符',

             errorMessageKey: 'badId'

         });

         $.formUtils.addValidator({

             name: 'check_required',

             validatorFunction: function(value, element, params){

                var one = $(element);

                var another = one.siblings("input[name='"+one.attr("name")+"']");

                return one.is(':checked') || another.is(':checked');

             },

             errorMessage: '  尚有未選的必填欄位',

             errorMessageKey: 'badChecked'

         });

         $.validate({

             language: myLanguage,

             form: '.single_player, #addteam_form',

             modules: 'file, date',

             onValidate: function(form){

                flag = false;

             }

         });

       /*==================================== 

         check validator result at console

         $('input').bind('beforeValidation', function(){

             console.log('Input "'+this.name+'" is about to become validated');

         }).bind('validation',function(evt,isValid){

             console.log('Input "'+this.name+'" is'+(isValid?'VALID':'INVALID'));

         });

        =======================================*/

        var bar = $('.pbar');

        var percent = $('.percent');



         $('#addteam_form').ajaxForm({

             beforeSubmit: function(flag){

                var percentVal = '上傳中... 0%';

                bar.width(percentVal)

                percent.html(percentVal);

                return flag;    

             },uploadProgress: function(event, position, total, percentComplete) {

                $(".mask").addClass("show_block");

                $("#addteam_form").find("input[type=submit]").attr('disabled','disabled');

                var percentVal = "上傳中... "+percentComplete + '%';

                bar.width(percentComplete+'%');

                percent.html(percentVal);

                if(percentComplete==100){

                    percent.html("處理中 請稍候...");

                }

                //console.log(percentVal, position, total);

             },

             dataType: 'json',

             success: function (data) {

                 $(".mask").removeClass("show_block");

                 $("#addteam_form").find("input[type=submit]").removeAttr('disabled');

                 if (data.error) {

                     $.ajax({

                         type: 'POST',

                         url: "<?php echo get_template_directory_uri();?>/managefox_errorlog_act.php",

                         dataType: 'json',

                         data: { 'message': data.error },

                         success: function(data){

                         },

                         error:function(){

                         }

                     });

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

             },

            error:function(errormessage){

                $.ajax({

                     type: 'POST',

                     url: "<?php echo get_template_directory_uri();?>/managefox_errorlog_act.php",

                     dataType: 'json',

                     data: { 'message': errormessage },

                     success: function(data){

                     },

                     error:function(){

                     }

                 });

                 $("#addteam_form").find("input[type=submit]").removeAttr('disabled');

                 $(".mask").removeClass("show_block");

                 alert("報名出現錯誤 請聯絡管理員");

             }

         });

        

         $('#addteam_form').on("keyup keypress", function(e) {

             var code = e.keyCode || e.which; 

             if (code  == 13) {               

                 e.preventDefault();

                return false;

             }

        });

        $("#add").on("click", function () {

         total_player++;

         player_check.value = total_player;



         $("#template").children().clone(true).insertBefore($("#add_anchor"));

         numbering();



        });





        $(".single_player").on("change", "input[type=file]", function () {

         if(simple==0){

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

         }

        });





        $(".simple").on("click",function(e){

          e.preventDefault();

          simple=1-simple;

          $(".preview").toggle();

          $("input[type=file]").toggleClass("block");

          $(".box").toggleClass("preview-box");

          $(this).parent().remove();



        });



        $(".single_player").on("change", ".super", function () {

            $(this).next("input[type=hidden]").val($(this).is(":checked")?1:0);

   /*         if($(this).is(":checked")){

                $('#total_super').val(parseInt($('#total_super').val(),10)+1);

            }else{

                $('#total_super').val(parseInt($('#total_super').val(),10)-1);

            }*/

        });

        $(".single_player").on("change", ".foreign", function () {

             $(this).next("input[type=hidden]").val($(this).is(":checked")?1:0);



             if($(this).is(":checked")){

                 $(this).siblings(".foreign_inf").addClass("show_opacity");

             }else{

                $(this).siblings(".foreign_inf").removeClass("show_opacity");

             }



             $(this).siblings(".fieldname").eq(5).text($(this).is(":checked")?"護照號碼":"身分證字號");

         });

         $(".delete").on("click",function(){

             total_player--;

             player_check.value = total_player;

             

	         $(this).parent().parent().remove();

             numbering();

         });

         

     });

    function numbering(){

        var players=$(".single_player");

        //alert(players.length);

        for(i=0;i<players.length;i++){

            players.eq(i).find(".num").remove();

            if(i==1){

                var h=$("<span class='num'></span>").text("#"+(i)+"(隊長)");

            }else{

                var h=$("<span class='num'></span>").text("#"+(i));

            }

            players.eq(i).prepend(h);

            if(i==1){

                players.eq(i).find("input[name|='cellphone[]']").attr("data-validation-optional", "false");  // 隊長手機為必填

                players.eq(i).find(".inf span:nth-child(20)").text("*手機(必填)");

            }

            // 外籍生 

            var checkbox="foreign_check"+(i);

            players.eq(i).find(".foreign").attr("name", checkbox);

            players.eq(i).find("input[type=radio]").attr("name","gender["+(i-1)+"]");

            players.eq(i).find("input[type=radio]").attr("data-validation-if-checked", checkbox);

            players.eq(i).find("input[name|='passport_name[]']").attr("data-validation-if-checked", checkbox);

            players.eq(i).find("input[name|='country[]']").attr("data-validation-if-checked", checkbox);

        }

    }

    </script>

    <!--[if lt IE 9]>

    <style>

    .single_player{

        border:1px solid black;

    }

    </style>

    <![endif]-->

    <style>

        .single_player {

            transition:opacity 0.2s ease;

            opacity:0.8;

            display: inline-block;

            margin: 10px;

            padding:20px;

            box-shadow:2px 2px 10px rgba(0,0,0,0.1);

        }

        

        .single_player:hover{

            opacity:1;

        }



        label>input[type=file] {

            opacity:0;

            position:absolute;

            width:10px;

        }

        

        label>img {

            width: 32px;

            cursor: pointer;

        }

        .fieldname{

            display:block;

            margin-top:5px;

        }

        .preview{

            width:110px;

            height:110px;

            background-size:contain;

            background-repeat: no-repeat;

            background-position:center center;

            cursor:pointer;

        }

        .pictures{

            width:250px;

            float:left;

        }

        .box,.inf{

            float:left;

        }

        .preview-box{

            width:110px;

            

        }

        .delete{

            float:right;

        }

        .num{

            display:block;

        }

        .super,.foreign{

            margin-top: 10px;

            transform-origin: 0px 0px;

            transform: scale(1.4);

        }

        .form-error{

            color:rgb(230,50,50);

        }

        .foreign_inf{

            transition:all 0.3s ease;

            opacity:0;

            visibility:hidden;

        }

        .show_opacity{

            opacity:1 !important;

            visibility:visible!important;

        }

        .show_block{

            opacity:1 !important;

            display:block !important;

        }

        .progress {

            position:relative;

            width:400px;

            border: 1px solid #ddd;

            padding: 1px;

            border-radius: 3px;

            margin:0 auto;

            

        }

        .pbar {

            background-color: #B4F5B4;

            width:0%;

            height:29px;

            border-radius: 3px;

        }

        .percent {

            position:absolute;

            display:inline-block;

            top:5px;

            left:43%;

        }

        .mask{

            display:none;

            position:fixed;

            background-color: rgba(0,0,0,0.8);

            opacity:0;

            transition:all 0.2s ease;

            top:0px;

            left:0px;

            right:0px;

            bottom:0px;

            z-index:99;

        }

        .vcenter{

            position:absolute;

            top:45%;

            width: 100%;

        }



        .block{

            display:block !important;

            width:100% !important;

            opacity:1 !important;

            position:static !important;

        }

        

    </style>

    <div style="display:none" id="template">

        <div class="single_player">

           <div class="pictures">

                <div class="box preview-box">

                    <span class="fieldname">大頭照(.jpg)</span>

                    <label>

                        <input type='file' name="pic_head[]" data-validation="required extension" data-validation-allowing="jpg" />

                        

                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>

                        

                        <span class="help-block ">&nbsp;</span>

                    </label>

                </div>

                <div class="box preview-box">

                    <span class="fieldname">學生證正面(.jpg)</span>

                    <label>

                        <input type='file' name="pic_front[]" data-validation="required extension" data-validation-allowing="jpg" />

                        

                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>

                        

                        <span class="help-block ">&nbsp;</span>

                    </label>

                </div>

                <div class="box preview-box">

                    <span class="fieldname">學生證反面(.jpg)</span>

                    <label>

                        <input type='file' name="pic_back[]" data-validation="required extension" data-validation-allowing="jpg" />

                        

                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>

                        

                        <span class="help-block ">&nbsp;</span>

                    </label>

                </div>

                <div class="box preview-box">

                    <span class="fieldname">第二證件正面(.jpg)</span>

                    <label>

                        <input type='file' name="pic_second[]" data-validation="required extension" data-validation-allowing="jpg" />

                        

                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>

                        

                        <span class="help-block ">&nbsp;</span>

                    </label>

                </div>

                <div class="clear"></div>

            </div>

            <div class="inf">

                <span class="fieldname">姓名</span>

                <input type="text" name="realname[]" data-validation="required length" data-validation-length="max64" maxlength="64">

                <br>

                <span class="fieldname">學號</span>

                <input type="text" name="stu_num[]" data-validation="required length alphanumeric" data-validation-allowing="_" data-validation-length="max32" maxlength="32">

                <br>

                <span class="fieldname">生日 (yyyy-mm-dd)</span>

                <input type="text" name="birthday[]" data-validation="birthdate" date-validation-format="yyyy-mm-dd">

                <br>

                <span class="fieldname" style="display:inline-block">體資/體保</span>

                <input type="checkbox" class="super">

                <input type="hidden" name="super[]" value="0">

                <span class="fieldname" style="display:inline-block">僑生/外籍生</span>

                <input type="checkbox" class="foreign" name="foreign_check">

                <input type="hidden" name="foreign[]" value="0">

                <br>

                <span class="fieldname">身份證字號</span>

                <input type="text" name="id_num[]" data-validation="identity required unique length alphanumeric" data-validation-allowing="_" data-validation-length="max32" maxlength="32">

                <br>

                <span class="fieldname">手機(選填)</span>

                <input type="text" name="cellphone[]" data-validation-optional="true" data-validation="number length" data-validation-length="max64" maxlength="64">

                <br>        

                <span class="fieldname foreign_inf">國籍</span>

                <input type="text" class="foreign_inf" name="country[]" maxlength="64" data-validation="required length" data-validation-length="max64" data-validation-if-checked="foreign_check">   

                <br>

                <span class="fieldname foreign_inf">性別</span>

                <input type="radio" class="foreign_inf" name="gender" value="m" data-validation="check_required" data-validation-if-checked="foreign_check"><span class="foreign_inf">男</span>

                <input type="radio" class="foreign_inf" name="gender" value="f" data-validation="check_required" data-validation-if-checked="foreign_check"><span class="foreign_inf">女</span>

                <br>

                <span class="fieldname foreign_inf">護照上姓名</span>

                <input type="text" class="foreign_inf" name="passport_name[]" maxlength="64" data-validation="required length" data-validation-length="max64" data-validation-if-checked="foreign_check">     

                <br>

                <a class="delete">刪除隊員</a>

            </div>

            <div class="clear"></div>

        </div>

    </div>

    <span>若是使用上覺得卡卡的,可以切換至 <a class="simple">精簡模式</a></span>

    <br>

    傳輸速度取決於您的圖片大小以及頻寬，建議將圖片先縮至1000px*1000px以內

    <br>

    <br>

    <form action="<?php echo get_template_directory_uri();?>/superteam_act3.php" method="POST" id="addteam_form" enctype="multipart/form-data">

        

        <p>隊伍簡稱

        <input type="text" name="teamname" id="teamname" data-validation="required length teamname" data-validation-length="max32"><br>

        欲報名第二隊者請在後面加上A/B<br>

        為了識別方便，命名請以校系名稱為主。(如: 交大資工A)

        </p>

        <p><input type="hidden" name="total_player" id="total_player" data-validation="number" data-validation-allowing="range[<?php echo $attr[$category]["min_player"]; ?>;<?php echo $attr[$category]["max_player"]; ?>]" data-validation-error-msg="隊員數量不符"></p>

        <p><input type="hidden" name="total_super" id="total_super" data-validation="super_count" value="0"></p>

        <?php

            for($i=0;$i<$attr[$category]["min_player"];$i++){

        ?>

        <div class="single_player">

           <div class="pictures">

                <div class="box preview-box">

                    <span class="fieldname">大頭照(.jpg)</span>

                    <label>

                        <input type='file' name="pic_head[]" data-validation="required extension" data-validation-allowing="jpg" />

                        

                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>

                        

                        <span class="help-block ">&nbsp;</span>

                    </label>

                </div>

                <div class="box preview-box">

                    <span class="fieldname">學生證正面(.jpg)</span>

                    <label>

                        <input type='file' name="pic_front[]" data-validation="required extension" data-validation-allowing="jpg" />

                        

                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>

                        

                        <span class="help-block ">&nbsp;</span>

                    </label>

                </div>

                <div class="box preview-box">

                    <span class="fieldname">學生證反面(.jpg)</span>

                    <label>

                        <input type='file' name="pic_back[]" data-validation="required extension" data-validation-allowing="jpg" />

                        

                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>

                        

                        <span class="help-block ">&nbsp;</span>

                    </label>

                </div>

                <div class="box preview-box">

                    <span class="fieldname">第二證件正面(.jpg)</span>

                    <label>

                        <input type='file' name="pic_second[]" data-validation="required extension" data-validation-allowing="jpg" />

                        

                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>

                        

                        <span class="help-block ">&nbsp;</span>

                    </label>

                </div>

                <div class="clear"></div>

            </div>

            <div class="inf">

                <span class="fieldname">姓名</span>

                <input type="text" name="realname[]" data-validation="required length" data-validation-length="max64" maxlength="64">

                <br>

                <span class="fieldname">學號</span>

                <input type="text" name="stu_num[]" data-validation="required length alphanumeric" data-validation-allowing="_" data-validation-length="max32" maxlength="32">

                <br>

                <span class="fieldname">生日 (yyyy-mm-dd)</span>

                <input type="text" name="birthday[]" data-validation="birthdate" date-validation-format="yyyy-mm-dd">

                <br>

                <span class="fieldname" style="display:inline-block">體資/體保</span>

                <input type="checkbox" class="super">

                <input type="hidden" name="super[]" value="0">

                <span class="fieldname" style="display:inline-block">僑生/外籍生</span>

                <input type="checkbox" class="foreign" name="foreign_check">

                <input type="hidden" name="foreign[]" value="0">

                <br>

                <span class="fieldname">身份證字號</span>

                <input type="text" name="id_num[]" data-validation="identity required unique length alphanumeric" data-validation-allowing="_" data-validation-length="max32" maxlength="32">

                <br>

                <span class="fieldname">手機(選填)</span>

                <input type="text" name="cellphone[]" data-validation-optional="true" data-validation="number length" data-validation-length="max64" maxlength="64">

                <br>        

                

                <span class="fieldname foreign_inf">國籍</span>

                <input type="text" class="foreign_inf" name="country[]" maxlength="64" data-validation="required length" data-validation-length="max64" data-validation-if-checked="foreign_check">  

                <br>

                <span class="fieldname foreign_inf">性別</span>

                <input type="radio" class="foreign_inf" name="gender" value="m" data-validation="check_required" data-validation-if-checked="foreign_check"><span class="foreign_inf">男</span>

                <input type="radio" class="foreign_inf" name="gender" value="f" data-validation="check_required" data-validation-if-checked="foreign_check"><span class="foreign_inf">女</span>

                <br>

                <span class="fieldname foreign_inf">護照上姓名</span>

                <input type="text" class="foreign_inf" name="passport_name[]" maxlength="64" data-validation="required length" data-validation-length="max64" data-validation-if-checked="foreign_check">

                <br>

                <a class="delete">刪除隊員</a>

            </div>

            <div class="clear"></div>

        </div>

        <?php

            }

        ?>

        

        <div id="add_anchor"><a id="add">新增隊員</a></div>

        <input type="hidden" name="category" value="<?php echo $category?>">

        <br>

        <input type="submit" value="送出"><br>

        <div class="mask">

            <div class="vcenter">

                <div class="progress">

                    <div class="pbar"></div>

                    <div class="percent">上傳中... 0%</div>

                </div>

            </div>

        </div>

        

    </form>

    <a href="../main">返回</a>

    

<?php get_footer(); ?>

