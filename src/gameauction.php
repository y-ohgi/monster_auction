<?php

require_once("common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');
require_once(ROOT_DIR . 'controller/Page.inc');
require_once(ROOT_DIR . 'controller/User.inc');

require_once(ROOT_DIR . 'controller/Room.inc');
require_once(ROOT_DIR . 'controller/Active.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;


$uuid = $_POST['uuid'];

$user = new User($uuid);
if($user->authUser()){
    Page::complete(BAD_REQUEST);
    return;
}
$um_id = $user->getId();
$rm_id = $user->getRMid();
$ua_id = $user->getUAid();

$response = array(
    "status"=>null,
    "timer"=>null
);
Page::setResponse($response);

$timer = 0;

$room = new Room($rm_id);
if($room->isMax() === false){
        Page::complete(SEE_OTHER);
        return;
}
if(ROOM_AUCTIONWAIT !== $room->getStat()){
    if(ROOM_WAIT !== $room->getStat()){
        Page::complete(SEE_OTHER);
        return;
    }
}

try{
    Dbh::get()->beginTransaction();

    // room_auctionにタイムが格納されているか確認後、
    //  存在していれば残り時間 もしくは303を返す
    $sql = "SELECT ra_time FROM room_auction WHERE ra_rm_id = :rm_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $time = $stmt->fetchColumn();

    $now = Carbon::now();

    if(is_null($time)){
        $sql = "UPDATE room_auction SET ra_time = :now WHERE ra_rm_id = :rm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
        $stmt->bindValue(":now", $now, PDO::PARAM_STR);
        $stmt->execute();

        $timer = TIMER_ROOM_WAIT2AUCTION;
    }else{
        $timer = TIMER_ROOM_WAIT2AUCTION - intval(Carbon::parse($time)->diffInSeconds($now));

        if(0 >= $timer){
            $sql = "UPDATE room_master SET rm_stat = :stat WHERE rm_id = :rm_id;";
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
            $stmt->bindValue(":stat", ROOM_AUCTION, PDO::PARAM_STR);
            $stmt->execute();
            
        }
    }

    
    /**/
    Dbh::get()->commit();
}catch(Exception $e){
    //Dbh::get()->rollback();
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS, $timer);



