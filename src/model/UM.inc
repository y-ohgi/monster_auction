<?php
require_once('Db.inc');

//====== ユーザーDB処理 ======

class Userdb{

    //=== SELECT系 ===//
    // 一行取得
    public static function getUser($uuid){
        $sql = 'SELECT * FROM user_master WHERE um_uuid = :uuid';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt;
    }

    // uuidが存在するかをbooleanで判定するだけ
    public static function authUser($uuid){
        $stmt = static::getUser($uuid);
        
        $judge = $stmt->fetch()? true : false;
        return $judge;
    }

    
    //=== UPDATE系 ===
    // ユーザー登録
    public static function regUser($uuid, $name){
        Dbh::get()->beginTransaction();
        // ユーザーnameかuuidが存在していた場合はエラーを返す
        $sql = 'SELECT um_name FROM user_master WHERE um_name = :name OR um_uuid = :uuid';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':name', (string)$name, PDO::PARAM_STR);
        $stmt->bindValue(':uuid', (string)$uuid, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->fetch()){
            Dbh::get()->commit();
            return false;
        }

        $sql = 'INSERT INTO user_master(um_name, um_uuid) values(:name, :uuid)';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':name', (string)$name, PDO::PARAM_STR);
        $stmt->bindValue(':uuid', (string)$uuid, PDO::PARAM_STR);
        $stmt->execute();

        Dbh::get()->commit();
        return true;
    }
}
