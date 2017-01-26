<?php
/**
 * jQuery Bracket server - release 1
 *
 * Copyright (c) 2012, Teijo Laine,
 * http://aropupu.fi/bracket-server/
 *
 * Licenced under the MIT licence
 */
    require_once("php/settings/functions.php");
    //get_header();
    $category=$_GET["category"];
    $myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");
    if(!in_array($category,$myarr)){
        echo "error";
        exit();
    }
?>
    <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery.json-2.2.min.js"></script>
    <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery.bracket.min.js"></script>
    <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/vector.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/jquery.bracket.min.css" />
    <style type="text/css">
    .empty {
      background-color: #FCC;
    }
    .invalid {
      background-color: #FC6;
    }
    </style>
    <script type="text/javascript">
    var canvas_w=300,
        canvas_h=300;
    var triangle_l=180;
    var rectangle_l=160;
    var triangle_c=vector.create(canvas_w/2,canvas_h/2+25);
    var rectangle_c=vector.create(canvas_w/2,canvas_h/2);
    var x=[];
    var numeric = new RegExp("[0-9]+");
    var alphabatic = new RegExp("[a-z]+","i");
    var mapping = {
        "12": [1,2],
        "13": [6,5],
        "14": [9,10],
        "23": [3,4],
        "24": [8,7],
        "34": [11,12],
        "AB": [1,2],
        "AC": [6,5],
        "AD": [9,10],
        "BC": [3,4],
        "BD": [8,7],
        "CD": [11,12]
    }
    var teams={
        "b_bas":[["1A","1B","1C"],["2A","2B","2C"],["3A","3B","3C"],["4A","4B","4C"],["5A","5B","5C"],["6A","6B","6C"],["7A","7B","7C"],["8A","8B","8C"],["9A","9B","9C"],["10A","10B","10C"],["11A","11B","11C"],["12A","12B","12C"],["13A","13B","13C"],["14A","14B","14C"],["15A","15B","15C"],["16A","16B","16C"],["17A","17B","17C"],["18A","18B","18C"],["19A","19B","19C"],["20A","20B","20C"],["21A","21B","21C"],["22A","22B","22C"],["23A","23B","23C"],["24A","24B","24C"],["25A","25B","25C"],["26A","26B","26C"],["27A","27B","27C"],["28A","28B","28C"],["29A","29B","29C"],["30A","30B","30C"],["31A","31B","31C"],["32A","32B","32C"]],
        "g_bas":[["1A","1B","1C"],["2A","2B","2C"],["3A","3B","3C"],["4A","4B","4C"],["5A","5B","5C"],["6A","6B","6C","6D"]],
        "b_vol":[["A1","A2","A3"],["B1","B2","B3"],["C1","C2","C3"],["D1","D2","D3"],["E1","E2","E3"],["F1","F2","F3"],["G1","G2","G3"],["H1","H2","H3"],["I1","I2","I3"],["J1","J2","J3"],["K1","K2","K3"],["L1","L2","L3"],["M1","M2","M3"],["N1","N2","N3"],["O1","O2","O3"],["P1","P2","P3"],["Q1","Q2","Q3"],["R1","R2","R3"],["S1","S2","S3"],["T1","T2","T3"]],
        "g_vol":[["A1","A2","A3"],["B1","B2","B3"],["C1","C2","C3"],["D1","D2","D3"],["E1","E2","E3"],["F1","F2","F3"],["G1","G2","G3"],["H1","H2","H3"],["I1","I2","I3"],["J1","J2","J3"],["K1","K2","K3"],["L1","L2","L3"]],
        "tab":[["A1","A2","A3","A4"],["B1","B2","B3","B4"],["C1","C2","C3","C4"],["D1","D2","D3","D4"],["E1","E2","E3"]],
        "bad":[["A1","A2","A3"],["B1","B2","B3"],["C1","C2","C3"],["D1","D2","D3"],["E1","E2","E3"],["F1","F2","F3"],["G1","G2","G3"],["H1","H2","H3"],["I1","I2","I3"],["J1","J2","J3"]],
        "sof":[["A1","A2","A3"],["B1","B2","B3"],["C1","C2","C3"],["D1","D2","D3","D4"],["E1","E2","E3"],["F1","F2","F3"],["G1","G2","G3"]]
    };
    var teamnames={
        "b_bas":[["中華資工","勤益資管","長庚資工"],["輔大資工","清華資工","雲科資工"],["中科大資管B","北實踐資訊","世新資傳"],["台科資工A","真理運資傳","中正資工"],["中山資工","元培資管","國北教數資"],["市北資科","嘉藥應資","政大資科B"],["佛光資應","嘉大資工","聯大資工"],["中山醫資系籃","高海資管","成大工資管"],["聯大資管","中正資工所","東吳資管A"],["東華資工","銘傳資管B","亞大商應"],["台大資工B","靜宜資工","淡江資管A"],["靜宜資傳 A","銘傳資傳A","暨南資工"],["淡江資工","明新資管","臺體運傳"],["龍華資網","義守資管","致理資管"],["銘傳電通B","東吳資管B","中山資管"],["樹德資管","嘉藥多媒體","中正資管"],["嘉大資管","輔大資管","大葉資管"],["元智資工B","修平資管","高應資管"],["真理資工","中央資工","海大資工"],["成大資工","交大資工A","中原資工"],["虎尾資工","逢甲資電","元智資工A"],["政大資管","政大資科A","德明資管"],["中科大資管A","亞大資傳","台大資工A"],["國北教資科","東海資工","崑科大資管"],["靜宜資管A","勤益資工","朝陽資工"],["台科資工B","元智資管","銘傳資傳B"],["淡江資傳","台大資管","屏大資科"],["嶺東資科系","中臺資管","高醫醫資"],["國北護資管","銘傳資管A","師大資工"],["逢甲資工A","銘傳電通A","輔大軟創"],["南台資管","中原資管","德明資科"],["暨大資管","中華資管","僑光資科"]],
        "g_bas":[["東海資工","逢甲資工","銘傳資管"],["文化資管","臺大資工","台大圖資"],["交大資工A","聯大資管","中央資管"],["勤益資管","高醫醫管資","中原資管"],["朝陽資管","臺體運傳","國北教數資"],["淡大資管","靜宜資傳","開南資傳","成大資工"]],
        "b_vol":[["文化資管","淡江大學","成大資工A"],["興大資工","輔大資工","嘉大資管"],["中央資管A","聯合資管男排","文化資工"],["東海資管","東華資工","交大資工A"],["朝陽資工","中山資管","銘傳資工"],["輔仁資管","交大資工B","僑光資科"],["元智資管","致理資管","台科資管"],["銘傳資傳","聯合資工A","中科大資訊管理系"],["大葉資管","靜宜資傳","台大資工B"],["中華資工","高海資管","中央資工A"],["臺大資管","靜宜資工","台大資工A"],["德明資科A","開南資管","國北教數資"],["政大資管","長庚資管","中原資管"],["東吳資管","東海資工A","中山資工"],["北大通訊","世新資管","朝陽資管A"],["朝陽資通","嘉大資工","真理資工"],["台科資工A","中山醫醫資","中原資工"],["南臺資管","德明資管","成大工資管"],["銘傳資管","清大資工B","逢甲資訊"],["清大資工A","朝陽資管B","長庚資工"]],
        "g_vol":[["銘傳資工","成大工資管","文化資管"],["東吳資管","淡江資工","靜宜資傳"],["南臺資管","銘傳資傳","銘傳資管"],["東海資管","淡江資管","台大資管"],["交大資財","交大資工","中山資管"],["明新資管","臺體運傳","中央資工"],["元智資管","成大資工","臺大資工"],["政大資管","開南資管","中科資管"],["中原資管","中華資工","中央資管"],["南臺工資","東海資工","中山資工"],["清大資工","朝陽資管","台大圖資"],["長庚資管","高海資管","中原資工"]],
        "tab":[["嘉大資管A","臺大資工A","清大資工","高大資管資工聯隊"],["東海資工","銘傳電通資管連隊","成大工資管","中央資工A"],["嘉大資管B","中原資工A","臺大資工B","政大資管"],["中央資管A","中興資工","中原資工B","交大資工A"],["中央資工B","暨大資工","北大資工"]],
        "bad":[["嘉大資工","交大資工B","中正資工"],["國北教大","清大資工A","師大資工"],["成大工資管","中山資管A","雲科資工資管"],["成大資工A","逢甲資工","淡江資工"],["朝陽資訊","台大資管A","亞大資傳A"],["海洋資工","台大圖資","中山資管B"],["暨大資工A","銘傳資管","東華資工A"],["成大資工B","政大資管","台大資工"],["宜大資工","中原資工電資","靜宜資傳A"],["中華資工","交大資工A","輔大統資資工聯隊"]],
        "sof":[["中央資管","台大資工","輔大資工"],["台體運傳","元智資工","勤益資管"],["逢甲通訊","輔大資管","暨大資工"],["銘傳電通","銘傳資管","成大資工","朝陽資通隊"],["台大資管","中原資工","元智資管"],["中央資工","東海資工","清大資工"],["淡江資工","逢甲資工","交大資工"]]
    };
    function isNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    function swap(a,b) {
        return [b, a];
    }
    function getPosition(target_team1, target_team2){
        var key = target_team1.concat(target_team2);
        return mapping[key];
    }

    function parseMatchDetail(teamA, teamB){
        var dummy, dummy_gid, teamA_id, teamB_id, gid;
        if (isNumeric(teamA[0])) { // if matches are grouped by numbers
            [dummy, gid, teamA_id] = teamA.match(/([0-9]+)([A-Za-z]+)/); // "18A" -> ["18A", "18", "A"]
            [dummy, dummy_gid, teamB_id] = teamB.match(/([0-9]+)([A-Za-z]+)/); 
        } else {
            [dummy, gid, teamA_id] = teamA.match(/([A-Za-z]+)([0-9]+)/); // "A18" -> ["A18", "A", "18"]
            [dummy, dummy_gid, teamB_id] = teamB.match(/([A-Za-z]+)([0-9]+)/); 
        }
        return [gid, teamA_id, teamB_id];
    }
    function triangle(team,teamname,flag,container_id){
        var mycanvas=document.createElement("canvas");
        var container=document.getElementById(container_id);
        container.appendChild(mycanvas);
        
        var context = mycanvas.getContext("2d"),
            width = mycanvas.width = canvas_w,
            height = mycanvas.height = canvas_h;
        
        
        var teamname_angle1=-Math.PI/2,
            teamname_angle2=-Math.PI/2-2*Math.PI/3,
            teamname_angle3=-Math.PI/2-4*Math.PI/3;
        var p1=triangle_c.add(vector.create(0,0).setLength(Math.sqrt(3)/3*triangle_l).setAngle(teamname_angle1));
        var p2=triangle_c.add(vector.create(0,0).setLength(Math.sqrt(3)/3*triangle_l).setAngle(teamname_angle2));
        var p3=triangle_c.add(vector.create(0,0).setLength(Math.sqrt(3)/3*triangle_l).setAngle(teamname_angle3));
        context.beginPath();
        context.fillStyle="#000000";
		context.moveTo(p1.getX(),p1.getY());
		context.lineTo(p2.getX(),p2.getY());
        context.lineTo(p3.getX(),p3.getY());
        context.lineTo(p1.getX(),p1.getY());
        context.stroke();
        context.textAlign="center";
        context.font="20px Arial";
        var text="asdqqq";
        context.fillText(team[0],p1.getX(),p1.getY()-30);
        context.fillText(teamname[0],p1.getX(),p1.getY()-10);
        context.fillText(team[1],p2.getX(),p2.getY()+20);
        context.fillText(teamname[1],p2.getX(),p2.getY()+40);
        context.fillText(team[2],p3.getX(),p3.getY()+20);
        context.fillText(teamname[2],p3.getX(),p3.getY()+40);
        if(flag=="numeric"){
            var res = numeric.exec(team[0]);    
        }else if(flag=="alphabatic"){
            var res = alphabatic.exec(team[0]);
        }else if(flag=="empty"){
            var res="";
        }
       
        
        context.fillText(res,triangle_c.getX(),triangle_c.getY()); 
        return mycanvas;
    }
    function rectangle(team,teamname,flag,container_id){
        var mycanvas=document.createElement("canvas");
        var container=document.getElementById(container_id);
        container.appendChild(mycanvas);
        
        var context = mycanvas.getContext("2d"),
            width = mycanvas.width = canvas_w,
            height = mycanvas.height = canvas_h;
        
        

        var p1=rectangle_c.add(vector.create(-rectangle_l/2,-rectangle_l/2)),
            p2=rectangle_c.add(vector.create(-rectangle_l/2,rectangle_l/2)),
            p3=rectangle_c.add(vector.create(rectangle_l/2,rectangle_l/2)),
            p4=rectangle_c.add(vector.create(rectangle_l/2,-rectangle_l/2));
        context.beginPath();
        context.fillStyle="#000000";
		context.moveTo(p1.getX(),p1.getY());
		context.lineTo(p2.getX(),p2.getY());
        context.lineTo(p3.getX(),p3.getY());
        context.lineTo(p4.getX(),p4.getY());
        context.lineTo(p1.getX(),p1.getY());
        context.stroke();
        context.textAlign="center";
        context.font="20px Arial";
        var text="asdqqq";
        context.fillText(team[0],p1.getX()-10,p1.getY()-30);
        context.fillText(teamname[0],p1.getX()-10,p1.getY()-10);
        context.fillText(team[1],p4.getX()+10,p4.getY()-30);
        context.fillText(teamname[1],p4.getX()+10,p4.getY()-10);
        context.fillText(team[2],p2.getX()-10,p2.getY()+20);
        context.fillText(teamname[2],p2.getX()-10,p2.getY()+40);
        context.fillText(team[3],p3.getX()+10,p3.getY()+20);
        context.fillText(teamname[3],p3.getX()+10,p3.getY()+40);
        
        
        
        context.beginPath();
        context.moveTo(p1.getX(),p1.getY());
		context.lineTo(p3.getX(),p3.getY());
        context.moveTo(p2.getX(),p2.getY());
		context.lineTo(p4.getX(),p4.getY());
        context.stroke();
        context.clearRect(rectangle_c.getX()-15,rectangle_c.getY()-15,30,30);
        if(flag=="numeric"){
            var res = numeric.exec(team[0]);    
        }else{
            var res = alphabatic.exec(team[0]);
        }
        context.fillText(res,rectangle_c.getX(),rectangle_c.getY()+10); 
        return mycanvas;
    }
    function trianglescore(mycanvas,score_obj){
        var context = mycanvas.getContext("2d"),
            width = canvas_w,
            height = canvas_h;
        
        var score_angle1=-8*Math.PI/12-0*Math.PI/3,
            score_angle2=-8*Math.PI/12-1*Math.PI/3,
            score_angle3=-8*Math.PI/12-2*Math.PI/3,
            score_angle4=-8*Math.PI/12-3*Math.PI/3,
            score_angle5=-8*Math.PI/12-4*Math.PI/3,
            score_angle6=-8*Math.PI/12-5*Math.PI/3;
        var position=[];
        position.push(triangle_c.add(vector.create(0,0).setLength(Math.sqrt(3)/3*triangle_l*4/5).setAngle(score_angle1)));
        position.push(triangle_c.add(vector.create(0,0).setLength(Math.sqrt(3)/3*triangle_l*4/5).setAngle(score_angle2)));
        position.push(triangle_c.add(vector.create(0,0).setLength(Math.sqrt(3)/3*triangle_l*4/5).setAngle(score_angle3)));
        position.push(triangle_c.add(vector.create(0,0).setLength(Math.sqrt(3)/3*triangle_l*4/5).setAngle(score_angle4)));
        position.push(triangle_c.add(vector.create(0,0).setLength(Math.sqrt(3)/3*triangle_l*4/5).setAngle(score_angle5)));
        position.push(triangle_c.add(vector.create(0,0).setLength(Math.sqrt(3)/3*triangle_l*4/5).setAngle(score_angle6)));
        
        context.fillStyle="#FF0000";
        context.fillText(score_obj.score.toString(),position[score_obj.position-1].getX(),position[score_obj.position-1].getY());
        
    }
    function rectanglescore(mycanvas,score_obj){
        var context = mycanvas.getContext("2d"),
            width = canvas_w,
            height = canvas_h;
        
        var position=[];
        position.push(rectangle_c.add(vector.create(-30,-rectangle_l/2-30)));
        position.push(rectangle_c.add(vector.create(30,-rectangle_l/2-30)));
        position.push(rectangle_c.add(vector.create(30,-35)));
        position.push(rectangle_c.add(vector.create(-35,25)));
        position.push(rectangle_c.add(vector.create(-rectangle_l/2-20,rectangle_l/2-5)));
        position.push(rectangle_c.add(vector.create(-rectangle_l/2-20,-rectangle_l/2+20)));
        
        position.push(rectangle_c.add(vector.create(rectangle_l/2+20,rectangle_l/2-5)));
        position.push(rectangle_c.add(vector.create(rectangle_l/2+20,-rectangle_l/2+20)));
        position.push(rectangle_c.add(vector.create(-30,-35)));
        position.push(rectangle_c.add(vector.create(35,25)));
        position.push(rectangle_c.add(vector.create(-30,rectangle_l/2+20)));
        position.push(rectangle_c.add(vector.create(30,rectangle_l/2+20)));
            
        context.fillStyle="#FF0000";
        context.fillText(score_obj.score.toString(),position[score_obj.position-1].getX(),position[score_obj.position-1].getY());
        
    }
    function getPreliminaryResult(round,category){
        $.ajax({
          type: "POST",
          url: "<?php echo get_template_directory_uri(); ?>/get_preliminary_result.php",
          data: { "round": round,
                  "category": category },
          dataType:"json",
          success: function(data){
            // Parse data and draw scores!
              
            for(var rid in data.results){
                
                var result = data.results[rid];
                var gid, teamA_id, teamB_id, target_score1, target_score2;
                var search_key, position1, position2;
                [gid, teamA_id, teamB_id] = parseMatchDetail(result.teamA, result.teamB);
                
                if (teamA_id < teamB_id) { // ex. AB, 12
                    [target_score1, target_score2] = [result.scoreA, result.scoreB];
                    [position1, position2] = getPosition(teamA_id, teamB_id);        
                } else {
                    [target_score1, target_score2] = [result.scoreB, result.scoreA];
                    [position1, position2] = getPosition(teamB_id, teamA_id);
                }
                // !!! draw triangle !!!
                // gid = canvas index -> x[gid]
                if(isNaN(gid)){
                    gid=gid.charCodeAt(0)-64;
                }
                gid=gid-1;
                
                // two positions that should be drawn on an edge
                var score_param = [{"position": position1, "score": target_score1},
                                      {"position": position2, "score": target_score2}];
                
                if(teams["<?php echo $category;?>"][gid].length==3){
                    
                    trianglescore(x[gid],score_param[0]);
                    trianglescore(x[gid],score_param[1]);
                }else if(teams["<?php echo $category;?>"][gid].length==4){
                    
                    rectanglescore(x[gid],score_param[0]);
                    rectanglescore(x[gid],score_param[1]);
                }
            }
          }
        });
    }                                              
    function setBracket(category){
        var num = category==="g_bas"?2:0;
        for (i=0; i<=num; i++){
            $.ajax({
              type: "POST",
              url: "<?php echo get_template_directory_uri(); ?>/get_tournament_act.php",
              data: {"category": category,
                     "flag": category==="g_bas"?i+1:0
                     },
              dataType:"json",
              success: function(data){
                var tournamentData = {
                    "teams": data.teams,
                    "results": data.scores
                };
                $('#bracket'+data.flag).bracket({
                    skipConsolationRound: !(category=="b_bas"||category=="b_vol"||category=="g_vol"||category=="bad"||category=="sof"),
                    init: tournamentData
                });
              }
            });
        };
    }
    $(document).ready(function() {
        var category="<?php echo $category;?>";
        var flag;
        console.log(isNaN(teams[category][0][0][0]));
        if(isNaN(teams[category][0][0][0])){
            flag="alphabatic";
        }else{
            flag="numeric";
            
        }
        for(i=0;i<teams["<?php echo $category;?>"].length;i++){
            if(teams["<?php echo $category;?>"][i].length==3){
                x.push(triangle(teams["<?php echo $category;?>"][i],teamnames["<?php echo $category;?>"][i],flag,"prelim_container"));
            }else{
                x.push(rectangle(teams["<?php echo $category;?>"][i],teamnames["<?php echo $category;?>"][i],flag,"prelim_container"));
            }
        }
        getPreliminaryResult(0,category);
        setBracket(category);
        
        if(category=="g_bas"){
            teams["g_bas"][6]=["7A","7B","7C"];
            x.push(triangle(["7A","7B","7C"], ["中央資管","台大圖資","成大資工"], "empty", "third_container"));
            getPreliminaryResult(3,category);
        }
    });
    </script>
    <div id="main">
        <div id="prelim_container">
            
        </div>
        <div>
            <div id="bracket0"></div>
            <div id="bracket1"></div>
            <div id="bracket2"></div>
            <div id="bracket3"></div>
        <div>
        <div id="second_container"></div>
        <div id="third_container"></div>
        </div>
<?php
    //get_footer();
?>
