
<?php 
    require_once("php/settings/db_connect.php"); 
    require_once("php/settings/functions.php"); 
    testing();
    start_session(1209600); 
    if(!isset($_SESSION["uid"])){
        header( 'Location: ../login' ) ; 
    }
    $username = $_SESSION["username"];
    $uid = $_SESSION["uid"];     
    $sql = "select * from users where username=? and uid=?";	
    $sth = $db->prepare($sql);	
    $sth->execute(array($username,$uid));
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
            form: '#edit_form',
            onValidate: function(form){
               flag = false;
            }
        });
        $('#edit_form').ajaxForm({
            beforeSubmit: function(flag){
                return flag;    
            },
            dataType:'json',
            success:function(data){
                if(data.error){
                    alert(data.error);
                }else{
                  // alert(data.message);
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
    
<?php
   if($result = $sth -> fetchObject())
   { 
?>

    <br>
    <div class="center">
    <form id="edit_form" action="<?php echo get_template_directory_uri(); ?>/edit_act.php" method="POST" >
        <input type="hidden" name="username" value="<?php echo $result -> username; ?>">
        <p>使用者名稱 <?php echo $result->username; ?> </p>
        <p>新密碼<br>
        <input type="password" name="password1" data-validation="length" data-validation-length="max128" value=""  maxlength="128"></p>
        <p>確認新密碼<br>
        <input type="password" name="password2" value="" data-validation="confirmation length" data-validation-length="max128" maxlength="128"></p>
        <p>真實姓名<br>
        <input type="text" name="realname" value="<?php echo $result -> realname; ?>" data-validation="required length" data-validation-length="max64" maxlength="64"></p>
        <p>學校<br>
        <input type="text" name="school" value="<?php echo $result -> school; ?>" data-validation="required length" data-validation-length="max32" maxlength="32"></p>
        <p>科系<br>
        <input type="text" name="department" value="<?php echo $result -> department; ?>" data-validation="required length" data-validation-length="max32" maxlength="32"></p>
        <p>手機<br>
        <input type="text" name="cellphone" value="<?php echo $result -> cellphone; ?>" data-validation="number length" data-validation-length="max64" data-validation-error-msg="  手機格式不符" maxlength="64"></p>
        <p>E-mail<br>
        <input type="text" name="email" value="<?php echo $result -> email; ?>" data-validation="email length" data-validation-length="max64" maxlength="64"></p>
        <br>
        <input type="submit" value="送出">
    </form>	
    
    <a href="../main">返回</a>
    </div>
<?php 	
   } 
?>
<?php get_footer(); ?>

