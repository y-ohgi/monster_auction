<?php

require_once("common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');
require_once(ROOT_DIR . 'controller/Page.inc');
require_once(ROOT_DIR . 'controller/User.inc');

require_once(ROOT_DIR . 'controller/Room.inc');

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


// ルーム系
try{
    Dbh::get()->beginTransaction();
    
    // roomへ参加
    $room->join($um_id, $rm_id);

    // monsterlistを返す
    // XXX: SELECTでASしてkeyネーム変える必要有るかも
    $monsterlist = $auction->getAuctionMonsters($rm_id);
    
    Dbh::get()->commit();
}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS, $monsterlist);
