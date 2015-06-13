<?php
// roomcreate.php

require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');
require_once('model/ActiveDao.inc');

//-----
//require_once('model/Dbh.inc');
require_once(dirname(__FILE__).'/../lib/Carbon/Carbon.php');
use Carbon\Carbon;


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

    // roomを登録
    $rm_id = RoomDao::addRoom($room_title, $room_max);

    
    // XXX: ルーム用のroom_auctionレコードを作成後
    //       monster_auctionレコードを$room_max分作成
    $time = Carbon::now();

    // モンスター数分の数値の格納された配列
    $ma_mmid_ary = range(1, 20);
    shuffle($ma_mmid_ary);

    $sql = "INSERT INTO room_auction(ra_rm_id, ra_time) VALUES(:rm_id, :time);";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
    $stmt->bindValue(':time', $time, PDO::PARAM_STR);
    $stmt->execute();
    
    $ra_id = Dbh::get()->lastInsertId();
    
    // 人数分のmonster_auctionレコードを作成
    for($i=0;$i<$room_max;$i++){
        $mm_id = $ma_mmid_ary[$i];
        
        // mmから取り出し
        $sql = "SELECT * FROM monster_master WHERE mm_id = :mm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':mm_id', $mm_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $mm_price = $row['mm_price'];
        
        $sql = "INSERT INTO monster_auction(ma_ra_id, ma_mm_id, ma_price) VALUES(:ra_id, :mm_id, :price);";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ra_id', $ra_id, PDO::PARAM_INT);
        $stmt->bindValue(':mm_id', $mm_id, PDO::PARAM_INT);
        $stmt->bindValue(':price', $mm_price, PDO::PARAM_INT);
        $stmt->execute();
    }

    // ルームに参加
    $code = RoomDao::joinRoom($rm_id, $um_id);
    /**/
}catch(Exception $e){
    echo $e->getMessage();
    Page::complete(550);
}

Page::complete($code);
