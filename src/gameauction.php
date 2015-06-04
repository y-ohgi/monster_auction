<?php
// gameauction.php

require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');
//require_once('model/ActiveDao.inc');
require_once('model/UserDao.inc');

// req:
$uuid = Util::h($_POST['uuid']);
// res:
$response = array(
    "status"=>null,
    "time"=>null
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
//$ru_id = $user->getRUid();
$rm_id = $user->getRMid();

$time = 0;

try{
    $time_start = microtime(true);
    
    // XXX: オークションidを取得しに行き、なかった場合は初期処理を行い、オークションidを改めて取得
    //  複数作られる可能性がありそう
    
    // XXX: room_auctionを取得
    //  -> XXX: 取得できなかった場合は新しくレコードを挿入
    //    -> XXX: room_auction.rm_id .ma_id を挿入

    // XXX: room_auction.createdを取得

    // XXX: createdにTime::getAuctionStart()を足し、isPast()を行う

    // -> XXX: 過ぎていた場合はstatus:250を返す
    // -> XXX: 過ぎていなかった場合はstatus:200, time: $timeを返す
    
}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(550);
}

Page::complete(200, $time);

