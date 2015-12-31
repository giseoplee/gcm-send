<?php
/***** _header.php 시작 *****/

if(preg_match("/local\./", $_SERVER['HTTP_HOST'])){
  define("_DEV", true);
  define("_DEBUG", true);
}else{
  define("_DEV", false);
  define("_DEBUG", false);
}


#상수 정의 1
define("_TT", true);

define("_ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."/");
define("_PASSWORD", false);

#PHP error_reporting
if(_DEBUG) error_reporting (E_ALL ^ E_NOTICE);
else error_reporting  (0);
error_reporting (E_ALL ^ E_NOTICE);

//쿠키와 세션의 원활한 사용을 위한 헤더 글로벌헤더
@header("Content-Type: text/html; charset=utf-8");
header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');
if (!isset($set_time_limit)) $set_time_limit = 0;
@set_time_limit($set_time_limit);
//@session_save_path($_SERVER[DOCUMENT_ROOT]."/_data/_session"); 필요 없음
ini_set("session.use_trans_sid", 0);
ini_set("url_rewriter.tags","");
if (isset($SESSION_CACHE_LIMITER)) @session_cache_limiter($SESSION_CACHE_LIMITER);
else @session_cache_limiter("no-cache, must-revalidate");
ini_set("session.cache_expire", 360);
ini_set("session.gc_maxlifetime", 10800);
ini_set("session.gc_probability", 1);
ini_set("session.gc_divisor", 100);
ini_set("session.cookie_domain", '');

setlocale(LC_CTYPE, 'ko_KR.eucKR');

ini_set("memory_limit","-1");
@session_start();
#클래스 파일 포함
require_once _ROOT_DIR."lib/cDB.class.php";
//require_once _ROOT_DIR."lib/cLIB.class.php";
// include_once _ROOT_DIR."cDB.class.php"
// include_once _ROOT_DIR."cLIB.class.php";


#모듈
require_once _ROOT_DIR."_config.php";

#객체선언1
$cLIB = new cLIB();

#디버깅 - 속도체크
if(DEBUG) $debug[total_stime] = $cLIB->getMicroTime();

#객체선언2
// print_r($db_setting);
$cDB = new cDB($db_setting);


$chic[now_time] = date("Y-m-d H:i:s", time());
// $chic[page_title] = "Crema.me Worldcup";
// $chic[smd] = $_GET[smd];
// $chic[tmd] = $_GET[tmd];
// $chic[pic_code] = 6428792;




/***** _header.php 끝 *****/
?>