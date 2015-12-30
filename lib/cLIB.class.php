<?
class cLIB{


function getArrSeleteOption($_arr){
	for($i=1; $i<count($_arr); $i++){
		echo '<option value="'.$i.'">'.$_arr[$i].'</option>';
	}
}

	######################################## 세션/쿠키
	// 세션변수 생성(그누보드's)
	function set_session($session_name, $value){
		if (PHP_VERSION < '5.3.0')
			session_register($session_name);
		$$session_name = $_SESSION["$session_name"] = $value;
	}

	// 세션변수값 얻음(그누보드's)
	function get_session($session_name){
		return $_SESSION[$session_name];
	}

	// 쿠키변수 생성(그누보드's)
	function set_cookie($cookie_name, $value, $expire){
		global $g4;
		setcookie(md5($cookie_name), base64_encode($value), $g4[server_time] + $expire, '/', $g4[cookie_domain]);
	}

	// 쿠키변수값 얻음(그누보드's)
	function get_cookie($cookie_name){
		return base64_decode($_COOKIE[md5($cookie_name)]);
	}

	######################################## -
	function getDateTime($datetime){
		$return=strtoupper(date("Y-m-d", strtotime($datetime)));
		return $return;
	}

	function getDateTime1($datetime){
		$return=date("Y.m.d", strtotime($datetime));
		return $return;
	}

	function getDateTime2($datetime){
		$return=date("Y.m.d H:i", strtotime($datetime));
		return $return;
	}

	function getMicrotime() {
		$microtimestmp = explode(" ",microtime());
		return $microtimestmp[0]+$microtimestmp[1];
	}

	// 한글 요일 (그누보드's)
	function getYoil($date, $full=0){
		$arr_yoil = array ("일", "월", "화", "수", "목", "금", "토");

		$yoil = date("w", strtotime($date));
		$str = $arr_yoil[$yoil];
		if ($full) {
			$str .= "요일";
		}
		return $str;
	}

	function movepage($url) {
		echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
		exit;
	}

	function movepage_close($url){
		echo"<script language=javascript>opener.location.href='$url'; window.close();</script>";
		exit;
	}

	function opener_refresh($url){
		echo"<script language=javascript>opener.location.href='$url';</script>";
	}

	function aerror($msg){
		echo"<script language=javascript>alert('$msg');</script>";
	}

	function aerror_b($msg){
		echo"<script language=javascript>alert('$msg'); history.go(-1);</script>";
		exit;
	}

	function aerror_c($msg){
		echo"<script language=javascript>alert('$msg'); self.close();</script>";
		exit;
	}

	function getSelectbox($query, $field_name, $field_value, $txt_header="", $txt_footer=""){
		$ret = mysql_query($query);
		while($select = mysql_fetch_array($ret)) $option .= "<option value='".$select[0]."'>".$txt_header.$select[1]."(".$select[0].")".$txt_footer."</option>\n";
		$script = "
		<script language=\"javascript\">mSelect(\"$field_name\", \"".$field_value."\");</script>";
		$ret = $option.$script;
		return $ret;
	}

	######################################## 파일관련
	function getFileSize($size) {
		if(!$size) return "0 Byte";
		if($size<1024) {
			return ($size." Byte");
		} elseif($size >1024 && $size< 1024 *1024)  {
			return sprintf("%0.1f KB",$size / 1024);
		}
		else return sprintf("%0.2f MB",$size / (1024*1024));
	}

	// 폴더의 용량 ($dir는 / 없이 넘기세요) (그누보드's)
	function getDirSize($dir)
	{
		$size = 0;
		$d = dir($dir);
		while ($entry = $d->read()) {
			if ($entry != "." && $entry != "..") {
				$size += filesize("$dir/$entry");
			}
		}
		$d->close();
		return $size;
	}

	######################################## 잘못된 접근 통제
	//레퍼러 검사
	function wrong_path(){
		global $HTTP_REFERER;
		if(!eregi("here", $HTTP_REFERER)) return false;
		return true;
	}

	//쿠키검사(없을경우)
	function wrong_cookie($errcode){
		global $HTTP_COOKIE_VARS;
		if(!$HTTP_COOKIE_VARS[here]) aerror_b("잘못된 경로로 접근하였습니다.\\nError Code : $errcode");
	}

	//메소드검사
	function wrong_way(){
		if(getenv("REQUEST_METHOD") == 'GET' ) aerror_b("잘못된 경로로 접속하였습니다.");
	}

	######################################## 각종 데이터 치환
	//금액표시
	function getNumberFormat($price){
		$price=number_format("$price");
		return $price;
	}
	function getNumberFormatD($price){
		$price="$".number_format("$price",2);
		return $price;
	}
	function cut_str($str, $len, $suffix="…"){
		$c = substr(str_pad(decbin(ord($str{$len})),8,'0',STR_PAD_LEFT),0,2); 
		if ($c == '10') 
			for (;$c != '11' && $c{0} == 1;$c = substr(str_pad(decbin(ord($str{--$len})),8,'0',STR_PAD_LEFT),0,2)); 
		return substr($str,0,$len) . (strlen($str)-strlen($suffix) >= $len ? $suffix : ''); 
	}

	// 태그제거.
	function del_html($str) {
		$str = str_replace( ">", "&gt;",$str);
		$str = str_replace( "<", "&lt;",$str);
		$str = str_replace( "\"", "&quot;",$str);
		return $str;
	}

	// 태그를 이용한 테러방지
	function avoid_crack($str)
	{
		$str=eregi_replace("<","&lt;",$str);
		$str=eregi_replace("&lt;div","<div",$str);
		$str=eregi_replace("&lt;p","<p",$str);
		$str=eregi_replace("&lt;font","<font",$str);
		$str=eregi_replace("&lt;b","<b",$str);
		$str=eregi_replace("&lt;marquee","<marquee",$str);
		$str=eregi_replace("&lt;img","<img",$str);
		$str=eregi_replace("&lt;a","<a",$str);
		$str=eregi_replace("&lt;embed","<embed",$str);

		$str=eregi_replace("&lt;/div","</div",$str);
		$str=eregi_replace("&lt;/p","</p",$str);
		$str=eregi_replace("&lt;/font","</font",$str);
		$str=eregi_replace("&lt;/b","</b",$str);
		$str=eregi_replace("&lt;/marquee","</marquee",$str);
		$str=eregi_replace("&lt;/img","</img",$str);
		$str=eregi_replace("&lt;/a>","</a>",$str);
		$str=eregi_replace("&lt;/embed","</embed",$str);

		return $str;
	}

	//0,1에 대한 텍스트 출력
	function getTrueFalse($value, $true_txt, $false_txt){
		if($value=="0") $return_txt="$false_txt";
		elseif($value=="1") $return_txt="$true_txt";
		else $return_txt="bad commander!";
		return $return_txt;
	}

	//체크박스 0,1 값 리턴
	function getCheckbox($int){
		if($int!="1") return "0";
		else return "1";
	}

	//비어있는 문자열 구분.
	function isEmpty($into) {
		if(eregi("[^[:space:]]",$into)) return 0;
		else return 1;
	}

	// 자리수 0으로 채우기
	function getZeroFill($str, $len){
		for($i=1; strlen($str)<$len; $i++){
			$str="0".$str;
		}
		return $str;
	}
	######################################## 폼관련
	function getOptionTag($optArr){
		//optArr 형식 : title1 |,| title2 |;| value1 |,| value2
		$optArr2=explode("|;|", $optArr);
		$optArr2[0]=explode("|,|", $optArr2[0]);
		$optArr2[1]=explode("|,|", $optArr2[1]);
		$optionTag = '<option>선택</option>';
		for($i=0; $i < count($optArr2[0]); $i++){
			$optionTag .= '<option value="'.$optArr2[1][$i].'">'.$optArr2[0][$i].'</option>';
		}
		return $optionTag;
	}

}
function getFileNames($directory) {
    $results = array(); 
    $handler = opendir($directory); 
    while ($file = readdir($handler)) { 
        if ($file != '.' && $file != '..' && is_dir($file) != '1') {
            $results[] = $file; 
        }
    } 
    closedir($handler); 
    return $results;
}
function getRatioSize($img, $width, $height){
	$limit_w = $width; // 원하는 최대 가로 크기를 입력합니다.
	$limit_h = $height; // 원하는 최대 세로 크기를 입력합니다.

	$upfile = $img;
	//이미지 사이즈 비율조절 시작.
	$size = GetImageSize($upfile); 
	$width = $size[0]; //입력받은 파일의 가로크기를 구합니다.
	$height = $size[1]; //입력받은 파일의 세로크기를 구합니다.
	//긴쪽을 기준 사이즈로 한다.
	if ($width > $height) 
	{ $percentage = $width/$limit_w; }
	elseif ($height > $width)
	{ $percentage = $height/$limit_h; }
	else
	{ $percentage = $width/$limit_w; }
	//크기에서 긴쪽을 나누어서 비율을 정한다.
	$info[0] = $width/$percentage; 
	$info[1] = $height/$percentage; 

	return $info;
}

function getProfilePicUrl($memberInfo, $size=""){
	/*
	if($m[pic_profile]==1) $env[profilePicUrl] = "/_data/profilePic/".md5('bvm'.$env[member_no]).".jpg";
	elseif($m[fb_id]>1) $env[profilePicUrl] = "https://graph.facebook.com/".$m[fb_id]."/picture";
	else $env[profilePicUrl] = "/_images/4.jpg";
	*/
	if($memberInfo[pic_profile]==1) $env[profilePicUrl] = "http://assets.bananavote.com/_data/profilePic/".md5('bvm'.$memberInfo[mno]).".jpg";
	elseif($memberInfo[fb_id]>1 && $size=="large") $env[profilePicUrl] = "https://graph.facebook.com/".$memberInfo[fb_id]."/picture?type=large";
	elseif($memberInfo[fb_id]>1) $env[profilePicUrl] = "https://graph.facebook.com/".$memberInfo[fb_id]."/picture";
	else $env[profilePicUrl] = "/_images/bin_100x100.gif";

	//elseif($memberInfo[fb_id]>1) $env[profilePicUrl] = "https://graph.facebook.com/".$memberInfo[fb_id]."/picture?type=normal";

	return $env[profilePicUrl];
	///_images/blank.jpg
}
function getClipImageUrl($cno, $size=270){
	$imgUrl = "http://assets.bananavote.com/_data/clip/".$size."/".md5('bvc'.$cno.$size).".jpg";
	return $imgUrl;
}



function encrypt($str, $key){
    # Add PKCS7 padding.
    $block = mcrypt_get_block_size('des', 'ecb');
    if (($pad = $block - (strlen($str) % $block)) < $block) {
      $str .= str_repeat(chr($pad), $pad);
    }
    return mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
}

function decrypt($str, $key){
    $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
    # Strip padding out.
    $block = mcrypt_get_block_size('des', 'ecb');
    $pad = ord($str[($len = strlen($str)) - 1]);
    if ($pad && $pad < $block && preg_match(
          '/' . chr($pad) . '{' . $pad . '}$/', $str
                                            )
       ) {
      return substr($str, 0, strlen($str) - $pad);
    }
    return $str;
}

    function getWeeks($date, $rollover)
    {
        $cut = substr($date, 0, 8);
        $daylen = 86400;

        $timestamp = strtotime($date);
        $first = strtotime($cut . "00");
        $elapsed = ($timestamp - $first) / $daylen;

        $i = 1;
        $weeks = 1;

        for($i; $i<=$elapsed; $i++)
        {
            $dayfind = $cut . (strlen($i) < 2 ? '0' . $i : $i);
            $daytimestamp = strtotime($dayfind);

            $day = strtolower(date("l", $daytimestamp));

            if($day == strtolower($rollover))  $weeks ++;
        }

        return $weeks;
    }
?>