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
    "memberlist"=>null
);
Page::setResponse($response);

// 現在のルームステータスのチェック
$room = new Room($rm_id);
if(ROOM_WAIT !== $room->getStat()){
    var_dump($user->getId());
    var_dump($user->getRMid());

    Page::complete(SEE_OTHER);
    return;
}

// // ユーザーがアクティブか
// $active = new Active($ua_id);
// // アクティブでなかった場合は処理を終了
// if(false === $active->isAlive()){
//     $room->leave($um_id);
//     Page::complete(TIMEOUT);
//     return;
// }
// // ユーザーのアクティブ時間を更新
// $active->update();

$userlist = array();

try{
    Dbh::get()->beginTransaction();
    
    
    Dbh::get()->rollback();
    //Dbh::get()->commit();
}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS, $userlist);