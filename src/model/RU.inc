<?php
// Roomuserdb.inc
require_once('Dbh.inc');

class RU{
    public static function addUser($rm_id, $um_id, $ru_money){
        $sql = 'INSERT INTO room_user(ru_rm_id, ru_um_id, ru_money) VALUES(:rm_id, :um_id, :ru_money);';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->bindValue(':um_id', $um_id, PDO::PARAM_INT);
        $stmt->bindValue(':ru_money', $ru_money, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function getUser($ru_id){
        $sql = 'SELECT * FROM room_user WHERE ru_id = :ru_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ru_id', $ru_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
    
    public static function getUsers($rm_id){
        $sql = 'SELECT * FROM room_user WHERE ru_rm_id = :rm_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':rm_id', $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
    
    public static function getUsersAtUM($um_id){
        $sql = 'SELECT * FROM room_user WHERE ru_um_id = :um_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':um_id', $um_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
    
    public static function delUser($ru_id){
        $sql = 'DELETE FROM room_user WHERE ru_id = :ru_id';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ru_id', $ru_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}