<?php
require_once('Db.inc');

//====== ユーザーDB処理 ======

class UM{
    // １レコード（１ユーザー）取得
    public static function getUser($uuid){
        $sql = 'SELECT * FROM user_master WHERE um_uuid = :uuid';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt;
    }

    // １レコードをuuidとnameで取得
    public static function getUserAtName($uuid, $name){
        $sql = 'SELECT um_name FROM user_master WHERE um_name = :name OR um_uuid = :uuid';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':name', (string)$name, PDO::PARAM_STR);
        $stmt->bindValue(':uuid', (string)$uuid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt;
    }

    public static function addUser($uuid, $name){
        $sql = 'INSERT INTO user_master(um_name, um_uuid) values(:name, :uuid)';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':name', (string)$name, PDO::PARAM_STR);
        $stmt->bindValue(':uuid', (string)$uuid, PDO::PARAM_STR);
        $stmt->execute();
    }
}
