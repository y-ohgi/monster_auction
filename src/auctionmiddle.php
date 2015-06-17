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
$uuid = Util::h(@$_POST['uuid']);


$user = new User($uuid);
$ua_id = $user->getUAid();
if(!$ua_id){
    Page::complete(453);
}
//$ru_id = $user->getRUid();
//$rm_id = $user->getRMid();

// room_auctionに入れる時間
$now = Time::getNow();
            
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
    $ra_id = $row['ra_id'];
    $stat = $row['rm_stat'];
    if(!$stat = "auction"){
        Page::complete(300);
    }
    

    // 現在のオークション(ra_ma_id)が開催されているかのチェック
    $sql = "SELECT * FROM room_auction WHERE ra_id = :ra_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':ma_id', $ra_id);
    $stmt->execute();
    $ra_ma_id = $row['ra_ma_id'];
    
    // * カラムがnullだった場合は新規にra_ma_idと、ra_timeを設定
    if(!$ra_ma_id){
        $sql = "SELECT * FROM monster_auction WHERE ma_ra_id = :ra_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ma_id', $ra_id);
        $stmt->execute();
        $row = $stmt->fetchColumn(PDO::FETCH_ASSOC);
        $ma_id = $row['ma_id'];

        $sql = "UPDATE room_auction SET ra_ma_id = :ma_id, ra_time = :ra_time WHERE ra_id = :ra_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ma_id', $ma_id);
        $stmt->bindValue(':ra_time', $now);
        $stmt->execute();
            
    }else{
        // * 数値(ra_ma_id)が入っていた場合は残り時間ra_timeを取得($time)
        $time = $row['ra_time'];
        
        //   - 残り時間を過ぎていた場合は現在設定されているma_idのレコードのma_closeflgをtrueにし、
        //      monste_auctionからma_closeflgがnullのma_idが若い物を新規に設定し、
        //      ma_timeに現在時刻を挿入する
        if($time->addSecond(Time::getAuctionTime())->isPast()){
            $sql = "UPDATE monster_auction SET ma_closeflg = 'true' WHERE ma_id = :ma_id;";
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(':ma_id', $ra_ma_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);


            $sql = "SELECT * FROM monster_auction WHERE ma_ra_id = :ra_id AND ra_closeflg != 'true' LIMIT 1;";
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(':ru_id', $ru_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$row){
                Page::complete(300);
            }

            $ma_id = $row['ma_id'];
            

            $sql = "UPDATE room_auction SET ra_ma_id = :ma_id, ra_time = :ra_time WHERE ra_id = :ra_id;";
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(':ma_id', $ma_id);
            $stmt->bindValue(':ra_time', $now);
            $stmt->execute();

        }
        //   - 残り時間内だった場合現在のroom_auctionを取得しする
        $sql = "SELECT * FROM room_auction WHERE ra_id = :ra_id;";
        
        //     - 現在のmonster_auction.ma_idを取得($ma_id)
        $ma_id = $row['ma_id'];
                
        //     - 現在のmonster_auction.ma_ru_idを取得し、user_master.um_idを取得する($user_id)
        $sql = "SELECT * FROM room_user WHERE ru_id = :ru_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ru_id', $ru_id);
        $stmt->execute();

        $rurow = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $rurow['ru_um_id'];
        if(!$user_id){
            $user_id = "未入札";
        }
        
        //     - 現在のmonster_auction.ma_mm_idを取得する($monster_id)
        $monster_id = $row['ma_mm_id'];
        
        //     - 現在のmonster_auction.ma_priceを取得する($price)
        $price = $row['ma_price'];
        
    }
}catch(Exception $e){
    //echo $e->getMessage();
    Page::complete(550);
}

Page::complete(200, $time, $ma_id, $monster_id, $user_id, $price);

