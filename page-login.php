<?php
/* 
Template Name:login
*/
    //require_once("php/settings/db_connect.php");
    require_once("php/settings/functions.php");
	testing();
    start_session(1209600);
    if(isset($_SESSION["uid"])){
        header( 'Location: ../main' ) ;
        //echo $_SESSION["uid"];
        //var_dump($_SESSION);
    }
    get_header();
?>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form-validator.js"></script>
    <script>
    $(document).ready(function() {  
        var flag = true;
        $.validate({
            form: '#login_form',
            onValidate: function(form){
               flag = false;
            }
        });
        $('#login_form').ajaxForm({
            beforeSubmit: function(flag){
                return flag;    
            },
            dataType:'json',
            success:function(data){
                if(data.error){
                    alert(data.error);
                }else{
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
        <form action="<?php echo get_template_directory_uri(); ?>/login_act.php" method="POST" id="login_form">
            <p>使用者名稱<br>
            <input type="text" name="username" data-validation="required" maxlength="32"></p>
            <p>密碼<br>
            <input type="password" name="password" data-validation="required" maxlength="128"></p>
            <br>
            <input type="submit" value = "登入">
        </form>
        <div class="clear"></div>
        <br>
        <a href="../signup">註冊</a> 
        &nbsp;<a href="../forget">忘記密碼</a><br>
        &nbsp;<a href="../regmethod">(使用說明)</a>
    </div>

<?php
    get_footer();
?>
