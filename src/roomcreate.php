<?php

require_once("common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');
require_once(ROOT_DIR . 'controller/Page.inc');
require_once(ROOT_DIR . 'controller/User.inc');

require_once(ROOT_DIR . 'controller/Room.inc');
require_once(ROOT_DIR . 'controller/Auction.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;


$uuid = $_POST['uuid'];
$title = $_POST['room_title'];
$max = $_POST['room_max'];

$user = new User($uuid);
if($user->authUser()){
    Page::complete(BAD_REQUEST);
    return;
}
$um_id = $user->getId();

$response = array(
    "status"=>null,
    "monsterlist"=>null
);
Page::setResponse($response);

// ユーザーが作ったルームが存在しているか
$sql = "SELECT * FROM room_master WHERE rm_creater_id = :um_id;";
$stmt = Dbh::get()->prepare($sql);
$stmt->bindValue(":um_id", $um_id, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
// ルームを作成してから一定時間経っているか
if(isset($row)){
    if(Carbon::parse($row['rm_created'])->addMinutes(TIMER_ROOM_CANCREATE)->isPast()){
        Page::complete(CANT_CREATEROOM);
        return;
    }
}

// ルーム系
try{
    Dbh::get()->beginTransaction();
    
    // room作成
    $room = new Room();
    $rm_id = $room->create($title, $max, $um_id);
    
    // オークションで使用する各種レコードを作成
    $auction = new Auction();
    $auction->create($rm_id, $max);
    
    // roomへ参加
    $room->join($um_id);

    // monsterlistを返す
    // XXX: SELECTでASしてkeyネーム変える必要有るかも
    $monsterlist = $auction->getAuctionMonsters($rm_id);
    
    
    //Dbh::get()->rollback();
    Dbh::get()->commit();
}catch(Exception $e){
    Dbh::get()->rollback();
    Page::complete(SERVER_ERROR);
    echo $e->getMessage();
    return;
}

Page::complete(SUCCESS, $monsterlist);
