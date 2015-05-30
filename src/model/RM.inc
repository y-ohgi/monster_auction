<?php

require_once('Db.inc');

class RM{
    public static function addRoom($title, $max){
        $sql = 'INSERT INTO room_master(rm_title, rm_max) VALUES(:title, :max);';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':max', $max, PDO::PARAM_INT);
        $stmt->execute();
        return Dbh::get()->lastInsertId();
    }
    
    public static function getRoom($rm_id){
        $sql = 'SELECT * FROM room_master WHERE rm_id = :rm_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public static function getWaitingRooms($s = null){
        $sql = $s? $s :'SELECT * FROM room_master WHERE rm_stat = "wait";';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public static function addPpl($rm_id){
        $sql = 'UPDATE room_master SET rm_ppl = rm_ppl+1 WHERE rm_id = :rm_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    public static function subPpl($rm_id){
        $sql = 'UPDATE room_master SET rm_ppl = rm_pp-1 WHERE rm_id = :rm_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function setPpl($rm_id, $ppl){
        $sql = 'UPDATE room_master SET rm_ppl =:ppl WHERE rm_id = :rm_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->bindValue(':ppl', $ppl, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function closeRoom($limit){
        $sql = 'UPDATE room_master SET rm_stat = "wait" WHERE rm_ppl = 0 OR rm_created < :closetime;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':closetime', $limit, PDO::PARAM_STR);
        $stmt->execute();
    }
}