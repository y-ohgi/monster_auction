<?php

require_once('common/init.inc');

require_once(ROOT_DIR . 'model/Dbh.inc');
require_once(ROOT_DIR . 'controller/Page.inc');
require_once(ROOT_DIR . 'controller/User.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;


$response = array(
    "status"=>null,
    "roomlist"=>null
);
Page::setResponse($response);

//==== 現在は認証を行わない
// $uuid = $_POST['uuid'];

// $user = new User($uuid);
// if($user->authUser()){
//     Page::complete(BAD_REQUEST);
//     return;
// }
//====


try{

    // ルーム人数が0人もしくはタイムアウトしているものを削除する
    $time = Carbon::now()->subMinutes(TIMER_ROOM_TIMEOUT);
    $sql = 'DELETE FROM room_master WHERE rm_ppl = 0 OR rm_created < :time;';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":time", $time, PDO::PARAM_STR);
    $stmt->execute();
    // TODO:その他もろもろルームに付随するものも削除    

    // ルーム一覧を表示
    $stat = ROOM_WAIT;
    $sql = 'SELECT rm_id, rm_title, rm_ppl, rm_max FROM room_master WHERE rm_stat = "wait";';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":stat", $stat, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $rooms = $rows? $rows : array();
    
}catch(Exception $e){
    Page::complete(SERVER_ERROR);
    return;
}

Page::complete(SUCCESS, $rooms);



