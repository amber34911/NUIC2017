<?php
    require_once("php/settings/db_connect.php");
    require_once("php/settings/functions.php");
    testing();
    start_session(1209600);
    if(isset($_SESSION["uid"])){
        header( 'Location: ../main' ) ; 
    }
    get_header();
?>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form-validator.js"></script>
    <script>
    var myLanguage = {
        lengthTooLongStart: '  此欄位不得超過',
        lengthBadEnd: '字元',
        badAlphaNumeric: '  此欄位僅限填英數字及底線',
        badAlphaNumericExtra: ''
    };
    $(document).ready(function() {  
        var flag = true;
        $.validate({
            language: myLanguage,
            form: '#forget_form',
            onValidate: function(form){
               flag = false;
            }
        });
        $('#forget_form').ajaxForm({
            beforeSubmit: function(flag){
                return flag;    
            },
            dataType:'json',
            success:function(data){
                if(data.error){
                    alert(data.error);
                }else{
                    alert(data.message);
                    redirect("../"+data.redirect);
                }
            }
        }); 
    }); 
    </script>
    <style>
        .center{
            width:154px;
            margin:0 auto;   
            padding:20px 60px;
            box-shadow:2px 3px 10px rgba(0,0,0,0.1);
            border-radius:5px;
        }

    </style>
    <p style="text-align:center;">請正確填寫下列資料，系統將寄送新的密碼到您的信箱</p>
    <div class="center">
       
        <form action="<?php echo get_template_directory_uri(); ?>/forget_act.php" method="POST" id="forget_form">
            <p>* 使用者名稱<br>
            <input type="text" name="username" maxlength="32" data-validation="alphanumeric length" data-validation-allowing="_" data-validation-length="max32"></p>
            <p>* 真實姓名<br>
            <input type="text" name="realname" maxlength="64" data-validation="required length" data-validation-length="max64"></p>
            <p>* 手機<br>
            <input type="text" name="cellphone" maxlength="64" data-validation="number length required" data-validation-length="max64" data-validation-error-msg="  手機格式不符"></p>
            <p>* E-mail<br>
            <input type="text" name="email" maxlength="64" data-validation="email length required" data-validation-length="max64"></p>
            <br>(*標示者為必填欄位)
            <br>
            <input type="submit" value = "送出">
        </form>
        <br>
        <a href="/main">返回</a>
    </div>
<?php
    get_footer();
?>
