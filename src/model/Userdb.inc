<?php
require_once('Db.inc');

class Userdb extends Db{
    //====== ユーザー認証系 ======
    public static function authUuid($uuid){
        $stmt = self::getUuid($uuid);

        echo "<br />";
        //var_dump($stmt->fetch());
        $stmt->fetch();
        //$judge = $stmt->fetch()? true : false;
        $judge = $stmt->fetch()? "true" : "false";
        echo $judge;
        return $judge;
    }

    public static function getUuid($uuid, $col = 'um_id'){
        $sql = 'SELECT :col FROM user_master WHERE um_uuid = :uuid';
        $stmt = self::$dbh->prepare($sql);
        $stmt->bindValue(':col', $col, PDO::PARAM_STR);
        $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        echo $col. " : ". $uuid;
        return $stmt;
    }
}
