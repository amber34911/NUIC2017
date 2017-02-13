<?php

date_default_timezone_set('Asia/Taipei');

$now=new DateTime();

$change_due=new DateTime("2016-12-09 23:59:59");

/*
//方便測試用的XD
$stage=4;
switch($stage){

case 1:

    $first_start=new DateTime("2020-06-08 00:00:00");

    $first_end=new DateTime("2020-06-08 23:59:59");

    $second_start=new DateTime("2020-06-09 00:00:00");

    $second_end=new DateTime("2020-06-09 23:59:59");

    break;

case 2:

    $first_start=new DateTime("2010-11-01 11:05:00");

    $first_end=new DateTime("2020-06-08 23:59:59");

    $second_start=new DateTime("2020-06-09 00:00:00");

    $second_end=new DateTime("2020-06-09 23:59:59");

    break;

case 3:

    $first_start=new DateTime("2010-01-08 00:00:00");

    $first_end=new DateTime("2010-01-08 23:59:59");

    $second_start=new DateTime("2020-06-09 00:00:00");

    $second_end=new DateTime("2020-06-09 23:59:59");

    break;

case 4:

    $first_start=new DateTime("2010-01-08 00:00:00");

    $first_end=new DateTime("2010-01-08 23:59:59");

    $second_start=new DateTime("2010-01-09 00:00:00");

    $second_end=new DateTime("2020-06-09 23:59:59");

    break;

case 5:

    $first_start=new DateTime("2010-01-08 00:00:00");

    $first_end=new DateTime("2010-01-08 23:59:59");

    $second_start=new DateTime("2010-01-09 00:00:00");

    $second_end=new DateTime("2010-01-09 23:59:59");

    break;





}

*/

//testing
/*


$first_start=new DateTime("2016-11-07 00:00:00");

$first_end=new DateTime("2016-11-09 23:59:59");

$second_start=new DateTime("2016-11-10 00:00:00");

$second_end=new DateTime("2016-11-11 23:59:59");
*/




//real






$first_start=new DateTime("2016-11-26 00:00:00");

$first_end=new DateTime("2016-12-02 23:59:59");

$second_start=new DateTime("2016-12-05 00:00:00");

$second_end=new DateTime("2016-12-09 23:59:59");




$guarantee=500;//保證金

$insurance=41;//每人保險費

$attr=array(

    "b_bas"=>array(

        "chinese"=>"男籃",

        "max_player"=>20,//隊員數量上限

        "min_player"=>5,//隊員數量下限

        "max_team"=>96,//隊數上限

        "price"=>"3800",//報名費

        "max_super"=>2

    ),

    "g_bas"=>array(

        "chinese"=>"女籃",

        "max_player"=>20,

        "min_player"=>5,

        "max_team"=>24,

        "price"=>"3800",

        "max_super"=>2

    ),

    "b_vol"=>array(

        "chinese"=>"男排",

        "max_player"=>20,

        "min_player"=>6,

        "max_team"=>60,

        "price"=>"3800",

        "max_super"=>2

    ),

    "g_vol"=>array(

        "chinese"=>"女排",

        "max_player"=>20,

        "min_player"=>6,

        "max_team"=>36,

        "price"=>"3800",

        "max_super"=>2

    ),

    "tab"=>array(

        "chinese"=>"桌球",

        "max_player"=>15,

        "min_player"=>4,

        "max_team"=>28,//

        "price"=>"2800",

        "max_super"=>0

    ),

    "bad"=>array(

        "chinese"=>"羽球",

        "max_player"=>15,

        "min_player"=>7,

        "max_team"=>30,//28

        "price"=>"3200",

        "max_super"=>0

    ),

    "sof"=>array(

        "chinese"=>"壘球",

        "max_player"=>20,

        "min_player"=>10,

        "max_team"=>28,//24

        "price"=>"3200",

        "max_super"=>1

    ),

    "soc"=>array(

        "chinese"=>"足球",

        "max_player"=>16,

        "min_player"=>8,

        "max_team"=>0,

        "price"=>"3000",

        "max_super"=>1

    )

);



if(isset($_GET["json"])){

	header('Content-Type:application/json;charset=utf-8');

	echo json_encode($attr);

}

function get_game_attibute(){

    global $attr;

    echo "var attr=";

    echo json_encode($attr);

    echo";";

}

function beforefirst(){

    global $now;

    global $first_start;

    global $first_end;

    global $second_start;

    global $second_end;

    return $now<$first_start;

}

function infirst(){

    global $now;

    global $first_start;

    global $first_end;

    global $second_start;

    global $second_end;

    return $now>$first_start&&$now<$first_end;

}

function afterfirst(){

    global $now;

    global $first_start;

    global $first_end;

    global $second_start;

    global $second_end;

    return $now>$first_end;

}



function beforesecond(){

    global $now;

    global $first_start;

    global $first_end;

    global $second_start;

    global $second_end;

    return $now<$second_start;

}

function insecond(){

    global $now;

    global $first_start;

    global $first_end;

    global $second_start;

    global $second_end;

    return $now>$second_start&&$now<$second_end;

}

function aftersecond(){

    global $now;

    global $first_start;

    global $first_end;

    global $second_start;

    global $second_end;

    return $now>$second_end;

}



//echo $attr["b_vol"]["first_start"];

function showstage(){

    global $now;

    global $first_start;

    global $first_end;

    global $second_start;

    global $second_end;

    if(beforefirst()){

        echo "第一階段前<br>";

    }elseif(infirst()){

        echo "第一階段中<br>";

    }elseif(afterfirst()&&beforesecond()){

        echo "兩階段間<br>";

    }elseif(insecond()){

        echo "第二階段中<br>";

    }elseif(aftersecond()){

        echo "第二階段後<br>";

    }

}

function beforechangedue(){

    global $now;

    global $change_due;

    return $now<$change_due;

}

?>

