<?php
// Roomuserdb.inc
require_once('Dbh.inc');

class RU{
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
    
    public static function deUser($um_id){
        $sql = 'DELETE FROM room_user WHERE ru_um_id = :um_id';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':um_id', $um_id, PDO::PARAM_STR);
        $stmt->execute();
    }
}