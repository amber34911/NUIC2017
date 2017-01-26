(function(){
	var pidAlpha = {
		char: ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'], 
		name: ['臺北市','臺中市','基隆市','臺南市','高雄市','新北市','宜蘭縣','桃園縣','嘉義市','新竹縣','苗栗縣','臺中縣','南投縣','彰化縣','新竹市','雲林縣','嘉義縣','臺南縣','高雄縣','屏東縣','花蓮縣','臺東縣','金門縣','澎湖縣','陽明山管理局','連江縣'],
		value: ['10','11','12','13','14','15','16','17','34','18','19','20','21','22','35','23','24','25','26','27','28','29','32','30','31','33']
	}

	var weight = [1,9,8,7,6,5,4,3,2,1,1];

	function checkPid(pid){    
		var patt = /^([A-Z]{1})([0-9]{9})$/;    
		if(!patt.test(pid)){
			return {err: true, message: '格式不正確'};
		}
		
		var index = pidAlpha.char.indexOf(pid.charAt(0));
		if(index==-1){
			return {err: true, message: '無法對應首碼英文字'};
		}
		
		var resultSex = '';
		
		switch(pid.charAt(1)){
			case '1': resultSex = '男性'; break;
			case '2': resultSex = '女性'; break;
			default:
				return {err: true, message: '第二碼性別有誤'};
		}
		
		var npid = pidAlpha.value[index] + pid.substring(1,10);    
		var sum = 0;
		for(var i=0;i<11;i++){        
			sum += weight[i] * parseInt(npid.charAt(i));
		}
		
		if(sum % 10 ==0){
			return {err: null, message: pidAlpha.name[index]+resultSex+'身份證號檢查總和為'+sum};
		}else{
			return {err: true, message: '身份證號檢查總和為'+sum};
		}
	}
	
	function randNum(min, max){
		return(Math.floor((Math.random() * max) + min)); 
	}
	
	function generatePid(){
		var pid = '';
		pid += pidAlpha.char[randNum(0, pidAlpha.char.length-1)];
		pid += randNum(1, 2);
		pid += randNum(0, 2);
		for(var i=3;i<9;i++){
			pid += randNum(0, 9);
		}
		var npid = pidAlpha.value[pidAlpha.char.indexOf(pid.charAt(0))] + pid.substring(1,9);
		var sum = 0;
		for(var i=0;i<10;i++){
			sum += weight[i] * parseInt(npid.charAt(i));
		}
		
		var lastNum = 10 - (sum % 10);
		lastNum = (lastNum==10) ? 0 : lastNum;
		
		pid += lastNum;
		
		return pid;
		
	}
undefined
generatePid
function generatePid(){
		var pid = '';
		pid += pidAlpha.char[randNum(0, pidAlpha.char.length-1)];
		pid += randNum(1, 2);
		pid += randNum(0, 2);
		for(var i=3;i<9;i++){
			pid += randNum(0, 9);
		}
		var npid = pidAlpha.value[pidAlpha.char.indexOf(pid.charAt(0))] + pid.substring(1,9);
		var sum = 0;
		for(var i=0;i<10;i++){
			sum += weight[i] * parseInt(npid.charAt(i));
		}
		
		var lastNum = 10 - (sum % 10);
		lastNum = (lastNum==10) ? 0 : lastNum;
		
		pid += lastNum;
		
		return pid;
		
	}
	var countrylist=["阿布哈茲","阿富汗","阿爾巴尼亞","阿爾及利亞","安道爾","安哥拉","安地卡及巴布達","阿根廷","亞美尼亞","澳洲","奧地利","亞塞拜然","巴哈馬","巴林","孟加拉","巴貝多","白俄羅斯","比利時","貝里斯","貝南","不丹","玻利維亞","波赫","波札那","巴西","汶萊","保加利亞","布吉納法索","蒲隆地","柬埔寨","喀麥隆","加拿大","維德角","中非共和國","查德","智利","中華人民共和國中國","哥倫比亞","葛摩","剛果共和國","剛果民主共和國","紐西蘭","哥斯大黎加","象牙海岸","克羅埃西亞","古巴","賽普勒斯","捷克","丹麥","吉布地","多米尼克","多明尼加","厄瓜多","埃及","薩爾瓦多","赤道幾內亞","厄利垂亞","愛沙尼亞","衣索比亞"];
	var chineselist="本比賽舉辦的目的在於藉由北部地區資訊相關校系的同學之間球技交流倡導運動的重要性並在忙碌於課業之餘可以重視身體健康希望藉由此次盃賽提供一個安全且完善的環境讓大家可以互相切磋並拓展人際關係";
    var engnum = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    var Beng="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var numlist="0123456789";
    function randoma0(n){
		var tmp = "";
		for( var i=0; i < n; i++ ){
			tmp += engnum.charAt(Math.floor(Math.random() * engnum.length));
		}
		return tmp;
	}
	
	function randomdate() {
		var start=new Date(1980, 0, 1);
		var end=new Date(1999,11,30);
		var nd= new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()));
		return String(nd.getFullYear())+"-"+String(nd.getMonth()+101).substr(1,2)+"-"+String(nd.getDate()+100).substr(1,2);
	}
	
	function randomA(n){
		var tmp = "";
		for( var i=0; i < n; i++ ){
			tmp += Beng.charAt(Math.floor(Math.random() * Beng.length));
		}
		return tmp;
	}
	
	function random0(n){

		var tmp = "";
		for( var i=0; i < n; i++ ){
			tmp += numlist.charAt(Math.floor(Math.random() * numlist.length));
		}
		return tmp;
	}
	
	function randomch(n){
		var tmp = "";
		for( var i=0; i < n; i++ ){
			tmp += chineselist.charAt(Math.floor(Math.random() * chineselist.length));
		}
		return tmp;
	}
	
    var mysingle=$("form .inf");

    $("#teamname").val(randomch(5));
    for(i=0;i<mysingle.length;i++){
        mysingle.eq(i).find("[name*='realname']").val(randomch(3));//name
        mysingle.eq(i).find("[name*='stu_num']").val(random0(7));//stunum
        mysingle.eq(i).find("[name*='birthday']").val(randomdate());//birthday
        mysingle.eq(i).find("[name*='id_num']").val(generatePid());//idnum
        mysingle.eq(i).find("[name*='cellphone']").val(random0(10));
        
        if(Math.random()>0.6){
            mysingle.eq(i).find(".super").prop("checked",true);//super
            mysingle.eq(i).find(".super").next("input[type=hidden]").val(1);
        }
        
        if(Math.random()>0.7){

            mysingle.eq(i).find("[name*='foreign_check']").prop("checked",true);//foreign
            mysingle.eq(i).find("[name*='foreign_check']").next("input[type=hidden]").val(1);
            mysingle.eq(i).find("[name*='id_num']").val(randoma0(10));//passportid
            mysingle.eq(i).find("[name*='country']").val(countrylist[Math.floor(Math.random() * countrylist.length)]);//country
            if(Math.random()>0.7){//gender
                mysingle.eq(i).find("[name*='gender']").eq(0).prop("checked",true);
            }else{
                mysingle.eq(i).find("[name*='gender']").eq(1).prop("checked",true);
            }
            mysingle.eq(i).find("[name*='passport_name']").val(randomA(20));//passportname
            
            
            
            mysingle.eq(i).find("[name*='foreign_check']").siblings(".foreign_inf").addClass("show_opacity");
            mysingle.eq(i).find("[name*='foreign_check']").siblings(".fieldname").eq(5).text("護照號碼");

        }
    }
}())