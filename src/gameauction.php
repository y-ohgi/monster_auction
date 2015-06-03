<?php
// gameauction.php

require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');
//require_once('model/ActiveDao.inc');
require_once('model/UserDao.inc');

// req:
$uuid = Util::h($_POST['uuid']);
// res:
$response = array(
    "status"=>null,
    "time"=>null, // 残り時間
    "auction_id"=>null, // 現在のオークションid
    "player_id"=>null, // 落札者id
    "price"=>null    
);
Page::setRes($response);

if(UserDao::authUser($uuid) !== true){
    Page::complete(452);
}

$user = new User($uuid);
$ua_id = $user->getUAid();
if(!$ua_id){
    Page::complete(453);
}
//$ru_id = $user->getRUid();
//$rm_id = $user->getRMid();

try{
    $time_start = microtime(true);
    
    // XXX: オークションidを取得しに行き、なかった場合は初期処理を行い、オークションidを改めて取得
    //  複数作られる可能性がありそう

    try{
        Dbh::get()->beginTransaction();
        // auction_idを取得
        $sql = 'SELECT * FROM auction_master WHERE am_rm_id = :rm_id;';
        $am = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // auction_masterに現在のルーム用のレコードがなかった場合
        if(!$am){
            // 人数分のuser_monsterを生成
            // auction_idを挿入？
            $sql = 'INSERT INTO auction_master(am_rm_id, am_time) VALUES(:rm_id, :time);';
            $id = Dbh::get()->lastInsertId():
            
        }
        Dbh::get()->commit();
    }catch(Exception $e){
        echo $e->getMessage();
        Dbh::get()->rollback();
    }
    // timeが0だった場合次のオークションに移行
    $sql = "";

    
    $timelimit = microtime(true) - $time_start;
    echo $timelimit;
    //
    
}catch(Exception $e){
    
}

Page::complete(200, $maxflg, $memberlist);

