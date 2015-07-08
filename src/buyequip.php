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
$wep_id = intval($_POST['weapon_id']);
$gua_id = intval($_POST['guard_id']);
$acce_id =intval($_POST['accessory_id']);

$user = new User($uuid);
if($user->authUser()){
    Page::complete(BAD_REQUEST);
    return;
}
// $um_id = $user->getId();
$rm_id = $user->getRMid();
$ru_id = $user->getRUid();

$room = new Room($rm_id);
if(ROOM_AUCTION !== $room->getStat()){
    if(ROOM_EQUIP !== $room->getStat()){
        Page::complete(SEE_OTHER);
        return;
    }
}
        
$response = array(
    "status"=>null,
    "msg"=>null
);
Page::setResponse($response);

// TODO: モンスターを所持していなかった場合 retrun
$sql = "SELECT * FROM monster_auction WHERE ma_ru_id = :ru_id;";
$stmt = Dbh::get()->prepare($sql);
$stmt->bindValue(":ru_id", $ru_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$row){
    Page::complete(BAD_REQUEST, "モンスター持ってないようです");
    return;
}

try{
    Dbh::get()->beginTransaction();

    // 各idで検索、 取得
    // 金額を足す
    $sql = "SELECT sum(im_price) FROM item_master WHERE im_id IN (:wep, :guard, :acce);";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":wep", $wep_id, PDO::PARAM_INT);
    $stmt->bindValue(":guard", $gua_id, PDO::PARAM_INT);
    $stmt->bindValue(":acce", $acce_id, PDO::PARAM_INT);
    $stmt->execute();
    $sum = $stmt->fetchColumn();

    // 自身の所持金を取得
    $sql = "SELECT ru_money FROM room_user WHERE ru_id = :ru_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":ru_id", $ru_id, PDO::PARAM_INT);
    $stmt->execute();
    $money = $stmt->fetchColumn();
    
    // 所持金に足りなければ retrun
    if($sum > $money){
        Page::complete(BAD_REQUEST, "所持金が足りない $sum < $money");
        return;
    }else{
    // 所持金以内なら update
        $sql = "UPDATE monster_wrap SET mw_wep_id = :wep_id, mw_gua_id = :gua_id, mw_acc_id = :acce_id WHERE mw_ru_id = :ru_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":wep_id", $wep_id, PDO::PARAM_INT);
        $stmt->bindValue(":gua_id", $gua_id, PDO::PARAM_INT);
        $stmt->bindValue(":acce_id", $acce_id, PDO::PARAM_INT);
        $stmt->bindValue(":ru_id", $ru_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $sql = "UPDATE room_user SET ru_money = ru_money - :sum  WHERE ru_id = :ru_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":sum", $sum, PDO::PARAM_INT);
        $stmt->bindValue(":ru_id", $ru_id, PDO::PARAM_INT);
        $stmt->execute();
        
    }
    Dbh::get()->commit();
    //Dbh::get()->rollback();
}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS);


