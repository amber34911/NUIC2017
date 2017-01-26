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

if(!isset($_GET["tid"])){

    echo "permission denied";

    exit();

}

$tid=$_GET["tid"];

# tid check

$sql = "select * from teams join users on users.uid=teams.uid where tid=? ";

$sth = $db->prepare($sql);

$sth->execute(array($tid));

$result = $sth->fetchObject();

if($result==false){

    echo "error: undefined tid";

    exit();

}

else{

    $teamname=$result->teamname;

    $category = $result->category;

    $realname=$result->realname;

    $email=$result->email;

    if($permit{0}==0&&$myarr[$permit{1}]!=$category){

        echo "permission denied";

        exit;

    }

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

            $("[name=title]").on("change",function(){

                if($("[name=title]").eq(<?php echo ($permit{0}==3?"2":"1");?>).is(":checked")){

                    

                    $("[name=title_custom]").removeAttr('disabled');

                }else{

                    $("[name=title_custom]").attr('disabled', '');

                }

            });

            $("[name=title_custom]").on("change",function(){

                

                $("[name=title]").eq(<?php echo ($permit{0}==3?"2":"1");?>).val($(this).val());

            });

            

            $('#email_form').ajaxForm({

             beforeSubmit: function(flag){

                return flag;    

             },

             dataType: 'json',

             success: function (data) {

                 if (data.error) {

                     alert(data.error);

                     if(data.redirect){

                        redirect(data.redirect);

                     }

                 } else {

                     alert(data.message);

                     window.history.go(-1);

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

<form action="<?php echo get_template_directory_uri();?>/managefox_email_act.php" method="post" id="email_form">

    <fieldset><legend>標題: </legend>

    <?php if($permit{0}!=1): ?><input type="radio" value="[2017大資盃] 資料補繳通知" name="title" checked>[2017大資盃] 資料補繳通知<br><?php endif;?>

    <?php if($permit{0}!=0): ?><input type="radio" value="[2017大資盃] 費用補繳通知" name="title" <?php echo ($permit{0}==1?"checked":"");?>>[2017大資盃] 費用補繳通知<br><?php endif;?>

    <input type="radio" value="" name="title"><input type="text" name="title_custom" value="[2017大資盃] " disabled><br>

    </fieldset>

    <fieldset>

        <legend>收件者: </legend>

        <?php

        echo $attr[$category]["chinese"]."-".$teamname."<br>";

        echo $realname."(".$email.")";

        ?>

    </fieldset>

    

    <fieldset><legend>內容: </legend>

    <textarea name="header" cols="80" rows="3"><?php echo $realname." 您好:\n";?></textarea><br>
    <!--我們收到您 2017大資盃 的報名資訊，發現有以下部分缺漏:-->

    <textarea name="content" cols="80" rows="10"></textarea><br> 

    <textarea name="footer" cols="80" rows="7">
    <?php echo "恭喜各隊進入男籃八強以及女籃前三，以下幾點事項請各位注意
明日賽程皆位於台灣體育運動大學 新教學大樓
校園內禁止吸菸
比賽球衣顏色 左淺又深 若有隊伍只有一種顏色的球衣 請盡速回復並告知有的顏色
\n\n若有任何問題，請聯絡\nemail:<a href='mailto:nuic2017nctu@gmail.com'>nuic2017nctu@gmail.com</a>\nFacebook:<a href='https://www.facebook.com/nuic2017'>https://www.facebook.com/nuic2017</a>\n\n2017大資盃工作團隊\n<img src='https://nuic2017.com/wp-content/themes/vantage/sponsor/sponsor4.jpg'>"
    ?>
    <?php /*echo "\n\n請您將缺漏之部分，盡速以'[2017大資盃] ".$attr[$category]["chinese"]."-".$teamname." 補件'為標題\n內文為欲修改之資料欄位以及欲修改之資料\n照片檔名用 姓名-欄位名稱 (如 王小名-學生證正面.jpg)壓縮起來傳附件\n寄email至: nuic2017nctu@gmail.com\n\n若有任何問題，請聯絡\nemail:<a href='mailto:nuic2017nctu@gmail.com'>nuic2017nctu@gmail.com</a>\nFacebook:<a href='https://www.facebook.com/nuic2017'>https://www.facebook.com/nuic2017</a>\n\n2017大資盃工作團隊\n<img src='https://nuic2017.com/wp-content/themes/vantage/sponsor/sponsor4.jpg'>"; */?>
    </textarea>    

    

    </fieldset><br>

    <input type="hidden" name="tid" value="<?php echo $tid;?>">

    <input type="submit" value="send email">

</form>

</body>

</html>



