<?php

// Useractivedb.inc

class Useractivedb extends Userdb{
    public static function getUser(){
        
    }

    public static function updUsers($limit){
        $sql = 'DELETE FROM user_active  WHERE ua_active > :limit;';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_STR);
        $stmt->execute();

        foreach($stmt as $row){
            $um_id = $row['ua_um_id'];
            $rm_id = $row['ua_rm_id'];
            
            static::delUser($um_id);
            RU::delUser($um_id);
            RM::subPpl($rm_id);
        }
        
        // あとルームユーザーでも消す

        //　あとrm.pplの更新
    }

    public static function delUser($um_id){

    }
}
