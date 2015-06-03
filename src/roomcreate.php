<?php
// roomcreate.php

require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');
require_once('model/ActiveDao.inc');


// POSTじゃなかった場合
if($_SERVER["REQUEST_METHOD"] != "POST"){
    Page::complete(400);
}


// req:
$uuid = Util::h($_POST['uuid']);
$room_title = Util::h($_POST['room_title']);
$room_max = Util::h($_POST['room_max']);
// auth();
// res:
$response = array(
    "status"=>null
);
Page::setRes($response);

$user = new User($uuid);
$um_id = $user->getId();

       
try{
    // XXX: userの最終ルーム作成時間を見て、一定時間経っていたor NULLの場合のみ作成可能
    // XXX: 作成したユーザーidの登録

    // XXX: roomを登録
    $rm_id = RoomDao::addRoom($room_title, $room_max);

    // ルームに参加
    //   Xxx::joinRoom($rm_id)
    $code = RoomDao::joinRoom($rm_id, $um_id);
    /**/
}catch(Exception $e){
    Page::complete(550);
}

Page::complete(200);
