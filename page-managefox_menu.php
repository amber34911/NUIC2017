<?php

require_once( "php/settings/db_connect.php");

require_once( "php/settings/functions.php");

require_once( "php/settings/game_attribute.php");

testing();

start_session(1209600);



//get_header();





/*

first digit = 0(系隊)second digit 1~8("b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc")

07->系壘

first digit = 1(總務)

first digit = 2(success)

first digit = 3(管理員)


third digit = 1(負責人)

third digit = 0(記錄人)

 */

$adminid=$_SESSION["adminid"];

$sql="select * from auth where admin=?";

$sth=$db->prepare($sql);

$sth->execute(array($adminid));

if($result = $sth->fetchObject()){

    $permit=$result->permit;

}else{

    echo "permission denied";

    exit();

}





$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");

?>

<!DOCTYPE html>

<html lang="zh-tw">

<head>

    <meta charset="UTF-8">

    <meta http-equiv="cache-control" content="max-age=0" />

    <meta http-equiv="cache-control" content="no-cache" />

    <meta http-equiv="expires" content="-1" />

    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />

    <meta http-equiv="pragma" content="no-cache" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>menu</title>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>
    <style>
        .grid{
            font-size: 26pt;
            width: 60%;
            padding: 50px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-top: 50px;
            border-radius: 5px;
            border-color: #FFFFFF;
            background-color: #FFFFFF;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .grid:active{
            background-color: #DDDDDD;
        }
    </style>

</head>
<body>
	<?php if($permit{2}!=0): ?>
    <div class="grid" id="managefox" align="center" onclick="location.href='../managefox'">隊伍管理</div>
    <br/>
    <?php endif;?>
    <div class="grid" align="center" onclick="location.href='../managefox_scoreboard?category=<?php echo $myarr{1}; ?>'">男籃計分</div>
    <div class="grid" align="center" onclick="location.href='../managefox_scoreboard?category=<?php echo $myarr{2}; ?>'">女籃計分</div>
    <div class="grid" align="center" onclick="location.href='../managefox_scoreboard?category=<?php echo $myarr{3}; ?>'">男排計分</div>
    <div class="grid" align="center" onclick="location.href='../managefox_scoreboard?category=<?php echo $myarr{4}; ?>'">女排計分</div>
    <div class="grid" align="center" onclick="location.href='../managefox_scoreboard?category=<?php echo $myarr{5}; ?>'">桌球計分</div>
    <div class="grid" align="center" onclick="location.href='../managefox_scoreboard?category=<?php echo $myarr{6}; ?>'">羽球計分</div>
    <div class="grid" align="center" onclick="location.href='../managefox_scoreboard?category=<?php echo $myarr{7}; ?>'">壘球計分</div>
</body>
