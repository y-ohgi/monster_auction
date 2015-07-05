<?php

require_once("common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');
require_once(ROOT_DIR . 'controller/Page.inc');
require_once(ROOT_DIR . 'controller/User.inc');

require_once(ROOT_DIR . 'controller/Room.inc');
require_once(ROOT_DIR . 'controller/Active.inc');
require_once(ROOT_DIR . 'controller/Auction.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;


$uuid = $_POST['uuid'];
$ma_id = $_POST['auction_id'];

$user = new User($uuid);
if($user->authUser()){
    Page::complete(BAD_REQUEST);
    return;
}
$um_id = $user->getId();
$rm_id = $user->getRMid();
$ua_id = $user->getUAid();

$room = new Room($rm_id);
if(ROOM_AUCTION !== $room->getStat()){
    Page::complete(SEE_OTHER);
    return;
}

// status: 200,
// timer: 30,
// auction_id: 3,
// monster_id: 10,
// user_id: 5,
// price: 4000
$response = array(
    "status"=>null,
    "monster_id"=>null,
    "user_id"=>null,
    "price"=>null
);
Page::setResponse($response);


$auction = new Auction($rm_id);

try{
    Dbh::get()->beginTransaction();


    $adata = $auction->getAuction($ma_id);

    $monster_id = $adata['monster_id'];
    $user_id = $adata['user_id'];
    $price = $adata['price'];
    
    Dbh::get()->rollback();
}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS, $monster_id, $user_id, $price);



