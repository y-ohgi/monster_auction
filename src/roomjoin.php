<?php
// roomjoin.php

require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');


// req:
$uuid = Util::h($_POST['uuid']);
$rm_id = Util::h($_POST['room_id']);
// auth();
// res:
$response = array(
    "status"=>null
);
Page::setRes($response);

$user = new User($uuid);
$um_id = $user->getId();

       
// POSTじゃなかった場合
if($_SERVER["REQUEST_METHOD"] != "POST"){
    Page::complete(400);
}


try{
    // ルームに参加
    //   Xxx::joinRoom($rm_id)
    $code = RoomDao::joinRoom($rm_id, $um_id);
    
}catch(Exception $e){
    echo $e->getMessage();
    Page::complete(550);
}

Page::complete($code);
