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
$price = intval(Util::h($_POST['price']));
$ma_id = intval(Util::h($_POST['auction_id']));
// res:
$response = array(
    "status"=>null,
    "price"=>null,
    "monster_id"=>null,
    "user_id"=>null
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
// $ru_id = $user->getRUid();
// $rm_id = $user->getRMid();



try{
    // XXX: 指定されたauction_idでmonster_auctionをSELECTし、現在のprice、mm_id、um_idを返す
    $sql = "SELECT * FROM monster_auction WHERE ma_id = :ma_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':ma_id', $ma_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // 最終価格の取得
    $price = $row['ma_price'];
    
    // monster_idの取得
    $mm_id = $row['ma_mm_id'];

    // user_idの取得
    $ru_id = $row['ma_ru_id'];
    $row = RU::getUser($ru_id);
    $um_id = $row['ru_um_id'];
    
    
}catch(Exception $e){
    echo $e->getMessage();
    Page::complete(550);
}

Page::complete(200, $price, $mm_id, $um_id);

