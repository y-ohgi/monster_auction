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

// require_once('init.php');
//require_once('db_connect.php');

require_once(dirname(__FILE__).'/../conf/Conf.inc');

require_once('controller/Util.inc');
require_once('controller/Page.inc');
require_once('model/RoomDao.inc');
require_once('model/ActiveDao.inc');
//require_once('model/UserDao.inc');

require_once('../lib/Carbon/Carbon.php');
use Carbon\Carbon;

// req:
// $uuid = Util::h($_POST['uuid']);
// XXX:authUser($uuid);
// res:
$response = array(
    "status"=>null,
    "roomlist"=>array()
);
Page::setRes($response);


// time系の名前とかどうにかしたい
// $closelimit = Config::get('closetime');
// $activelimit = Config::get('activetime');

// 何分前までをルームを生かすか
$closelimit = 24;
$closetime = Carbon::now()->subHours($closelimit);

// TODO:クラス分けする
// 何秒前までをユーザーをアクティブと認めるか.
$activelimit = 30;
//$activetime = Carbon::now()->subSeconds($activelimit);
$activetime = Carbon::now()->subMinutes($activelimit); // 一時的にn分にする


// XXX: ユーザーの認証
// $auth = UserDao::authUser($uuid);
// if($auth !== true){
//     Page::complete($auth);
// }

// $user = new User($uuid);

try{
        
    // XXX: 一定時間更新のないユーザーを削除
    ActiveDao::delTimeoutUsers($activetime);

    // XXX: ステータスがwaitのルームを取得し、1レコードづつrm.pplの更新を行う
    RoomDao::updRoomppl();

    // XXX: 作成から一定時間経っている、もしくはppl=0のルームのstatを"closed"へ更新
    RoomDao::closeRoom($closetime);

    // XXX: statが"wait"の部屋を全件取得
    $roomlist = RoomDao::getWaitingRooms();
    
}catch(Exception $e){
    Page::complete(550);
}

Page::complete(200,$roomlist);
//endProces("Success!!", $maxflg, $member);
// $jlist = json_encode($roomlist, JSON_UNESCAPED_UNICODE);
// echo $jlist;
