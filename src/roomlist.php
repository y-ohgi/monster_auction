<?php
// roomlist.php

require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('model/RoomDao.inc');
require_once('model/ActiveDao.inc');


// req:
//==========
// $uuid = Util::h($_POST['uuid']);
// XXX: ユーザーの認証
// $auth = UserDao::authUser($uuid);
// if($auth !== true){
//     Page::complete($auth);
// }
// $user = new User($uuid);
//==========

// res:
$response = array(
    "status"=>null,
    "roomlist"=>array()
);
Page::setRes($response);


// 部屋
$closetime = Time::getRoom();
// ユーザー
$activetime = Time::getActive();


try{
    // 一定時間更新のないユーザーを削除
    ActiveDao::delTimeoutUsers($activetime);

    // ステータスがwaitのルームを取得し、1レコードづつrm.pplの更新を行う
    RoomDao::updRoomppl();

    // 作成から一定時間経っている、もしくはppl=0のルームのstatを"closed"へ更新
    RoomDao::closeRoom($closetime);
    // XXX:"closed"へ変更したルームで使用するレコードを削除
    // - room_auction/monster_auction
    //   ra_idを取得し、monster_auctionレコードを全て削除してからroom_auctionを削除
    

    // statが"wait"の部屋を全件取得
    $roomlist = RoomDao::getWaitingRooms();
    
}catch(Exception $e){
    echo $e->getMessage();
    Page::complete(550);
}

Page::complete(200,$roomlist);
