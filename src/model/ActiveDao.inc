<?php
// ActiveDao
// ping系処理
require_once('Dbh.inc');
require_once('UM.inc');
require_once('UA.inc');
require_once('UserDao.inc');

require_once(dirname(__FILE__). '/../../lib/Carbon/Carbon.php');
use Carbon\Carbon;

class ActiveDao{
    public static function getUser($ua_id){
        $stmt = UA::getUser($ua_id);
        $auser = $stmt->fetch(PDO::FETCH_ASSOC);
        return $auser;
    }
    // // room_user.idでレコードを取得する
    public static function getUserAtRU($ru_id){
        $stmt = UA::getUserAtRU($ru_id);
        $auser = $stmt->fetch(PDO::FETCH_ASSOC);
        return $auser;
    }
    
    public static function addUser($ru_id, $time){
        UA::addUser($ru_id, $time);
    }

    // 全ての一定時間反応のないユーザーを削除
    public static function delTimeoutUsers($limit){
        // XXX: timeoutの判定、nullになっている者は消さない
        $rows = UA::getTimeoutUsers($limit);

        if(!$rows)return;

        foreach($rows as $row){

            $ua_id = $row['ua_id'];
            $ru_id = $row['ua_ru_id'];

            $ru = RU::getUser($ru_id)->fetch(PDO::FETCH_ASSOC);
            $rm_id = $ru['ru_rm_id'];
            
            // XXX: user_activeからユーザーを削除
            UA::delUser($ua_id);
            // XXX: room_userからユーザーを削除
            UserDao::delUser($ru_id, $rm_id);
        }        
    }

    // ルーム内の一定時間反応のないユーザーを削除
    public static function delTimeoutUserFromRoom($rm_id,$time){
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
            echo $ua['ua_time'];
            if(!Carbon::parse($ua['ua_time'])->isPast()){
                // XXX: user_activeからユーザーを削除
                UA::delUser($uaid);
                // XXX: room_userからユーザーを削除
                UserDao::delUser($ruid, $rmid);
            }
        }


    }
}