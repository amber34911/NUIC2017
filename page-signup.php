<?php 

    require_once("php/settings/db_connect.php"); 

    require_once("php/settings/functions.php");

    testing(); 

    start_session(1209600); 

    if(isset($_SESSION["uid"])){

        header( 'Location: ../main' ); 

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

            modules: 'security',

            form: '#signup_form',

            onValidate: function(form){

               flag = false;

            }

        });

        $('#signup_form').ajaxForm({

            beforeSubmit: function(flag){
				$("#signup_form").find("input[type=submit]").attr('disabled','disabled').val("請稍後");
                return flag;    

            },

            dataType:'json',

            success:function(data){
				$("#signup_form").find("input[type=submit]").removeAttr( "disabled" ).val("送出");
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

<div class="center">

    <form action="<?php echo get_template_directory_uri(); ?>/signup_act.php" method="POST" id="signup_form">

        <p>*使用者名稱<br>

        <input type="text" name="username" data-validation="alphanumeric length" data-validation-allowing="_" data-validation-length="max32" maxlength="32" placeholder="username"></p>

        <p>*密碼<br>

        <input type="password" name="password1" data-validation="required length" data-validation-length="max128" maxlength="128" placeholder="password"></p>

        <p>*確認密碼<br>

        <input type="password" name="password2" data-validation="confirmation length" data-validation-length="max128" maxlength="128" placeholder="password"></p>

        <p>*真實姓名<br>

        <input type="text" name="realname" data-validation="required length" data-validation-length="max64" maxlength="64" placeholder="王小明"></p>

        <p>*學校<br>

        <input type="text" name="school" data-validation="required length" data-validation-length="max32" maxlength="32" placeholder="國立交通大學"></p>

        <p>*科系<br>

        <input type="text" name="department" data-validation="required length" data-validation-length="max32" maxlength="32" placeholder="資訊工程學系"></p>

        <p>*手機<br>

        <input type="text" name="cellphone" data-validation="number length" data-validation-length="max64" data-validation-error-msg="  手機格式不符" maxlength="64" placeholder="0912345678"></p>

        <p>*E-mail<br>

        <input type="text" name="email" data-validation="email length" data-validation-length="max64" maxlength="64" placeholder="nuic2017nctu@gmail.com"></p>

        <br>

        (*標示者為必填欄位)<br>

        <input type="submit" value="送出">

    </form>

    <br>

    <a href="../login">返回</a>

    </div>

    <br>



<?php get_footer(); ?>

