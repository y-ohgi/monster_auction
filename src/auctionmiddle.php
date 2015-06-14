<?php
// auctionmiddle.php

require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');
//require_once('model/ActiveDao.inc');
require_once('model/UserDao.inc');

// res:
$response = array(
    "status"=>null,
    "timer"=>null,
    "auction_id"=>null, //"ma_id"=>null,
    "monster_id"=>null,
    "user_id"=>null,
    "price"=>null
);
Page::setRes($response);
// req:
if(UserDao::authUser($uuid) !== true){
    Page::complete(452);
}
$uuid = Util::h($_POST['uuid']);


$user = new User($uuid);
$ua_id = $user->getUAid();
if(!$ua_id){
    Page::complete(453);
}
//$ru_id = $user->getRUid();
//$rm_id = $user->getRMid();


// 残り時間
$time;


try{
    // ステータスチェック
    //  XXX: ステータスバリデート($想定するステータス); 違ったら300を返す。をつくる
    $sql = "SELECT * FROM room_master WHERE rm_id = :rm_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue($rm_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stat = $row['rm_stat'];
    if(!$stat = "auction"){
        Page::complete(300);
    }
    

    // 現在のオークション(ra_ma_id)が開催されているかのチェック
    // * カラムがnullだった場合は新規にra_ma_idを設定
    // * 数値が入っていた場合は残り時間を取得($time)
    //   - 残り時間を過ぎていた場合は現在設定されているma_idのレコードのma_closeflgをtrueにし、
    //      monste_auctionからma_closeflgがnullのma_idが若い物を新規に設定する
    //     - ma_closeflgが全てtrueになった場合、シーン遷移処理を行う
    //   - 残り時間内だった場合
    //     - 現在のmonster_auction.ma_idを取得($ma_id)
    //     - 現在のmonster_auction.ma_ru_idを取得し、user_master.um_idを取得する($user_id)
    //     - 現在のmonster_auction.ma_mm_idを取得する($monster_id)
    //     - 現在のmonster_auction.ma_priceを取得する($price)    
}catch(Exception $e){
    //echo $e->getMessage();
    Page::complete(550);
}

Page::complete(200, $time, $ma_id, $monster_id, $user_id, $price);

