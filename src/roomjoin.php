<?php

require_once("common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');
require_once(ROOT_DIR . 'controller/Page.inc');
require_once(ROOT_DIR . 'controller/User.inc');

require_once(ROOT_DIR . 'controller/Room.inc');
require_once(ROOT_DIR . 'controller/Auction.inc');

$uuid = $_POST['uuid'];
$rm_id = $_POST['room_id'];

$user = new User($uuid);
if($user->authUser()){
    Page::complete(BAD_REQUEST);
    return;
}
$um_id = $user->getId();

$response = array(
    "status"=>null,
    "monsterlist"=>null
);
Page::setResponse($response);

// 現在のルームステータスのチェック
$room = new Room($rm_id);
if(ROOM_WAIT !== $room->getStat()){
    Page::complete(SEE_OTHER);
    return;
}

// ルーム系
try{
    Dbh::get()->beginTransaction();

    $room = new Room($rm_id);
    // roomへ参加
    if(false === $room->join($um_id)){
        Page::complete(FULL_OF_PEOPLE);
    }

    // monsterlistを返す
    // XXX: SELECTでASしてkeyネーム変える必要有るかも
    $auction = new Auction();
    $monsterlist = $auction->getAuctionMonsters($rm_id);
    
    Dbh::get()->commit();
}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS, $monsterlist);
