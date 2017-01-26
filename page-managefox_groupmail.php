<?php

require_once( "php/settings/db_connect.php");

require_once( "php/settings/functions.php");

require_once( "php/settings/game_attribute.php");

testing();

start_session(1209600);



$adminid=$_SESSION["adminid"];

$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");

$sql="select * from auth where admin=?";

$sth=$db->prepare($sql);

$sth->execute(array($adminid));

if($result = $sth->fetchObject()){

    $permit=$result->permit;

}else{

    echo "permission denied";

    exit();

}







?>

<!DOCTYPE html>

<html lang="zh-tw">

<head>

    <meta charset="UTF-8">

    <title>email</title>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.tablesorter.min.js"></script>

    <script>

        $(document).ready(function(){ 

            $('#email_form').ajaxForm({

             beforeSubmit: function(flag){

                return confirm("ready to send?");    

             },

             dataType: 'json',

             success: function (data) {

                 if (data.error) {

                     console.log(data.error);

                     if(data.redirect){

                        redirect(data.redirect);

                     }

                 } else {

                     console.log(data.message);

                     //window.history.go(-1);

                 }

             },

            error:function(){

                 alert("寄信出現錯誤 請聯絡管理員");

             }

         });

        }); 

    </script>

    <style>

        fieldset{

            width: 600px;

        }

    </style>

</head>

<body>

<form action="<?php echo get_template_directory_uri();?>/managefox_groupmail_act.php" method="post" id="email_form">

   <fieldset>

        <legend>收件者: </legend>

        <?php 

        foreach ($attr as $key => $value){

            echo "<input type='radio' value='".$key."' name='category'>";

            echo $value["chinese"]."<br>";

        }

        ?>

    </fieldset>



    <fieldset><legend>標題: </legend>

        <input type="text" name="title">

    </fieldset>



    <fieldset><legend>內容: </legend>

    <textarea name="content" cols="80" rows="10"></textarea><br> 

    </fieldset><br>

    <input type="submit" value="send email">

</form>

</body>

</html>



