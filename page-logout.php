<?php
require_once( "php/settings/db_connect.php");
require_once( "php/settings/functions.php");
require_once( "php/settings/game_attribute.php");
testing();
start_session(1209600);
session_destroy();
header( "refresh:1; url=../login" ); 
get_header(); 
?>

成功登出!
(重新導向回登入頁面)
<?php get_footer(); ?>