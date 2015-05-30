<?php
// gamewait.php

//=============
// タイムオーバーでワーニング出る、
//  が、正常なのと今日のやる気が尽きた。
ini_set('display_errors', 'Off');
//============



require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');
require_once('model/ActiveDao.inc');
require_once('model/UserDao.inc');

// req:
$uuid = Util::h($_POST['uuid']);
// res:
$response = array(
    "status"=>null,
    "maxflg"=>null,
    "memberlist"=>array()
);
Page::setRes($response);

if(UserDao::authUser($uuid) !== true){
    Page::complete(452);
}

$user = new User($uuid);
$ua_id = $user->getUAid();
if(!$ua_id){
    Page::complete(453);
}
$ru_id = $user->getRUid();
$rm_id = $user->getRMid();

$ua = ActiveDao::getUser($ua_id);


$maxflg = false;
$memberlist = array();

// ユーザー制限時間
$activetime = Time::getActive();


try{
    // XXX: タイムオーバーとuser_activeに存在しなかった場合の処理
    $time = Time::s($ua["ua_time"]);
    if(Time::s($activetime) > $time){
        Page::complete(453);
    }

    // XXX: 同じルーム内の一定時間反応の無い者をルームから削除
    ActiveDao::delTimeoutUserFromRoom($rm_id, $activetime);

    // XXX: 現在のルームのroom_master.pplの更新
    RoomDao::updRoomppl();
    
    // XXX: 同じルーム内のユーザーを取得
    $memberlist = RoomDao::getUserInRoom($rm_id);
    
    // XXX: 現在のルーム内人数が埋まったかを確認
    $maxflg = RoomDao::chkPpl($rm_id);
    /**/
}catch(Exception $e){
    // echo $e->getMessage();
    Page::complete(550);
}

Page::complete(200, $maxflg, $memberlist);

