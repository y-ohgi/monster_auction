<?php
// gamewait.php

//=============
// タイムオーバーでワーニング出る、
//  が、正常なのと今日のやる気が尽きた。
ini_set('display_errors', 'Off');
//============



require_once('controller/Page.inc');
require_once('controller/Util.inc');
require_once('controller/Time.inc');
require_once('controller/User.inc');
require_once('model/RoomDao.inc');
require_once('model/ActiveDao.inc');
require_once('model/UserDao.inc');

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
    Page::complete(453);
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
    $sql = 'SELECT * FROM room_user WHERE ru_rm_id = :rm_id;';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
        // 各ユーザーのroom_user.id
        $ruid = $row['ru_id'];
        $rmid = $row['ru_rm_id'];
        
        $sql = 'SELECT * FROM user_active WHERE ua_ru_id = :ru_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ru_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $ua = $stmt->fetch(PDO::FETCH_ASSOC);
        $uaid = $ua['ua_id'];
        if($ua['ua_time'] > $time){
            // XXX: user_activeからユーザーを削除
            UA::delUser($uaid);
            // XXX: room_userからユーザーを削除
            UserDao::delUser($ruid, $rmid);
        }
    }
    
    
    // XXX: 現在のルームのroom_master.pplの更新
    $sql = 'SELECT count(*) FROM room_user WHERE ru_rm_id = :rm_id;';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $ppl = $stmt->fetchColumn();

    $sql = 'UPDATE room_master SET rm_ppl = :ppl WHERE rm_id = :rm_id;';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':ppl', $ppl, PDO::PARAM_INT);
    $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
    $stmt->execute();

    
    // XXX: 同じルーム内のユーザーを取得
    $sql = 'SELECT * FROM room_user WHERE ru_um_id = :rm_id;';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
        $umid = $row['ru_um_id'];
        $sql = 'SELECT * FROM user_master WHERE um_id = :um_id';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $um = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $memberlist[] = array(
            'id'=>$umid,
            'name'=>$um['um_name']
        );
    }
    
    
    // XXX: 現在のルーム内人数が埋まったかを確認
    $sql = 'SELECT * FROM room_master WHERE rm_id = :rm_id;';
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
    $stmt->execute();
    $um = $stmt->fetch();
    if($um['um_ppl'] < $um['um_max']){
        $maxflg = true;
    }
    
}catch(Exception $e){
    echo $e->getMessage();
    Page::complete(550);
}

Page::complete(200, $maxflg, $memberlist);

