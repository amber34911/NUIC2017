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
    header( 'Location: ../managefox_menu' ) ;
}
?>
<html lang="zh-tw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>大資盃隊伍管理</title>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>

<script>
    $(document).ready(function(){
         $('form').ajaxForm({
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
</head>
<body>
    請以系計中帳號登入<br><br>
    <form action="<?php echo get_template_directory_uri(); ?>/managefox_login_act.php" method="POST">
        帳號 <input type="text" name="id"><br>
        密碼 <input type="password" name="pw"><br>
        <input type="hidden" name="dep" value="cs"><br>
        <input type="submit" value="登入">
    </form>
</body>
</html>

