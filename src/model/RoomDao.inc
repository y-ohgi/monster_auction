<?php
// RoomDao
// Roomに関する物の処理
// room_master room_userを取り扱う
require_once('Dbh.inc');
require_once('RM.inc');
require_once('RU.inc');

class RoomDao{
    public static function getUserAtUM($um_id){
        $stmt = RU::getUsersAtUM($um_id);
        $ruser = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ruser;
    }

    public static function getUserInRoom($rm_id){
        $memberlist = array();
        
        $sql = 'SELECT * FROM room_user WHERE ru_rm_id = :rm_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows as $row){
            $umid = $row['ru_um_id'];
        
            $sql = 'SELECT * FROM user_master WHERE um_id = :umid';
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(':umid', $umid, PDO::PARAM_INT);
            $stmt->execute();
            $um = $stmt->fetch(PDO::FETCH_ASSOC);

            $memberlist[] = array(
                'id'=>$umid,
                'name'=>$um['um_name']
            );
        }
        return $memberlist;

    }

    public static function chkPpl($rm_id){
        $maxflg = false;
        
        $sql = 'SELECT * FROM room_master WHERE rm_id = :rm_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $rm = $stmt->fetch();
        if($rm['rm_ppl'] => $rm['rm_max']){
            $maxflg = true;
        }
        return $maxflg;
    }

    // ルーム作成
    public static function addRoom($title, $max = 8){
        $rm_id = RM::addRoom($title, $max);
        return $rm_id;
    }

    public static function joinRoom($rm_id, $um_id){
        //transaction
        // XXX: 人数を見る
        // とりあえずここで所持金を設定
        $ru_money = 10000;
        $stmt = RM::getRoom($rm_id);
        $row = $stmt->fetch();
        if($row['rm_max'] > $row['rm_ppl']){
            // room_userに追加
            RU::addUser($rm_id, $um_id, $ru_money);
            // room_master.pplにインクリメント
            RM::addPpl($rm_id);

            return 200;
        }
        return 600;
    }

    public static function getWaitingRooms(){
        // 連想配列整形するより一発でとって来た方が楽なのでベタ書き。
        $sql = 'SELECT rm_id, rm_title, rm_ppl, rm_max FROM room_master WHERE rm_stat = "wait";';
        $stmt = RM::getWaitingRooms($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rooms = $rows? $rows : array();

        return $rooms;
    }

    public static function updRoomppl(){
        $stmt = RM::getWaitingRooms();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows as $row){
            $rm_id = $row['rm_id'];
            $ppl = RU::getUsers($rm_id)->rowCount();
            if($row['rm_ppl'] !== $ppl){
                RM::setPpl($rm_id, $ppl);
            }
        }
    }



    // close対象のルームをcloseする
    public static function closeRoom($limit){
        RM::closeRoom($limit);
    }
}
