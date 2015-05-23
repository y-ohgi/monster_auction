<?php

class DButil{
    private function __construct(){

    }

    public static function authUuid($uuid, $col = 'um_id'){
        $sql = 'SELECT :col FROM user_master WHERE um_id = :uuid';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':col', $col, PDO::)
        return $stmt;
    }
}