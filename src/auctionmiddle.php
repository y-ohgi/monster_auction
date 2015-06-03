<?php
// auctionmiddle.php

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
    "timer"=>null,
    "ma_id"=>null,
    "user_id"=>null,
    "price"=>null
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
//$rm_id = $user->getRMid();


$timer;


try{
    $sql = 'SELECT * FROM room_auction WHERE ra_rm_id = :rm_id;';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $ma_id = $row['ra_ma_id'];

    // 時間チェック
    $created = Carbon::parse($row['ra_created']);
    $targettime = $created->copy()->addSeconds(Time::getAuctionTime());
    if($targettime->isPast()){
        // room_auctionが参照するmonster_auctionを変更
        
        $timer = Time::getAuctionTime();
    // 残り秒を求める
    }else{
        $timer = $created->diffInSeconds($targettime);
    }

    
    $sql = 'SELECT * FROM monster_auction WHERE ma_id = :ma_id;';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":ma_id", $ma_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $price = $row['ma_price'];
    
    
    $ru_id = $row['ma_ru_id'];
    // ru_idからum_idを取得
    $sql = "SELECT * FROM room_user WHERE ru_id = :ru_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":ru_id", $ru_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_id = $row['ru_um_id'];
    
    
}catch(Exception $e){
    //echo $e->getMessage();
    Page::complete(550);
}

Page::complete(200, $timer, $ma_id, $user_id, $price);

