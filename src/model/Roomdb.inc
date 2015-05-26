<?php

class Roomdb{
    
    
    
    public static function chkActive(){
        $sql = 'UPDATE user_master SET um_active = null, um_rm_id = null WHERE um_active > :activetime;';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':activetime', $activetime, PDO::PARAM_STR);
        $stmt->execute();
    }
    
}