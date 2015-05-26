<?php
require_once('Db.inc');

//====== ユーザーDB処理 ======

class Userdb{

    //=== SELECT系 ===//
    // uuidが存在するかをbooleanで判定するだけ
    public static function authUser($uuid){
        $stmt = self::getUuid($uuid);

        $judge = $stmt->fetch()? true : false;
        return $judge;
    }

    // レコードを取得する
    public static function getRecord($uuid){
        $stmt = self::getUuid($uuid, 'um_name');
        
        $row = $stmt->fetch();
        return $row;
    }

    // $stmtを返す
    // XXX:カラムを$colで指定したい
    public static function getUuid($uuid, $col = 'um_id'){
        // $sql = 'SELECT :col FROM user_master WHERE um_uuid = :uuid';
        $sql = 'SELECT * FROM user_master WHERE um_uuid = :uuid';
        $stmt = Db::getDbh()->prepare($sql);
        // $stmt->bindParam(':col', $col, PDO::PARAM_STR);
        $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        echo $col. " : ". $uuid;
        return $stmt;
    }

    //=== UPDATE系 ===
    // ユーザー登録
    public static function regUser($uuid, $name){
        // ユーザーnameかuuidが存在していた場合はエラーを返す
        $sql = 'SELECT um_name FROM user_master WHERE um_name = :name OR um_uuid = :uuid';
        $stmt = self::$dbh->prepare($sql);
        $stmt->bindValue(':name', (string)$name, PDO::PARAM_STR);
        $stmt->bindValue(':uuid', (string)$uuid, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->fetch()){
            return "名前が存在しています";
        }

        $sql = 'INSERT INTO user_master(um_name, um_uuid) values(:name, :uuid)';
        $stmt = self::$dbh->prepare($sql);
        $stmt->bindValue(':name', (string)$name, PDO::PARAM_STR);
        $stmt->bindValue(':uuid', (string)$uuid, PDO::PARAM_STR);
        $stmt->execute();

        return true;
    }
}
