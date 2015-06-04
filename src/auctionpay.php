<?php
// auctionpay.php

require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');
//require_once('model/ActiveDao.inc');
require_once('model/UserDao.inc');

// req:
$uuid = Util::h($_POST['uuid']);
$price = Util::h($_POST['price']);
$ma_id = Util::h($_POST['ma_id']);
// res:
$response = array(
    "status"=>null,
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


// どっかでDBに
$now_price = 10000;


try{
    // 指定されたオークションが現在開催されているか
    $sql = 'SELECT * FROM room_auction WHERE ra_rm_id = :rm_id;';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$row){
        Page::complete(500);
    }
    $now_maid = $row['ra_ma_id'];
    if($now_maid != $ma_id){
        Page::complete(453);
    }

    
    // $priceは所持金以内に収まっているか
    if($now_price >= $price){
        // monster_auctionを更新
        $sql = 'UPDATE monster_auction SET ma_price = :price WHERE ma_id = :ma_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ma_id", $ma_id, PDO::PARAM_INT);
        $stmt->execute();
        
    }else{
        Page::complete(400);
    }

}catch(Exception $e){
    //echo $e->getMessage();
    Page::complete(550);
}

Page::complete(200);

