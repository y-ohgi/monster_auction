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
    var_dump($room->getStat());
    Page::complete(SEE_OTHER);
    return;
}

$response = array(
    "status"=>null,
    "timer"=>null,
    "auction_id"=>null,
    "monster_id"=>null,
    "user_id"=>null,
    "price"=>null
);
Page::setResponse($response);


$auction = new Auction($rm_id);

try{
    Dbh::get()->beginTransaction();

    // オークション入れ替え処理
    if(false === $auction->isAlive()){
        if(false === $auction->isSold()){
            
            // 強制購入処理
            $auction->forceBuy();
        }else{
            // 購入確定
            $auction->cmpBuy();
        }

        // 新規登録できなかった場合
        //  MEMO: 登録とその可否を同時に行っている
        if(false === $auction->setAuction()){
            // TODO:ステータスをかえる
            //   + timer_room_auction分加えて equipに変更？
            $sql = "UPDATE room_master SET rm_stat = :stat WHERE rm_id = :rm_id;";
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
            $stmt->bindValue(":stat", ROOM_EQUIP, PDO::PARAM_STR);
            $stmt->execute();
          
            Page::complete(SEE_OTHER);
            return;
        }
    }

    $adata = $auction->getAuction();

    $timer = $adata["timer"];
    $auction_id = $adata['auction_id'];
    $monster_id = $adata['monster_id'];
    $user_id = $adata['user_id'];
    $price = $adata['price'];    
    
    //Dbh::get()->rollback();
    Dbh::get()->commit();
}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS, $timer, $auction_id, $monster_id, $user_id, $price);



