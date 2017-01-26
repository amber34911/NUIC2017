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

if($permit{2}!=1){
    
    echo "permission denied";

    exit();
}

$category=$_GET["category"];



$myarr=array("","b_bas","g_bas","b_vol","g_vol","tab","bad","sof","soc");



if(!in_array($category,$myarr)){

    echo "error";

    exit();

}

$request_cat= array_search($category,$myarr);

//echo $request_cat;

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

    <title>view team</title>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-2.1.1.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.form.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/functions.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.tablesorter.min.js"></script>

    <script>

        $(document).ready(function(){ 

            $.tablesorter.addParser({ 

                // set a unique id 

                id: 'money_num', 

                is: function(s) { 

                    // return false so this parser is not auto detected 

                    return false; 

                }, 

                format: function(s) { 

                    // format your data for normalization 

                    return s.replace(/Edit/,"").replace(/Save/,"");

                }, 

                // set type, either numeric or text 

                type: 'numeric' 

            }); 

            $("#myTable").tablesorter({ 

                headers: { 

                    <?php if($permit{0}==1||$permit{0}==2){ ?>

                    8: { 

                        sorter:'money_num' 

                    } 

                    <?php }elseif($permit{0}==3){ ?>

                    11: { 

                        sorter:'money_num' 

                    } 

                    <?php }?>

                } 

            }); 

            

            

            $("button.goto").on("click",function(){

                redirect("/managefox?category="+$("#gotocategory").val());



            });

            

            

            <?php if($permit{0}==1||$permit{0}==3):?>

            $(".edit_money_num").data("state","edit").on("click",function(){

                inputbox=$(this).parent().find("input[type=text]");

                

                if($(this).data("state")=="edit"){

                    $(this).text("Save");

                    $(this).data("state","save");

                    inputbox.prop( "disabled",false);

                }else if($(this).data("state")=="save"){

                    $(this).text("Edit");

                    $(this).data("state","edit");

                    inputbox.prop( "disabled",true);



                    $.ajax({

                        type: "POST",

                        url: "<?php echo get_template_directory_uri(); ?>/managefox_act.php",

                        data: {

                            "field":"money_num",

                            "tid":$(this).parent().parent().data("tid"),

                            "value":inputbox.val()

                        },

                        success: function(data){



                            if(data.error){

                                alert(data.error);

                                inputbox.val(inputbox.siblings(".forsort").text());

                                //$("<input type='text' disabled></input>").val(data.ori_money_inf).insertAfter(inputbox);

                                //inputbox.remove();

                                //$(".server_message").text(data.error).addClass("server_error");

                            }else{

                                $(".messages").text(data.message);

                                inputbox.siblings(".forsort").text(inputbox.val());

                                $("#myTable").trigger("update"); 

                                //inputbox.attr("value",data.money_inf);

                                //$(".server_message").text(data.message).removeClass("server_error");

                            }

                        },

                        error:function(){

                            inputbox.val(inputbox.siblings(".forsort").text());

                        },

                        dataType:"json"

                    });



                    //alert("money_num\n"+inputbox.val()+"\n"+$(this).parent().parent().data("tid"));



                }

            });

            <?php endif;?>

            

            <?php if($permit{0}==2||$permit{0}==3):?>

            $(".successc").on("change",function () {

                var checkvalue=$(this).is(":checked")?1:0;

                var mycheckbox=$(this);

                if(confirm("Are you sure you want to check this?\nThis will send an email to the team leader")){

                    $.ajax({

                        type: "POST",

                        url: "<?php echo get_template_directory_uri(); ?>/managefox_act.php",

                        data: {

                            "field":"success",

                            "tid":mycheckbox.parent().parent().data("tid"),

                            "value":checkvalue

                        },

                        success: function(data){

                            if(data.error){

                                alert(data.error);

                                mycheckbox.prop("checked", mycheckbox.siblings(".forsort").text()=="1");

                            }else{

                                $(".messages").text(data.message);

                                mycheckbox.siblings(".forsort").text(checkvalue);

                                mycheckbox.attr("disabled","");

                                $("#myTable").trigger("update");

                            }

                        },

                        error:function(){

                            mycheckbox.prop("checked", mycheckbox.siblings(".forsort").text()=="1");

                        },

                        dataType:"json"

                    });

                }else{

                    mycheckbox.prop("checked", mycheckbox.siblings(".forsort").text()=="1");

                }

                

            });

            <?php endif;?>

            

            <?php if($permit{0}==0||$permit{0}==3):?>

            $(".datac").on("change",function () {

                var checkvalue=$(this).is(":checked")?1:0;

                var mycheckbox=$(this);

                $.ajax({

                    type: "POST",

                    url: "<?php echo get_template_directory_uri(); ?>/managefox_act.php",

                    data: {

                        "field":"data_checked",

                        "tid":mycheckbox.parent().parent().data("tid"),

                        "value":checkvalue

                    },

                    success: function(data){

                        if(data.error){

                            alert(data.error);

                            mycheckbox.prop("checked", mycheckbox.siblings(".forsort").text()=="1");

                            

                        }else{

                            $(".messages").text(data.message);

                            mycheckbox.siblings(".forsort").text(checkvalue);

                            $("#myTable").trigger("update");

                        }

                    },

                    error:function(){

                        mycheckbox.prop("checked", mycheckbox.siblings(".forsort").text()=="1");

                    },

                    dataType:"json"

                });

                

            });

            <?php endif;?>

            

            <?php if($permit{0}==3):?>

            $(".delete").on("click",function () {

                var thisbutton=$(this);

                var realname=$(this).parent().parent().find("td").eq(5).text();

                var category=$(this).parent().parent().find("td").eq(2).text();

                var teamname=$(this).parent().parent().find("td").eq(3).text();

                var success=$(this).parent().parent().find("td").eq(14).children(".forsort").text();

                var money_inf=$(this).parent().parent().find("td").eq(10).text();

                

                var tid=$(this).parent().parent().data("tid");

                if(confirm("Really?\nAre you sure you want to delete tid"+tid+"?")){

                    if(success==1){

                        alert("報名成功 不能刪除")

                        return;

                    }

                    if(money_inf!=""){

                        if(!confirm("已繳費 可能要退費 確認刪除?")){

                            return;

                        }

                    

                    }

                    var title=prompt("Please enter the title", "[2017大資盃] 資料刪除通知");

                    if(title!=null){

                        var reason=prompt("Please enter the reason", "未報名成功");

                        if(reason!=null){

                            var content=realname+" 您好:\n"+

                                "您所報名之隊伍 "+category+"-"+teamname+"\n"+

                                "由於"+reason+"\n"+

                                "我們將刪除此隊之報名資料\n"+

                                "謝謝您的參與\n\n"+

                                "Email: nuic2017nctu@gmail.com\n"+

                                "FB:https://www.facebook.com/nuic2017\n"+

                                "2017大資盃 謝謝您";

                            if(confirm("title:\n"+title+"\ncontent:\n"+content)){

                                $.ajax({

                                    type: "POST",

                                    url: "<?php echo get_template_directory_uri(); ?>/managefox_email_act.php",

                                    data: {

                                        "title":title,

                                        "tid":tid,

                                        "header":"",

                                        "content":content,

                                        "footer":""

                                    },

                                    success: function(data){

                                        if(data.error){

                                            alert(data.error);

                                        }else{

                                            alert(data.message);

                                            if(data.message=="信件已成功寄出"){

                                                $.ajax({

                                                    type: "POST",

                                                    url: "<?php echo get_template_directory_uri(); ?>/managefox_act.php",

                                                    data: {

                                                        "field":"delete",

                                                        "tid":tid,

                                                        "value":1

                                                    },

                                                    success: function(data){

                                                        if(data.error){

                                                            alert(data.error);

                                                        }else{

                                                            $(".messages").text(data.message);

                                                            thisbutton.parent().parent().remove();

                                                        }

                                                    },

                                                    dataType:"json"

                                                });

                                            }

                                        }

                                    },

                                    dataType:"json"

                                });

                            }

                        }

                    }

                    

                }

                

            });

            <?php endif;?>

        }); 

    </script>

    <style>

        table{

            border-collapse: collapse;

            

        }

        tr:nth-child(even){

            background-color:#eee;

        }

        tr:hover{

            background-color:#ddd;

        }

        td,th{

            border:1px solid rgba(0,0,0,0.3);

            padding:2px 20px 2px 2px;

            vertical-align:middle;

        }

        th{

            text-align:left;

            background-image: url(<?php echo get_template_directory_uri()?>/image/bg.gif);

            background-repeat: no-repeat;

            background-position: center right;

            cursor: pointer;

        }

        td input[type=checkbox]{

            transform:scale(1.6);

        }

        .headerSortUp{

            background-image: url(<?php echo get_template_directory_uri()?>/image/asc.gif);

        }

        .headerSortDown{

            background-image: url(<?php echo get_template_directory_uri()?>/image/desc.gif);

        }

        .moneyn{

            width:50px;

        }

        .forsort{

            display:none;

        }

    </style>

</head>

<body>





<select id="gotocategory">

<?php 

    for($i=0;$i<9;$i++){

        if($i==0){

            echo "<option value=''>全部</option>";

        }else{

            echo "<option value='".$myarr[$i]."' ".($myarr[$i]==$category?"selected='selected'":"").">".$attr[$myarr[$i]]["chinese"]."</option>";

        }

    }



?>

</select>

<button class="goto">filter</button>



<a href="../managefox_logout">logout</a>



<br>

<a href="../managefox_log">email_log</a><br>

<a href="https://goo.gl/QY3Nt0">資料待補</a><br>

<a href="https://goo.gl/7MUU3a">需要收據的隊伍</a>

<a href="https://goo.gl/VOQt57">繳費相關問題</a>

<br>

<?php if($permit{0}==3):?>

<br>

<a href="../managefox_viewlog">viewlog</a>

<br>

保險資料

<?php for($i=0;$i<9;$i++):?>

<a href="<?php echo get_template_directory_uri(); ?>/insurance_act.php?category=<?php echo $myarr[$i];?>"><?php echo ($i==0?"全部":$attr[$myarr[$i]]["chinese"])?></a>

<?php endfor;?>

<br>

選手資料

<?php for($i=0;$i<9;$i++):?>

<a href="<?php echo get_template_directory_uri(); ?>/data_act.php?category=<?php echo $myarr[$i];?>"><?php echo ($i==0?"全部":$attr[$myarr[$i]]["chinese"])?></a>

<?php endfor;?>

<br>

<a href="<?php echo get_template_directory_uri(); ?>/zip_act.php">選手照片</a><br>

<?php endif; ?>



<div class="messages">&nbsp;&nbsp;</div>

<table id="myTable">

   <thead>

    <tr>

        <?php if($permit{0}==3):?><th>tid</th><?php endif;?>

        <?php if($permit{0}==3):?><th>delete</th><?php endif;?>

        <th>項目</th>

        <th>隊名</th>

        <?php if($permit{0}==3):?><th>A/B</th><?php endif;?>

        <th>連絡人</th>

        <th>手機</th>

        <th>學校</th>

        <th>科系</th>

        <th>應繳金額</th>

        <?php if($permit{0}!=0):?><th>匯款資料</th><?php endif;?>

        <?php if($permit{0}!=0):?><th>匯款資料號碼</th><?php endif;?>

        <?php if($permit{0}!=0):?><th>匯款圖片時間</th><?php endif;?>

        <?php if($permit{0}!=1):?><th>資料檢驗</th><?php endif;?>

        <?php if($permit{0}==2||$permit{0}==3):?><th>報名成功</th><?php endif;?>

        <th>報名時間</th>

    </tr></thead><tbody>

<?php

/*

    if($permit{0}==0){

        $sql="select teams.*,money_inf_pic.ext_name,users.school,users.department,users.email,users.realname,users.cellphone,money_inf_pic.id as picid from teams left join money_inf_pic on teams.money_inf=money_inf_pic.hashed_name join users on teams.uid=users.uid where teams.category=? order by tid asc";

        $sth=$db->prepare($sql);

        $sth->execute(array($myarr[$permit{1}]));

    }else{

    */

        if($request_cat==0){

            $sql="select teams.*,money_inf_pic.ext_name,users.school,users.department,users.email,users.realname,users.cellphone,money_inf_pic.id as picid from teams left join money_inf_pic on teams.money_inf=money_inf_pic.hashed_name join users on teams.uid=users.uid where teams.success=1 order by tid asc";

            $sth=$db->prepare($sql);

            $sth->execute(array());

        }else{

            $sql="select teams.*,money_inf_pic.ext_name,users.school,users.department,users.email,users.realname,users.cellphone,money_inf_pic.id as picid from teams left join money_inf_pic on teams.money_inf=money_inf_pic.hashed_name join users on teams.uid=users.uid where teams.category=? and teams.success=1 order by tid asc";

            $sth=$db->prepare($sql);

            $sth->execute(array($myarr[$request_cat]));

        }

    /*

    }

    */





    



    while($result = $sth->fetchObject()):

?>

    <tr data-tid="<?php echo $result->tid;?>">

        <?php if($permit{0}==3):?><td><?php echo $result->tid;?></td><?php endif;?>

        <?php if($permit{0}==3):?><td><button class="delete">delete</button></td><?php endif;?>

        <td><?php echo $attr[$result->category]["chinese"];?></td>

        

        <td><a href="../<?php echo ($permit{0}==3?"managefox_edit":"managefox_team");?>?tid=<?php echo $result->tid;?>"><?php echo $result->teamname;?></a></td>

        

        <?php if($permit{0}==3):?><td><?php echo ($result->reg_stage=="1"?"A":"B");?></td><?php endif;?>

        

        <td><a href="../managefox_email?tid=<?php echo $result->tid;?>"><?php echo $result->realname;?></a></td>

        

        <td><?php echo $result->cellphone;?></td>

        

        <td><?php echo $result->school;?></td>

        

        <td><?php echo $result->department;?></td>

        

        <td><?php echo $result->reg_player*41+$attr[$result->category]["price"]+$guarantee+200;?></td>

        

        <?php if($permit{0}!=0):?><td><?php if($result->money_inf!=""):?><a href="<?php echo get_template_directory_uri()."/infpic/".$result->money_inf.".".$result->ext_name;?>">匯款圖片<?php echo $result->picid;?></a><?php endif;?></td><?php endif;?>

        

        <?php if($permit{0}!=0):?><td><span class="forsort"><?php echo $result->money_num;?></span><input type="text" class="moneyn" value="<?php echo $result->money_num;?>" maxlength="3" disabled><?php if($permit{0}==1||$permit{0}==3):?><button class="edit_money_num">Edit</button><?php endif; ?></td><?php endif;?>

        

        <?php if($permit{0}!=0):?><td><?php echo $result->money_inf_timestamp;?></td><?php endif;?>

        

        <?php if($permit{0}!=1):?><td><span class="forsort"><?php echo $result->data_checked;?></span><input type="checkbox" class="datac" <?php echo ($result->data_checked=="1"?"checked":"");?> <?php echo ($permit{0}==0||$permit{0}==3)?"":"disabled";?>></td><?php endif;?>

        

        <?php if($permit{0}==2||$permit{0}==3):?><td><span class="forsort"><?php echo $result->success;?></span><input type="checkbox" class="successc" <?php echo ($result->success=="1"?"checked disabled":"");?>></td><?php endif;?>

        

        <td><?php echo $result->timestamp;?></td>

    </tr>

<?php endwhile;?>   



</tbody>

</table>



<span id="cur_cat" data-category="<?php echo $request_cat;?>"></span>



</body>

</html>

<?php// get_footer();?>

