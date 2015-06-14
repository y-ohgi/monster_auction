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

// オークション開始までの残り秒数
$time = 0;

try{
    $time_start = microtime(true);
    
    // XXX: ルーム内オークション管理レコードから時間(ra_time)を取得、なかった場合は現在時刻を挿入
    $sql = "SELECT * FROM room_auction WHERE ra_rm_id = :rm_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $ra_id = $row['ra_id'];
    $ra_time = $row['ra_time'];
    if(!$ra_time){
        $ra_time = Time::getNow();
        $sql = "UPDATE room_auction SET ra_time =:ra_time WHERE ra_id = :ra_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ra_time", $ra_time, PDO::PARAM_STR);
        $stmt->bindValue(":ra_id", $ra_time, PDO::PARAM_INT);
        $stmt->execute();
    }

    // その時間は過ぎたか
    if($ra_time->addSecond(Time::getAuctionStart())->isPast()){
        // 別ページヘのリクエストを勧める
        Page::complete(300);
    }

    $now = Time::getNow();
    $time = $ra_time->diffInSeconds($now);

}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(550);
}

Page::complete(200, $time);

