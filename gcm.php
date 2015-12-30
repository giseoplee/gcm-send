<?php
include "./lib/cDB.class.php";
include "./lib/cLIB.class.php";
require_once "./lib/GCMPushMessage.php";
define("MULTICAST_COUNT", 1000);

// */10 * * * * /usr/bin/php /home/cms/wwwroot/gcm/push.php

/*
10분마다 실행
*/

$send_key = $_POST['no'];
echo $send_key;

// 즉시발송
// $query = "select * from pushes where status_sending=10 and status=20 and sending_at<=now()";
// $result = mysql_query($query);
// while($row = mysql_fetch_array($result)){
//     unset($sending_devices, $divide_array, $send_data);

//     // 발송중인지 다시 체크
//     $pushing = $cDB->Select("select * from pushes where id={$row[id]}");
//     if($pushing['status_sending']!=10) continue;

//     mysql_query("update pushes set status_sending=20 where id={$row[id]}");

//     $game = $cDB->Select("select * from games where id={$row[game_id]}");
//     $_gcm_api_key = $game['gcm_key'];

//     $send_data['GcmPackageName'] = $game['package'];
//     $send_data['GcmTitle'] = $row['subject'];
//     $send_data['GcmMessage'] = $row['content'];
//     $send_data['GcmImageUrl'] = $row['image_1'];
//     $send_data['GcmEventUrl'] = "";

//     $query_user = "
//         select b.*, a.country
//         from users a, user_games b
//         where
//             b.game_id={$row[game_id]} and a.id=b.user_id and a.userid='pgasweet3@gmail.com'
//     ";

//     $result_user = mysql_query($query_user);
//     $country_arr = explode(',', $row['sending_country']);
//     while($row2 = mysql_fetch_array($result_user)){
//         if(!in_array($row2['country'], $country_arr)) continue;
//         $sending_devices[] = $row2['gcm_key'];
//     }

//     $divide_array = array_chunk($sending_devices, MULTICAST_COUNT);
//     for($j=0; $j < count($divide_array); $j++) {
//         $gcm = new GCMPushMessage($_gcm_api_key);
//         $gcm->setDevices($divide_array[$j]);
//         $response = $gcm->send($send_data);
//     }


//     mysql_query("update pushes set status_sending=30 where id={$row[id]}");
// }