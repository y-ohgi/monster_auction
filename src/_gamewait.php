<?php
// _gamewait.php

/*
  require_once
  Time
  Carbon
  User
  Page
*/



require_once(dirname(__FILE__).'/../lib/Carbon/Carbon.php');
use Carbon\Carbon;

class ActiveChecker{
    private $_time;
    private $_user_id;

    public function __constract($time, $user_id){
        $this->_time = $time;
        $this->_user_id = $user_id;
    }


    public function isActive(){
        $sql = "SELECT * FROM user_active WHERE ua_ru_id = :ru_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ru_id", $this->_user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $ua_time = $row['ua_time'];

        //$time = Carbon::parse($ua_time)->addSeconds($this->_time);
        $time = Carbon::parse($ua_time)->addMinitus($this->_time);
        if(isset($time) && $time->isPast()){
            return true;
        }else{
            return false;
        }
    }

    public function updTime(){
        $sql = "UPDATE user_active SET ua_time = :now WHERE ua_ru_id = :ru_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":now", Time::getNow(), PDO::PARAM_INT);
        $stmt->bindValue(":ru_id", $this->_user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $ua_time = $row['ua_time'];
        
    }

    public function delUser(){
        $sql = "DELETE FROM user_active WHERE ua_ru_id = :ru_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ru_id", $this->_user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}


//=============================

$uuid = h($_POST['uuid']);

// 現在のユーザー
$user = new User($uuid);
$um_id = $user->getId();
if(is_null($um_id)){
    Page::complete(452);
}
$ru_id = $user->getRUId();

// ユーザーがルーム内に居るかの生存確認
$time = Config::activeTimer();
$active = new ActiveChecker($time, $ru_id);
if(is_null($active->isActive())){
    Page::complete(453);
}
$active->update();

//============================


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
    $act = new ActiveChecker($time, $ruid);
    if($act->isActive()){
        // ルーム内に残る
    }else{
        // ルーム内から削除
        UA::delUser($uaid);
        UserDao::delUser($ruid, $rmid);
    }
}



// ルーム内の人数を更新
RoomDao::updRoomppl();

// 同じルーム内のユーザーを取得
$memberlist = RoomDao::getUserInRoom($rm_id);


// 現在のルーム内人数が埋まったかを確認
$maxflg = RoomDao::chkPpl($rm_id);
// あとで"""絶対に"""分ける
// Page::ルームステータスチェック($想定する現在のステータス, $変更するステータス)
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
