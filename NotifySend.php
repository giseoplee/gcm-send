<?php
// include "./lib/cDB.class.php";
// include "./lib/cLIB.class.php";
include "./lib/header.php";
require_once "./lib/GCMPushMessage.php";
define("MULTICAST_COUNT", 2);
error_reporting (E_ALL ^ E_NOTICE);

// $notification_id = $_POST['app'];
// $limit = $_POST['limit'];
// $min = $_POST['min'];

$notification_id = $_GET['app'];
$limit = $_GET['limit'];
$min = $_GET['min'];

$notify_data = $cDB->Select("select * from notifications where id={$notification_id}");

//print_r($notify_data);

$query = "select id, registration_key from users where application_id={$notify_data[application_id]} and id > {$min} limit {$limit}";
$result = mysql_query($query);

while($rows = mysql_fetch_assoc($result)){
	// print_r($rows);
	// echo "<br />";
	//$registration_ids[$arr_cnt] = $rows['registration_key'];
	$registration_ids[] = $rows['registration_key'];
	$next_min = $rows['id'];
	//$arr_cnt++;
}

//print_r($registration_ids);
$next_min+=1;
//$registration_ids = $cDB->Select($query);
//print_r($registration_ids);



$_gcm_api_key = "AIzaSyDa1JpGKQZYNvecZzUe3PEcZ4mQqAKzjv0";

$send_data['no'] = "1";
$send_data['code'] = $notify_data['image_flag'];
$send_data['title'] = $notify_data['title'];
$send_data['msg'] = $notify_data['description'];
$send_data['ticker'] = $notify_data['ticker'];
$send_data['url'] = $notify_data['url'];


// echo count($registration_ids);
// echo "<br /><br /><br /><br />";

$divide_array = array_chunk($registration_ids, MULTICAST_COUNT);

// echo json_encode($send_data);
// echo "<br /><br /><br />";
// echo count($divide_array);

for($j=0; $j < count($divide_array); $j++) {
    $gcm = new GCMPushMessage($_gcm_api_key);
    $gcm->setDevices($divide_array[$j]);
    //print_r($gcm);
    $response = $gcm->send($send_data);
    echo $response;
    echo "<br />";
}




?>