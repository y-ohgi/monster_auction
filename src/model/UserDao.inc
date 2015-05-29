<?php
// UserDao
// ユーザーに関するデータを扱う
require_once('Dbh.inc');

class UserDAO {
    // 渡されたuuidを持つユーザーが存在するか
    public static function authUser($uuid){
        $stmt = UM::getUser($uuid);
        
        $judge = $stmt->fetch()? true : false;
        return $judge;
    }
    
    // ユーザー登録
    public static function regUser($uuid, $name){
        try{
            Dbh::get()->beginTransaction();
            // ユーザーnameかuuidが存在していた場合はエラーコードを返す
            $sql = 'SELECT um_name FROM user_master WHERE um_name = :name OR um_uuid = :uuid';
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(':name', (string)$name, PDO::PARAM_STR);
            $stmt->bindValue(':uuid', (string)$uuid, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->fetch()){
                Dbh::get()->commit();
                return 451;
            }

            $sql = 'INSERT INTO user_master(um_name, um_uuid) values(:name, :uuid)';
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(':name', (string)$name, PDO::PARAM_STR);
            $stmt->bindValue(':uuid', (string)$uuid, PDO::PARAM_STR);
            $stmt->execute();

            Dbh::get()->commit();
            return 200;
        }catch(Exception $error){
            Dbh::get()->rollback();
            return 500;
        }
    }

    // 1ユーザーの取得
}