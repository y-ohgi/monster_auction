<?php
// roomexit.php

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
// auth();
// res:
$response = array(
    "status"=>null
);
Page::setRes($response);

$user = new User($uuid);
$ua_id = $user->getUAid();
$rm_id = $user->getUAid();
$ru_id = $user->getRUid();

       
try{
    UA::delUser($ua_id);
    UserDao::delUser($ru_id, $rm_id);
}catch(Exception $e){
    Page::complete(550);
}

Page::complete(200);
