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
$price = intval($_POST['price']);
$ma_id = intval($_POST['auction_id']);

$user = new User($uuid);
if($user->authUser()){
    Page::complete(BAD_REQUEST);
    return;
}
$um_id = $user->getId();
$rm_id = $user->getRMid();
$ru_id = $user->getRUid();
//$ua_id = $user->getUAid();

$room = new Room($rm_id);
if(ROOM_AUCTION !== $room->getStat()){
    Page::complete(SEE_OTHER);
    return;
}

$response = array(
    "status"=>null
);
Page::setResponse($response);

try{
    Dbh::get()->beginTransaction();

    // 所持金と 入札額の比較
    $sql = "SELECT ru_money FROM room_user WHERE ru_um_id = :um_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":um_id", $um_id, PDO::PARAM_INT);
    $stmt->execute();
    $money = $stmt->fetchColumn();
    if($money < $price){
        Page::complete(BAD_REQUEST);
        return;
    }
    

    // 現在額の確認
    $sql = "SELECT ma_id, ma_price FROM room_auction LEFT JOIN monster_auction ON room_auction.ra_ma_id = monster_auction.ma_id WHERE ra_rm_id = :rm_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $nowprice = $row['ma_price'];
    $maid = $row['ma_id'];

    if($nowprice < $price || $maid != $ma_id){
        Page::complete(BAD_REQUEST);
        return;
    }
    /*
    // 入札
    $sql = "UPDATE monster_auction SET ma_price = :price, ma_ru_id = :ru_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":price", $price, PDO::PARAM_INT);
    $stmt->bindValue(":ru_id", $ru_id, PDO::PARAM_INT);
    $stmt->execute();

    /**/
    //Dbh::get()->rollback();
    Dbh::get()->commit();
}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS);
