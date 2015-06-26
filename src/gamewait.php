<?php
// gamewait.php

require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');
require_once('model/ActiveDao.inc');
require_once('model/UserDao.inc');

require_once(dirname(__FILE__). '/../lib/Carbon/Carbon.php');
use Carbon\Carbon;

// req:
$uuid = Util::h($_POST['uuid']);
// res:
$response = array(
    "status"=>null,
    "maxflg"=>null,
    "memberlist"=>array()
);
Page::setRes($response);

if(UserDao::authUser($uuid) !== true){
    Page::complete(452);
}

$user = new User($uuid);
$ua_id = $user->getUAid();
if(!$ua_id){
    Page::complete(4533);
}
$ru_id = $user->getRUid();
$rm_id = $user->getRMid();

$ua = ActiveDao::getUser($ua_id);


$maxflg = false;
$memberlist = array();

// ユーザー制限時間
$activetime = Time::getActive();


try{
    // XXX: タイムオーバーとuser_activeに存在しなかった場合の処理
    $time = Time::s($ua["ua_time"]);
    if(Time::s($activetime) > $time){
        Page::complete(453);
    }

    // XXX: 同じルーム内の一定時間反応の無い者をルームから削除
    //ActiveDao::delTimeoutUserFromRoom($rm_id, $activetime);
    // 現在のルーム内のユーザーを取得
    $sql = "SELECT * FROM room_user WHERE ru_rm_id = :rm_id;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 各ユーザーのra.ra_timeを取得
    foreach($rows as $row){
        $ruid = $row['ru_id'];
        $sql = "SELECT * FROM user_active WHERE ua_ru_id = :ru_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ru_id', $ruid, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $uatime = $row['ua_time'];
        if(Carbon::parse($uatime)->isPast()){
            // ルーム内に残る
        }else{
            // ルーム内から削除
            UA::delUser($uaid);
            UserDao::delUser($ruid, $rmid);
        }
    }
    

    // XXX: 現在のルームのroom_master.pplの更新
    RoomDao::updRoomppl();
    
    // XXX: 同じルーム内のユーザーを取得
    $memberlist = RoomDao::getUserInRoom($rm_id);
    
    // XXX: 現在のルーム内人数が埋まったかを確認
    $maxflg = RoomDao::chkPpl($rm_id);
    // あとで"""絶対に"""分ける
    // XXX: Page::ルームステータスチェック($想定する現在のステータス, $変更するステータス)
    //       みたいなのを作る
    if($maxflg){
        $sql = "SELECT * FROM room_master WHERE rm_id = :rm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stat = $row['rm_stat'];
        
        // XXX: 直したい。statは数値にすればよかった.
        if($stat == "wait"){
            $sql = 'UPDATE room_master SET rm_stat = "auctionwait" WHERE rm_id = :rm_id;';
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    /**/
}catch(Exception $e){
    // echo $e->getMessage();
    Page::complete(550);
}

Page::complete(200, $maxflg, $memberlist);

