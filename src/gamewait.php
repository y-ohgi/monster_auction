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
    "maxflg"=>null,
    "memberlist"=>null
);
Page::setResponse($response);

$maxflg = false;
$memberlist = array();

// 現在のルームステータスのチェック
$room = new Room($rm_id);
if(ROOM_WAIT !== $room->getStat()){
    if(ROOM_AUCTIONWAIT !== $room->getStat()){
        Page::complete(SEE_OTHER);
        return;
    }
}

// ユーザーがアクティブか
$active = new Active($ua_id);
// アクティブでなかった場合は処理を終了
if(false === $active->isAlive()){
    $room->leave($um_id);
    Page::complete(TIMEOUT);
    return;
}
// ユーザーのアクティブ時間を更新
$active->update();

try{
    Dbh::get()->beginTransaction();
    // 同じルーム内にいるユーザーを取得
    $sql = "SELECT * FROM user_active WHERE ua_ru_id = (SELECT ru_id FROM room_user WHERE ru_um_id = :um_id);";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":um_id", $um_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($rows as $row){
        $uaid = $row['ua_id'];
    
        // ユーザーがアクティブかのチェック
        $act = new Active($uaid);
        if(false === $act->isAlive()){
            // アクティブでなかった場合deleteする
            $sql = "SELECT ru_um_id FROM room_user WHERE ru_id = (SELECT ua_ru_id FROM user_active WHERE ua_id = :ua_id);";
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(":ua_id", $ua_id, PDO::PARAM_INT);
            $stmt->execute();
            $umid = $stmt->fetchColumn();
        
            $room->leave($umid);
        }
    }

    $room->updPpl();

    if($maxflg = $room->isMax()){
        $sql = "UPDATE room_master SET rm_stat = :stat WHERE rm_id = :rm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
        $stmt->bindValue(":stat", ROOM_AUCTIONWAIT, PDO::PARAM_INT);
        $stmt->execute();
        
    }

    // 現在のメンバーを返す
    $userlist = $room->getUsers();
    
    //Dbh::get()->rollback();
    Dbh::get()->commit();
}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS, $maxflg, $userlist);