<?php
// gameauction.php

require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');
//require_once('model/ActiveDao.inc');
require_once('model/UserDao.inc');

// res:
$response = array(
    "status"=>null,
    "timer"=>null
);
Page::setRes($response);
// req:
if(UserDao::authUser($uuid) !== true){
    Page::complete(452);
}
$uuid = Util::h($_POST['uuid']);


$user = new User($uuid);
$ua_id = $user->getUAid();
if(!$ua_id){
    Page::complete(453);
}
//$ru_id = $user->getRUid();
$rm_id = $user->getRMid();

$timer;


try{
    /*
    // 人数差を求める
    $sql = 'SELECT * FROM room_master WHERE rm_id = :rm_id;';
    if($row['rm_ppl'] === $row['rm_max']){}
     */

    // 現在のオークションを取得
    $sql = 'SELECT * FROM room_auction WHERE ra_rm_id = :rm_id;';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // auctionが存在しなかった場合新たに作成
    if(!$row){
        $sql = 'INSERT INTO room_auction(ra_rm_id) VALUES(:rm_id);';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $sql = 'SELECT * FROM room_auction WHERE ra_rm_id = :rm_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $created = Carbon::parse($row['ra_created']);
    $targettime = $created->copy()->addSeconds(Time::getAuctionStart());
    // Time::AuctionStart()秒分経ったか
    if($targettime->isPast()){
        $timer = 0;
    // 残り秒を求める
    }else{
        $timer = $created->diffInSeconds($targettime);
    }


    
}catch(Exception $e){
    //echo $e->getMessage();
    Page::complete(550);
}

Page::complete(200, $timer);

