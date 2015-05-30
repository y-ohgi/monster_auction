<?php
// Useractivedb.inc
require_once('Dbh.inc');

class UA {
    public static function addUser($ru_id, $time){
        $sql = 'INSERT INTO user_active(ua_ru_id, ua_time) VALUES(:ru_id, :time);';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ru_id', $ru_id, PDO::PARAM_INT);
        $stmt->bindValue(':time', $time, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function getUser($ua_id){
        $sql = 'SELECT * FROM user_active WHERE ua_id = :ua_id';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ua_id', $ua_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public static function getUserAtRU($ru_id){
        $sql = 'SELECT * FROM user_active WHERE ua_ru_id = :ru_id';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ru_id', $ru_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public static function delUser($ua_id){
        $sql = 'DELETE FROM user_active WHERE ua_id = :ua_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ua_id', $ua_id, PDO::PARAM_INT);
        $stmt->execute();
    }


    public static function getTimeoutUsers($limit){
        $sql = 'SELECT * FROM user_active WHERE ua_time > :limit;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
    
        
}
