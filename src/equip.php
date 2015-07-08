<?php

require_once("common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');
require_once(ROOT_DIR . 'controller/Page.inc');
require_once(ROOT_DIR . 'controller/User.inc');

require_once(ROOT_DIR . 'controller/Room.inc');
//require_once(ROOT_DIR . 'controller/Active.inc');
//require_once(ROOT_DIR . 'controller/Auction.inc');
//require_once(ROOT_DIR . 'controller/Equip.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;


$uuid = $_POST['uuid'];

$user = new User($uuid);
if($user->authUser()){
    Page::complete(BAD_REQUEST);
    return;
}
// $um_id = $user->getId();
$rm_id = $user->getRMid();
// $ua_id = $user->getUAid();

$room = new Room($rm_id);
if(ROOM_AUCTION !== $room->getStat()){
    if(ROOM_EQUIP !== $room->getStat()){
        Page::complete(SEE_OTHER);
        return;
    }
}
        
$response = array(
    "status"=>null,
    "timer"=>null,
    "itemlist"=>null
);
Page::setResponse($response);

$itemlist = new arrayObject();

try{
    Dbh::get()->beginTransaction();

    $sql = "SELECT * FROM room_equip WHERE re_rm_id = :rm_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$row){
        
        $sql = "INSERT INTO room_equip(re_rm_id) VALUES(:rm_id);";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "SELECT * FROM room_equip WHERE re_rm_id = :rm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    $re_id = $row['re_id'];
    $time = $row['re_created'];
    
    $time = Carbon::parse($time);
    
    $sql = "SELECT rm_max FROM room_master WHERE rm_id = :rm_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $max = $stmt->fetchColumn();

    
    
    // 残り時間
    $sec = TIMER_AUCTION * intval($max);

    $addsectime = $time->addSeconds($sec);
    
    
    $now = Carbon::now();
    $timer = $addsectime->diffInSeconds($now);
    
    // (現在時刻+ TIMER_AUCTION * 最大人数)秒 が装備購入時間
    if($addsectime->isPast()){
        Page::complete(SEE_OTHER);
        return;
    }


    
    // 武器の取得
    $sql = "SELECT im_id AS item_id, im_name AS item_name, im_desc AS item_desc, im_price AS item_price FROM item_master WHERE im_site = 1;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->execute();
    $itemlist['weapon'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 防具の取得
    $sql = "SELECT im_id AS item_id, im_name AS item_name, im_desc AS item_desc, im_price AS item_price FROM item_master WHERE im_site = 1;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->execute();
    $itemist['guard'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // アクセサリの取得
    $sql = "SELECT im_id AS item_id, im_name AS item_name, im_desc AS item_desc, im_price AS item_price FROM item_master WHERE im_site = 1;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->execute();
    $itemlist['accessory'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /**/
    Dbh::get()->commit();
    //Dbh::get()->rollback();
}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS, $timer, $itemlist);


