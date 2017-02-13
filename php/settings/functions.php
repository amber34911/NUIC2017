<?php
//for hashing password
function nic_hash($string){
    return hash("sha256",$string."qqq") . hash("md5",$string."asd");
}

//clean up strings
//only english char and numbers and '_' kept
function clean($string) {
   //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\_]/', '', $string); // Removes special chars.

   return $string;
}

//for setup session expire time in second
function start_session($expire = 0){
    if ($expire == 0) {
        $expire = ini_get('session.gc_maxlifetime');
    } else {
        ini_set('session.gc_maxlifetime', $expire);
    }

    if (empty($_COOKIE['PHPSESSID'])) {
        session_set_cookie_params($expire, "/", "", TRUE, TRUE);
        session_save_path("/sessions");
        session_start();
    } else {
		session_save_path("/sessions");
        session_start();
        setcookie('PHPSESSID', session_id(), time() + $expire,"/","",TRUE,TRUE);
    }
}
// check have critical character
function check_have_critical_character($source){
   return strlen($source) != strlen(strip_tags($source)); // 阻擋HTML關鍵字
}

// convert null to emptry string
function null_to_empty_string($val){
   if(is_null($val)){
      return "";  
   }
   else{
      return $val;
   }
}

// check fields in hash
function check_input_fields($fields,$data){
   // 空值欄位自動轉為空字串
   $fields = array_map("null_to_empty_string",$fields);



   // check password match
   if(array_key_exists("password1",$fields)&&array_key_exists("password2",$fields)){ 
      if($fields['password1']!=$fields['password2']){
         $data->error.="密碼與確認密碼不符\n";
      }
   }
   //check for special char in username
   if(array_key_exists("username",$fields)){
       if (strlen($fields['username'])>32){
         $data->error.="'使用者名稱'欄位長度過長 請縮短長度\n";
      }
      if (preg_match('/[^a-zA-Z0-9_]/', $fields["username"])){
         //$data->error.="Special characters in username\n";
         $data->error.="'使用者名稱'欄位僅限英數字及底線\n";
      }
   }
    //check for special char in passport name
    if(array_key_exists("passport_name",$fields)){
      // check for special char in realname
      if (strlen($fields['passport_name'])>64){
         //$data->error.="Password length is too\n";
         $data->error.="'護照上姓名'欄位長度過長 請縮短長度\n";
      }
      if (preg_match('/(*UTF8)[\'^£$%&*()}{@#~?!><>,|=_+¬]/', $fields['passport_name'])||check_have_critical_character($fields['passport_name'])){
         $data->error.="'護照上姓名'欄位內含違法字元\n";
         
      }
   }
    //check for special char in country
    if(array_key_exists("country",$fields)){
      // check for special char in realname
      if (strlen($fields['country'])>64){
         //$data->error.="Password length is too\n";
         $data->error.="'國籍'欄位長度過長 請縮短長度\n";
      }
      if (preg_match('/(*UTF8)[\'^£$%&*()}{@#~?!><>,|=_+¬]/', $fields['country'])||check_have_critical_character($fields['country'])){
         $data->error.="'國籍'欄位內含違法字元\n";
      }
   }
   
   //check for special char in password1
   if(array_key_exists("password1",$fields)){
      if (strlen($fields['password1'])>128){
         $data->error.="'密碼'欄位長度過長 請縮短長度\n";
      }
      if (check_have_critical_character($fields['password1'])){
         //$data->error.="Special characters in password\n";
         $data->error.="'密碼'欄位內含違法字元\n";
      }   
   }
   //check for special char in password2
   if(array_key_exists("password2",$fields)){
      if (strlen($fields['password2'])>128){
         //$data->error.="Password length is too\n";
         $data->error.="'確認密碼'欄位長度過長 請縮短長度\n";
      }
      if (check_have_critical_character($fields['password2'])){
         //$data->error.="Special characters in password\n";
         $data->error.="'確認密碼'欄位內含違法字元\n";
      }   
   }
   // real name
   if(array_key_exists("realname",$fields)){
      // check for special char in realname
      if (strlen($fields['realname'])>64){
         //$data->error.="Password length is too\n";
         $data->error.="'真實姓名'欄位長度過長 請縮短長度\n";
      }
      if (preg_match('/(*UTF8)[\'^£$%&*()}{@#~?!><>,|=_+¬]/', $fields['realname'])||check_have_critical_character($fields['realname'])){
         $data->error.="'真實姓名'欄位內含違法字元\n";
         
      }
   }
   // school
   if(array_key_exists("school",$fields)){
      if (strlen($fields['school'])>32){
         //$data->error.="Password length is too\n";
         $data->error.="'學校'欄位長度過長 請縮短長度\n";
      }
      if (preg_match('/(*UTF8)[\'^£$%&*()}{@#~?!><>,|=_+¬]/', $fields['school'])||check_have_critical_character($fields['school'])){
         $data->error.="'學校'欄位內含違法字元\n";
      }
   }
   // department
   if(array_key_exists("department",$fields)){
      if (strlen($fields['password1'])>32){
         $data->error.="'科系'欄位長度過長 請縮短長度\n";
      }
      if (preg_match('/(*UTF8)[\'^£$%&*()}{@#~?!><>,|=_+¬]/', $fields['department'])||check_have_critical_character($fields['department'])){
         $data->error.="'科系'欄位內含違法字元\n";
      }
   }
   // cellphone
   if(array_key_exists("cellphone",$fields)){
       if (strlen($fields['cellphone'])>64){
         $data->error.="'手機號碼'欄位長度過長 請縮短長度\n";
      }
      if (preg_match('/[\'^£$%&*()}{@#~?!><>,|=_+¬]|[^0-9]/', $fields['cellphone'])||check_have_critical_character($fields['cellphone'])){
         $data->error.="'手機號碼'欄位內含違法字元\n";
      }
   }
   // email
   if(array_key_exists("email",$fields)){
        if (strlen($fields['email'])>64){
         $data->error.="'E-mail'欄位長度過長 請縮短長度\n";
      }
      if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)||check_have_critical_character($fields['email'])){
         $data->error.="無法識別的E-mail地址\n";
      }
   }
   
   // 檢查team-id，只允許純數字
   $_field_name = "team_id";
   if(array_key_exists($_field_name,$fields)){
      $_field = $fields[$_field_name];
      if (preg_match('/\D/', $_field)){
         $data->error.="無法識別的隊伍ID\n";
      }   
   }
   
   // 檢查money_inf是否包含有特殊字元
   $_field_name = "money_inf";
   if(array_key_exists($_field_name,$fields)){
      $_field = $fields[$_field_name];
      $in=$_field;
      preg_match('/[a-z0-9]{64}/', $in,$out);
      if($out[0]!=$in){
         $data->error.="繳費資訊格式不符，請確認已複製完整代碼\n";
      }
      if (check_have_critical_character($_field)){
         $data->error.="繳費資訊內含違法字元\n";
      }   
   }

   // 檢查addinf_action，只能有init和update
   $_field_name = "addinf_action";
   if(array_key_exists($_field_name,$fields)){
      $_field = $fields[$_field_name];
      if ($_field!="init" and $_field != "update"){
         $data->error.="無法識別的指令\n";
      }   
   }

   // 檢查game_category, 限制：array("b_bas","g_bas","b_vol","g_vol","bad","tab","sof","soc");
   $_field_name = "game_category";
   $_restrict = array("b_bas","g_bas","b_vol","g_vol","bad","tab","sof","soc");
   if(array_key_exists($_field_name,$fields)){
      $_field = $fields[$_field_name];
      if (!in_array($_field,$_restrict)){
         $data->error.="無法識別的比賽類別\n"; }   
   }

   // 檢查team name，阻擋關鍵字
   $_field_name = "teamname";
   if(array_key_exists($_field_name,$fields)){
     
      $_field = $fields[$_field_name];
      if (strlen($_field)>32){
         $data->error.="'隊伍名稱'欄位長度過長 請縮短長度\n";
      }
      if (check_have_critical_character($_field) // 檢查關鍵字
         || preg_match('/(*UTF8)[\'^£$%&*()}{@#~?!><>,|=_+¬]/',$_field) // 其他不合法的字元
      )
      {
         $data->error.="隊伍名稱內含違法字元\n";
      }   
       
   }


   // 檢查stu num，限制英數底線
   $_field_name = "stu_num";
   if(array_key_exists($_field_name,$fields)){ 
      $_field = $fields[$_field_name];
      if (strlen($_field)>32){
         $data->error.="'學號'欄位長度過長 請縮短長度\n";
      }
      if (preg_match('/[^a-zA-Z0-9_]/', $_field)){
         $data->error.="'學號'欄位僅限英數字及底線\n";
      }   
   }
   
   // 檢查日期，限制NNNN-NN-NN
   $_field_name = "date";
   if(array_key_exists($_field_name,$fields)){
      $_field = $fields[$_field_name];
      if (!preg_match('/\d\d\d\d-\d\d-\d\d/', $_field)){
         $data->error.="'日期'格式必須為 yyyy-mm-dd\n";
      }   
   }
   // 檢查id num，限制英數底線
   $_field_name = "id_num";
   if(array_key_exists($_field_name,$fields)){
      $_field = $fields[$_field_name];
      if (strlen($_field)>32){
         $data->error.="'身分證字號'欄位長度過長 請縮短長度\n";
      }
      if (preg_match('/[^a-zA-Z0-9_]/', $_field)){
         $data->error.="'身分證字號'欄位僅限英數字及底線\n";
      }   
   }
   $_field_name = "local_id_num";
   if(array_key_exists($_field_name,$fields)){
      $_field = $fields[$_field_name];
      $tab = "ABCDEFGHJKLMNPQRSTUVXYWZIO";
      $A1 = array(1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,3,3,3,3,3,3 );
      $A2 = array(0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5 );
      $Mx = array(9,8,7,6,5,4,3,2,1,1);
      if( strlen($_field) != 10 ){
         $data->error.="'身分證字號'欄位長度不符";
      }
      else{
         $error = 0;
         $i = strpos($tab, $_field{0});
         if ( $i == -1 ) $error=1;
         else{
            $sum = $A1[$i] + $A2[$i]*9;
            for ( $i=1; $i<10; $i++ ) {
                $v = (int)$_field{$i};
                if ( is_nan($v) || $v>9 || $v<0 ) $error=1;
                $sum = $sum + $v * $Mx[$i];
            }
        }
        if( $sum % 10 != 0 ) $error=1;
        if($error) $data->error.="'身份證字號'欄位格式不符";
      }
   }
   // 檢查addteam_super，阻擋關鍵字
   $_field_name = "addteam_super";
   if(array_key_exists($_field_name,$fields)){
      $_field = $fields[$_field_name];
      if ($_field!=0&&$_field!=1){
         $data->error.="無法識別的'體資生'欄位\n";
      }
   }
    //check is foreign
   $_field_name = "foreign";
   if(array_key_exists($_field_name,$fields)){
      $_field = $fields[$_field_name];
      if ($_field!=0&&$_field!=1){
         $data->error.="無法識別的'外籍生'欄位\n";
      }   
   }
   $_field_name = "gender";
   if(array_key_exists($_field_name,$fields)){
      $_field = $fields[$_field_name];
      if ($_field!="m"&&$_field!="f"&&$_field!=""){
         $data->error.="無法識別的'性別'欄位\n";
      }   
   }
    
   // 純數字檢查
   $_field_name = "number";
   if(array_key_exists($_field_name,$fields)){
      $_field = $fields[$_field_name];
      if (preg_match('/\D/', $_field)){
         $data->error.="數字安全性檢查未通過！請檢查輸入資料是否合法！\n";
      }   
   }
}

// 資料夾名稱跳脫
function escape_directory_name($old){
   $new = str_replace('/', '_', $old);
   return $new;
} 


// 加密圖檔名稱
function picture_name_hash($type,$team,$id_num,$name,$pictype){
   $ori_name = "$type/$team/$id_num-$name.$pictype";
   $new_name = hash("sha256",$ori_name) . hash("md5",$ori_name) . ".jpg";
   return $new_name;
}


// 把加密反查資料放進資料庫，但這好像用不到了，但仍保留(?
function make_picture_entry($db,$team,$filename,$new_name){
   if($db){
      // deprecated : 以下這行本來是要把檔案格式存進去的，不過都限制JPG了，就不必要了
      // $new_name = preg_replace('/.*([Pp][Nn][Gg]|[Jj][Pp][Ee]?[Gg])$/',$hashed.'.${1}',$name);
      $sql = "INSERT INTO `pic` (team,ori_name,hashed_name)
         VALUES (?,?,?)";
      $sth = $db->prepare($sql);
      $result = $sth->execute(array($team,$filename,$new_name)); // 隊名識別，原始檔案名稱，加密名稱
      if($result){
         return true;
      }
      else{
         return false;
      }
   }
   else{
      // echo "no db";
      return false;
   }
}

function create_thumbnail($src_filename,$dst_filename,$max_side){
   // 建立縮圖
   $src = imagecreatefromjpeg($src_filename);   //讀取來源圖檔
   $src_w = imagesx($src);     //取得來源圖檔長寬
   $src_h = imagesy($src);
   // 利用最長邊計算新長寬，
   if($src_w > $max_side || $src_h > $max_side){
      if($src_w > $src_h){
         // 最長邊是寬
         $new_w = $max_side;
         $new_h = $new_w * ($src_h / $src_w);
      }
      else{
         // 最長邊是高
         $new_h = $max_side;
         $new_w = $new_h * ($src_w / $src_h);  
      }
   }
   else{
      $new_h = $src_h;
      $new_w = $src_w;
   }

   $thumb = imagecreatetruecolor($new_w, $new_h);    //建立空白縮圖

   //設定空白縮圖的背景，如不設定，背景預設為黑色
   $bg = imagecolorallocate($thumb,255,255,255);       //空白縮圖的背景顏色：白色
   imagefilledrectangle($thumb,0,0,$src_w,$src_h,$bg); //將顏色填入縮圖

   //執行縮圖
   imagecopyresized($thumb, $src, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);

   // 儲存新圖片
   imagejpeg($thumb, $dst_filename);

}
function extract_file_type($file){
    $file_info = new finfo(FILEINFO_MIME);
    $mime_type = $file_info->buffer(file_get_contents($file));
    return $mime_type;
}


// 上傳檔案：參數：DB物件、DATA、檔案物件、識別資料物件、隊伍ID、索引index、圖片類型
function upload_file($db,$data,$file_obj,$post_obj,$tid,$index,$pictype){ 
   if ($file_obj["error"][$index] > 0){
      //echo "Error: " . $file_obj["error"][$index];
      return false;
   }
   else{
      //echo "檔案名稱: " . $file_obj["name"][$index]."<br/>";
      //echo "檔案類型: " . $file_obj["type"][$index]."<br/>";
      //echo "檔案大小: " . ($file_obj["size"][$index] / 1024)." Kb<br />";
      //echo "暫存名稱: " . $file_obj["tmp_name"][$index]."<br />";
      // 取出項目資訊
      $game_type = $post_obj["category"];
      $game_team = (string)$tid;
      $game_id_num = $post_obj["id_num"][$index];
      $game_name = $post_obj["realname"][$index];
      // 產生上傳/縮圖資料夾
      $escape_type = escape_directory_name($game_type);
      $escape_team = escape_directory_name($game_team);
      $escape_id_num = escape_directory_name($game_id_num);
      $path = "$escape_type/$escape_team/";
      $upload_dir = "upload/$path";
      $thumb_dir = "thumb/$path";
      // 取出檔案格式與原始檔案名稱
      $type = extract_file_type($file_obj["tmp_name"][$index]); #$file_obj["type"][$index];
      $filename = $file_obj["name"][$index]; 
      // 檔案格式判斷，只允許JPG
      if(preg_match('/image\/([Jj][Pp][Ee]?[Gg])/',$type)==0){
         //echo "檔案格式錯誤！" . "<br />";   
         $data->error .= "違法的檔案格式($type)"; 
         return false;
      }
      // 產生加密檔名
      $new_name = picture_name_hash($game_type,$game_team,$game_id_num,$game_name,$pictype);
      // 紀錄
      make_picture_entry($db,"$game_type-$game_team",$filename,$new_name);
      // 建立資料夾：上傳與縮圖
      @mkdir($upload_dir,0777,true);
      @mkdir($thumb_dir,0777,true);
      // 判斷上傳資料夾中是否有同名檔案存在
      if (file_exists($upload_dir . $new_name)){
         //echo "在upload資料夾中相對應的目錄已經存在同樣的檔案名稱";
      }

      create_thumbnail($file_obj["tmp_name"][$index],$upload_dir.$new_name,1200);
      create_thumbnail($file_obj["tmp_name"][$index],$thumb_dir.$new_name,200);
      
      // Deprecate<原始圖片不再需要> : 移動原始圖片到指定位置
      // move_uploaded_file($file_obj["tmp_name"][$index],$upload_dir.$new_name);
      // 回傳新名稱
      return $new_name;

   }
}

function random_password(){
   $validchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
   $validcharnum = strlen($validchars);
   $result = "";
   $length = 8;
   for($i=0; $i<$length; $i++){
      $index = mt_rand(0, $validcharnum-1);
      $result .= $validchars[$index];
   }
   return $result;
}

function test(){
   echo "hi";
}


function get_menu(){
    global $db;
    global $uid;
    $sql = "select uid,category,count(*) as c from teams where uid=? and success=1 group by category order by c desc";
    $sth = $db->prepare($sql);
    $sth->execute(array($_SESSION["uid"]));
    $result = $sth->fetchObject();
    if($result->c==2){
        $show_change=1;
    }
    $sql = "select * from users where uid=?";
    $sth = $db->prepare($sql);
    $sth->execute(array($uid));
    $result = $sth->fetchObject();
    $veri=$result->veri_state;
    echo '
        <ul class="mymenu">
                    <a href="../main"><li>您好 '.$_SESSION["username"].'</li></a>
                    <hr>
                    <a href="../reg?category=b_bas"><li>男籃報名'.($veri==1?'':'(尚未認證)').'</li></a>
                    <a href="../reg?category=g_bas"><li>女籃報名'.($veri==1?'':'(尚未認證)').'</li></a>
                    <a href="../reg?category=b_vol"><li>男排報名'.($veri==1?'':'(尚未認證)').'</li></a>
                    <a href="../reg?category=g_vol"><li>女排報名'.($veri==1?'':'(尚未認證)').'</li></a>
                    <a href="../reg?category=tab"><li>桌球報名'.($veri==1?'':'(尚未認證)').'</li></a>
                    <a href="../reg?category=bad"><li>羽球報名'.($veri==1?'':'(尚未認證)').'</li></a>
                    <a href="../reg?category=sof"><li>壘球報名'.($veri==1?'':'(尚未認證)').'</li></a>
                    <hr>
                    <a href="../myteam"><li>我的隊伍</li></a>'.
                    ($show_change==1&&beforechangedue()?'<a href="../change"><li>隊員交換</li></a>':'')
                    .'<a href="../edit"><li>使用者帳戶</li></a>'.
                    ($veri==1?'':'<a href="../verify"><li>重寄認證信</li></a>')
                    .'<a href="../logout"><li>登出</li></a>
                </ul> ';
}
function rrmdir($dir) { 
    if (is_dir($dir)) { 
        $objects = scandir($dir); 
        foreach ($objects as $object) { 
            if ($object != "." && $object != "..") { 
                if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
            } 
        } 
    reset($objects); 
    rmdir($dir); 
    }
}
function Zip($source, $destination){
    
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }
    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }
    
    $source = str_replace('\\', '/', realpath($source));
    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', $file);
            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;
            $file = realpath($file);
            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {   
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}
function get_ip(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
       $myip = $_SERVER['HTTP_CLIENT_IP'];
    }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
       $myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
       $myip= $_SERVER['REMOTE_ADDR'];
    }
    return $myip;
}


function testing(){
/*用cookie當判斷依據
    
    if($_COOKIE["WP_TESTING"]!="330c8ef4621649a0237ead79e3791a4af2d57a83979aa0b0976c483fb2f6a377"){
        
        header( 'Location: https://nuic2017.com/sorry' ) ;
    }else{
        setcookie("WP_TESTING","330c8ef4621649a0237ead79e3791a4af2d57a83979aa0b0976c483fb2f6a377",0,"/","",false,true);
    }
  */
    /*用IP當判斷依據
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
   $myip = $_SERVER['HTTP_CLIENT_IP'];
}else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
   $myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
   $myip= $_SERVER['REMOTE_ADDR'];
}
if(substr($myip,0,7)!="140.113"){
	header( 'Location: https://nuic2017.com/sorry' ) ;
}*/
}
function get_mime_type($file) {
	$mtype = false;
    /*
	if (function_exists('finfo_open')) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mtype = finfo_file($finfo, $file);
		finfo_close($finfo);
	} elseif (function_exists('mime_content_type')) {
    */
		$mtype = mime_content_type($file);
    /*
	} 
*/
	return $mtype;
}
function remote_log($message){
	//重要事件log在遠端伺服器
	$post = array(
		"message"=>$message,
		"ip"=>get_ip(),
		"time"=>date("Y-m-d H:i:s")
	);
	$ch = curl_init();
	$options = array(
		CURLOPT_URL=>"http://ip/nuiclog.php",
		CURLOPT_HEADER=>0,
		CURLOPT_VERBOSE=>0,
		CURLOPT_RETURNTRANSFER=>true,
		CURLOPT_USERAGENT=>"Mozilla/4.0 (compatible;)",
		CURLOPT_POST=>true,
		CURLOPT_POSTFIELDS=>http_build_query($post),
	);
	curl_setopt_array($ch, $options);

	$result = curl_exec($ch); 
	curl_close($ch);
}
?>
