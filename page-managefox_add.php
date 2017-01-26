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
?>
<!DOCTYPE html>
<html lang="zh-tw">
<head>
    <meta charset="UTF-8">
    <title>view player</title>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery_ui/jquery-ui.js"></script>

    <script>
        $(document).ready(function(){
         $('#addplayer_form').ajaxForm({
             dataType: 'json',
             success: function (data) {
                 $("#addplayer_form").find("input[type=submit]").removeAttr('disabled');
                 if (data.error) {
                     
                     alert(data.error);
                     if(data.redirect){
                        redirect(data.redirect);
                     }
                 } else {
                     
                     alert("成功送出\n請再次確認資料是否正確");
                     history.go(-1);
                 }
             },
            error:function(errormessage){
                 $("#addplayer_form").find("input[type=submit]").removeAttr('disabled');
                 alert("新增隊員錯誤");
             }
         });
        
         $('#addplayer_form').on("keyup keypress", function(e) {
             var code = e.keyCode || e.which; 
             if (code  == 13) {               
                 e.preventDefault();
                return false;
             }
        });
         $("input[type='checkbox']").on("change",function(){
			$(this).next().val($(this).is(":checked")?"1":"0");
         });
        });
    
    </script>
    <style>
    </style>
</head>
<body>
    <form action="<?php echo get_template_directory_uri();?>/managefox_add_act<?php echo $target?>.php" method="POST" id="addplayer_form" enctype="multipart/form-data">
    <?php
        $sql="select teamname from teams where tid=?";        
        $sth=$db->prepare($sql);
        $sth->execute(array($tid));
        if($result = $sth->fetchObject()){
            $teamname = $result->teamname;
        }
    ?>
        <p>隊伍簡稱: <?php echo $teamname; ?>
        <input type="hidden" name="tid" value="<?php echo $tid;?>"> 
        </p>
        <div class="single_player">
           <div class="pictures">
                <div class="box preview-box">
                    <span class="fieldname">大頭照(.jpg)</span>
                    <label>
                        <input type='file' name="pic_head" data-validation="required extension" data-validation-allowing="jpg" />
                        
                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>
                        
                        <span class="help-block ">&nbsp;</span>
                    </label>
                </div>
                <div class="box preview-box">
                    <span class="fieldname">學生證正面(.jpg)</span>
                    <label>
                        <input type='file' name="pic_front" data-validation="required extension" data-validation-allowing="jpg" />
                        
                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>
                        
                        <span class="help-block ">&nbsp;</span>
                    </label>
                </div>
                <div class="box preview-box">
                    <span class="fieldname">學生證反面(.jpg)</span>
                    <label>
                        <input type='file' name="pic_back" data-validation="required extension" data-validation-allowing="jpg" />
                        
                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>
                        
                        <span class="help-block ">&nbsp;</span>
                    </label>
                </div>
                <div class="box preview-box">
                    <span class="fieldname">第二證件正面(.jpg)</span>
                    <label>
                        <input type='file' name="pic_second" data-validation="required extension" data-validation-allowing="jpg" />
                        
                        <div class="preview" style="background-image:url(<?php echo get_template_directory_uri(); ?>/image/upload.png)"></div>
                        
                        <span class="help-block ">&nbsp;</span>
                    </label>
                </div>
                <div class="clear"></div>
            </div>
            <div class="inf">
                <span class="fieldname">姓名</span>
                <input type="text" name="realname" data-validation="required length" data-validation-length="max64" maxlength="64">
                <br>
                <span class="fieldname">學號</span>
                <input type="text" name="stu_num" data-validation="required length alphanumeric" data-validation-allowing="_" data-validation-length="max32" maxlength="32">
                <br>
                <span class="fieldname">生日 (yyyy-mm-dd)</span>
                <input type="text" name="birthday" data-validation="birthdate" date-validation-format="yyyy-mm-dd">
                <br>
                <span class="fieldname" style="display:inline-block">體資/體保</span>
                <input type="checkbox" class="super">
                <input type="hidden" name="super" value="0">
                <span class="fieldname" style="display:inline-block">僑生/外籍生</span>
                <input type="checkbox" class="foreign" name="foreign_check">
                <input type="hidden" name="foreign" value="0">
                <br>
                <span class="fieldname">身份證字號</span>
                <input type="text" name="id_num" data-validation="identity required unique length alphanumeric" data-validation-allowing="_" data-validation-length="max32" maxlength="32">
                <br>
                <span class="fieldname">手機(選填)</span>
                <input type="text" name="cellphone" data-validation-optional="true" data-validation="number length" data-validation-length="max64" maxlength="64">
                <br>        
                
                <span class="fieldname foreign_inf">國籍</span>
                <input type="text" class="foreign_inf" name="country" maxlength="64" data-validation="required length" data-validation-length="max64" data-validation-if-checked="foreign_check">  
                <br>
                <span class="fieldname foreign_inf">性別</span>
                <input type="radio" class="foreign_inf" name="gender" value="m" data-validation="check_required" data-validation-if-checked="foreign_check"><span class="foreign_inf">男</span>
                <input type="radio" class="foreign_inf" name="gender" value="f" data-validation="check_required" data-validation-if-checked="foreign_check"><span class="foreign_inf">女</span>
                <br>
                <span class="fieldname foreign_inf">護照上姓名</span>
                <input type="text" class="foreign_inf" name="passport_name" maxlength="64" data-validation="required length" data-validation-length="max64" data-validation-if-checked="foreign_check">
                <br>
            </div>
            <div class="clear"></div>
        </div>
        <input type="submit" value="送出"><br>
        </form>
</body>
</html>
