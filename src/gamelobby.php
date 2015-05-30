<?php
// gamelobby.php

require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');
require_once('model/ActiveDao.inc');
require_once('model/UserDao.inc');


// req:
$uuid = Util::h(@$_POST['uuid']);
// res:
$response = array(
    "status"=>null
);
Page::setRes($response);


if(UserDao::authUser($uuid) !== true){
    Page::complete(452);
}

$user = new User($uuid);
$ru_id = $user->getRUid();
$time = Time::getNow();


try{
    // user_activeにユーザーを追加する
    ActiveDao::addUser($ru_id, $time);
    
}catch(Exception $e){
    Page::complete(550);
}

Page::complete(200);
