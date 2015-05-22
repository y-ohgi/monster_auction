<?php
// roomlist.php

/**
 * ルームへ参加
 * req:
 *   TODO:$_POST['uuid'];
 * res:
 *   {
 *     "rm_id": 1
 *     "rm_title": "部屋名"
 *     "rm_ppl": 1,   // 現在人数
 *     "rm_max": 8    //部屋の格納可能人数
 *   }
 *
 */

require_once('init.php');
require_once('db_connect.php');

require_once('../lib/Carbon/Carbon.php');
use Carbon\Carbon;

// room一覧の連想配列
$roomlist = array();
// rm_statをclosedにするまでの制限時間
$closelimit = 24;
$closetime = Carbon::now()->subHours($closelimit);


// TODO: uuidの認証をする


// $closetime時間の間使用されていない物のステータスを更新
// room_masterのrm_createdが3時間前の物を'closed'へupdate
// TODO: 'UPDATE room_master SET rm_stat = "closed" WHERE rm_created < :closetime' AND rm_ppl = 0;';
$sql = 'UPDATE room_master SET rm_stat = "closed" WHERE rm_created < :closetime';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':closetime', $closetime, PDO::PARAM_STR);
$stmt->execute();

// activelimitに達している物をupdateする

// room_masterからrm_stat = 'wait'の物をselect
$sql = 'SELECT rm_id, rm_title, rm_ppl, rm_max FROM room_master WHERE rm_stat = "wait"';
$stmt = $dbh->query($sql);
$roomlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

$jlist = json_encode($roomlist, JSON_UNESCAPED_UNICODE);
echo $jlist;
