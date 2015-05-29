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
}
