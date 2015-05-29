<?php

//require_once('Db.inc');

class Roomdb extends Db{
    public static function getRoom($rm_id){
        $sql = 'SELECT * FROM room_master WHERE rm_id = :rm_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public static function getWaitingRooms(){
        $sql = 'SELECT * FROM room_master WHERE rm_stat = "wait";';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public static function subPpl($rm_id){
        $sql = 'UPDATE room_master SET rm_ppl = rm_pp-1 WHERE rm_id = :rm_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
}