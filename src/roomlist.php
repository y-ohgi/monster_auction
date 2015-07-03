<?php

require_once('common/init.inc');

require_once(ROOT_DIR . 'model/Dbh.inc');
require_once(ROOT_DIR . 'controller/Page.inc');
require_once(ROOT_DIR . 'controller/User.inc');

require_once(ROOT_DIR . 'controller/Room.inc');

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
    // 待機状態の部屋でタイムアウトしているユーザーを削除
    $time = Carbon::now()->subMinutes(TIMER_USER_TIMEOUT);
    //$sql = "SELECT * FROM room_user WHERE ru_id = (SELECT ua_ru_id FROM user_active WHERE ua_time < :time)";
    $sql = "SELECT * FROM user_active LEFT JOIN room_user ON user_active.ua_ru_id = room_user.ru_id WHERE user_active.ua_time < :time;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":time", $time, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($rows as $row){
        $rmid = $row["ru_rm_id"];
        $umid = $row["ru_um_id"];
        
        $room = new Room($rmid);
        $room->leave($umid);
        $room->updPpl();
    }
    
    // ルーム人数が0人もしくはタイムアウトしているものを取得し削除する
    $time = Carbon::now()->subMinutes(TIMER_ROOM_TIMEOUT);
    $sql = 'SELECT * FROM room_master WHERE rm_ppl = 0 OR rm_created < :time;';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":time", $time, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($rows as $row){
        $rmid = $row["rm_id"];
        $room = new Room($rmid);
    }

    $sql = "SELECT * FROM room_master WHERE rm_stat = :stat;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":stat", ROOM_WAIT, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
        $rmid = $row["rm_id"];
        $room = new Room($rmid);
        $room->updPpl();
    }
    
    
    
    // ルーム一覧を表示
    $stat = ROOM_WAIT;
    $sql = 'SELECT rm_id, rm_title, rm_ppl, rm_max FROM room_master WHERE rm_stat = "wait";';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(":stat", $stat, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $rooms = $rows? $rows : array();
    /**/
}catch(Exception $e){
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS, $rooms);



