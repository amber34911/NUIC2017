<?php
require_once("php/settings/db_connect.php");
require_once("php/settings/functions.php");
require_once("php/settings/game_attribute.php");
$data=new stdClass();
start_session(1209600);
$category=$_POST["category"];
$flag=$_POST["flag"];
$myarr=array("b_bas","g_bas","b_vol","g_vol","tab","bad","sof");
if(!in_array($category,$myarr)){
    $data->error="category not found";
    echo json_encode($data);
    exit();
}

if($category=="tab"){
    $teams=array();
    $flat_teams=array();
    $sql="select A.teamname teamnameA,B.teamname teamnameB,A.code codeA,B.code codeB from tournament T left join (select* from mapping where category=?) A on T.teamA=A.code left join (select* from mapping where category=?) B on T.teamB=B.code where T.round=1 and T.category=? and T.flag=? order by T.mynumber";
    $sth=$db->prepare($sql);
    $sth->execute(array($category,$category,$category,$flag));
    while($result=$sth->fetchObject()){
        $teampair=array();
        array_push($teampair,$result->teamnameA);
        array_push($teampair,$result->teamnameB);
        array_push($flat_teams,$result->codeA);
        array_push($flat_teams,$result->codeB);
        array_push($teams,$teampair);
    }
    $data->teams=$teams;
    $round_one=array();
    $sql="select teamA,teamB,scoreA,scoreB,round,number from tournament where round!=0 and category=? and flag=0 order by round,mynumber";
    $sth=$db->prepare($sql);
    $sth->execute(array($category));

    while($result=$sth->fetchObject()){
        if(count($round_one)<$result->round){
            $round=array();
            array_push($round_one,$round);
        }
        $scores=array();
        if(array_search($result->teamA,$flat_teams)>array_search($result->teamB,$flat_teams)){
			array_push($scores,(is_null($result->scoreB)?null:intval($result->scoreB)));
			array_push($scores,(is_null($result->scoreA)?null:intval($result->scoreA)));
        }else{
			array_push($scores,(is_null($result->scoreA)?null:intval($result->scoreA)));
			array_push($scores,(is_null($result->scoreB)?null:intval($result->scoreB)));
        }
        array_push($round_one[$result->round-1],$scores);
    }
    $flat_teams=array(6,1,4,11,5,2,3,12,15);
    $round_two=array();
    $sql="select teamA,teamB,scoreA,scoreB,round,number from tournament where round!=0 and category=? and flag=1 order by round,mynumber";
    $sth=$db->prepare($sql);
    $sth->execute(array($category));

    while($result=$sth->fetchObject()){
        if(count($round_two)<$result->round){
            $round=array();
            array_push($round_two,$round);
        }
        $scoreA=$result->scoreA;
        $scoreB=$result->scoreB;
        $teamA=$result->teamA;
        $teamB=$result->teamB;
        
        $scores=array();
        if (is_null($scoreA) && is_null($scoreB)){
            array_push($scores,null);
            array_push($scores,null);
        } else {
        
            $sql2="SELECT `number` FROM `tournament` WHERE `category`=? and `flag`=0 and ((`teamA`=? and (`scoreA` < `scoreB`)) or (`teamB`=? and (`scoreB` < `scoreA`)))";
            $sth2=$db->prepare($sql2);
            $sth2->execute(array($category,$teamA,$teamA));
            if($result2=$sth2->fetchObject()){
                $teamA_lose_number = $result2->number;
            }
            
            $sql2="SELECT `number` FROM `tournament` WHERE `category`=? and `flag`=0 and ((`teamA`=? and (`scoreA` < `scoreB`)) or (`teamB`=? and (`scoreB` < `scoreA`)))";
            $sth2=$db->prepare($sql2);
            $sth2->execute(array($category, $teamB, $teamB));
            if($result2=$sth2->fetchObject()){
                $teamB_lose_number = $result2->number;
            }
            
            if(array_search($teamA_lose_number,$flat_teams)>array_search($teamB_lose_number,$flat_teams)){
                array_push($scores,(is_null($scoreB)?null:intval($scoreB)));
                array_push($scores,(is_null($scoreA)?null:intval($scoreA)));
            }else{
                array_push($scores,(is_null($scoreA)?null:intval($scoreA)));
                array_push($scores,(is_null($scoreB)?null:intval($scoreB)));
            }
        }
        array_push($round_two[$result->round-1],$scores);
    }
    $round_three=array();
    $sql="select teamA,teamB,scoreA,scoreB,round,number from tournament where round!=0 and category=? and flag=2 order by round,mynumber";
    $sth=$db->prepare($sql);
    $sth->execute(array($category));

    while($result=$sth->fetchObject()){
		$scoreA=$result->scoreA;
        $scoreB=$result->scoreB;
        $teamA=$result->teamA;
        $teamB=$result->teamB;
        if(count($round_three)<$result->round){
            $round=array();
            array_push($round_three,$round);
        }
        $scores=array();
        if (is_null($scoreA) && is_null($scoreB)){
            array_push($scores,null);
            array_push($scores,null);
        } else {
        
            $sql2="SELECT `number` FROM `tournament` WHERE `category`=? and `flag`=0 and ((`teamA`=? and (`scoreA` < `scoreB`)) or (`teamB`=? and (`scoreB` < `scoreA`)))";
            $sth2=$db->prepare($sql2);
            $sth2->execute(array($category,$teamA,$teamA));
            $result2=$sth2->fetchObject();
			if(is_null($result2->number)){
				array_push($scores,(is_null($scoreA)?null:intval($scoreA)));
				array_push($scores,(is_null($scoreB)?null:intval($scoreB)));
			}else{
				array_push($scores,(is_null($scoreB)?null:intval($scoreB)));
				array_push($scores,(is_null($scoreA)?null:intval($scoreA)));
			}
                
            

        }
        array_push($round_three[$result->round-1],$scores);
    }
    $rounds=array();
    array_push($rounds,$round_one);
    array_push($rounds,$round_two);
    array_push($rounds,$round_three);
}else{
    $teams=array();
    $flat_teams=array();
    $sql="select A.teamname teamnameA,B.teamname teamnameB,A.code codeA,B.code codeB from tournament T left join (select* from mapping where category=?) A on T.teamA=A.code left join (select* from mapping where category=?) B on T.teamB=B.code where T.round=1 and T.category=? and T.flag=? order by T.mynumber";
    $sth=$db->prepare($sql);
    $sth->execute(array($category,$category,$category,$flag));
    while($result=$sth->fetchObject()){
        $teampair=array();
        array_push($teampair,$result->teamnameA);
        array_push($teampair,$result->teamnameB);
        array_push($flat_teams,$result->codeA);
        array_push($flat_teams,$result->codeB);
        array_push($teams,$teampair);
    }
    $data->teams=$teams;
    $data->flat_teams=$flat_teams;
    $rounds=array();
    $sql="select teamA,teamB,scoreA,scoreB,round,number from tournament where round!=0 and category=? and flag=? order by round,mynumber";
    $sth=$db->prepare($sql);
    $sth->execute(array($category,$flag));

    while($result=$sth->fetchObject()){
        if(count($rounds)<$result->round){
            $round=array();
            array_push($rounds,$round);
        }
        $scores=array();
//echo array_search($result->teamA,$flat_teams).",";
//echo array_search($result->teamB,$flat_teams)."\n";
        if(array_search($result->teamA,$flat_teams)>array_search($result->teamB,$flat_teams)){
			array_push($scores,(is_null($result->scoreB)?null:intval($result->scoreB)));
			array_push($scores,(is_null($result->scoreA)?null:intval($result->scoreA)));
        }else{
			array_push($scores,(is_null($result->scoreA)?null:intval($result->scoreA)));
			array_push($scores,(is_null($result->scoreB)?null:intval($result->scoreB)));
        }
        array_push($rounds[$result->round-1],$scores);
    }
}
$data->scores=$rounds;
$data->flag=$flag;
echo json_encode($data);
?>
