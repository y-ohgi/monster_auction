<?php

// Roomuserdb.inc

class Roomuserdb extends Roomdb{
    public static function getRoomusers($rm_id){
        $sql = 'SELECT * FROM room_user WHERE ru_rm_id = :rm_id;';
        $stmt = Dbh::get
    }

    public static function getUser($um_id){

    }
    
    public static function deUser($um_id){
        $sql = 'DELETE FROM room_user WHERE ru_um_id = :um_id';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':um_id', $um_id, PDO::PARAM_STR);
        $stmt->execute();
    }
}