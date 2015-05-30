<?php
// UserDao
// ユーザーに関するデータを扱う
require_once('Dbh.inc');
require_once('UM.inc');

class UserDAO {
    // 渡されたuuidを持つユーザーが存在するか
    public static function authUser($uuid){
        $stmt = UM::getUser($uuid);
        
        $judge = $stmt->fetch()? true : 452;
        return $judge;
    }

    public static function getUser($uuid){
        $stmt = UM::getUser($uuid);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }
    
    // ユーザー登録
    public static function regUser($uuid, $name){
        try{
            Dbh::get()->beginTransaction();
            // ユーザーnameかuuidが存在していた場合はエラーコードを返す
            if(UM::getUserAtName($uuid, $name)->fetch()){
                return 451;
            }
            UM::addUser($uuid, $name);
            
            Dbh::get()->commit();
            return 200;
        }catch(Exception $error){
            Dbh::get()->rollback();
            return 500;
        }
    }

    // 1ユーザーの削除
    
}