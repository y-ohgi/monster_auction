<?php
require_once(dirname(__FILE__).'/../../conf/Conf.inc');

class Db{
    protected static $dbh;
    
    protected function __construct(){}

    public static function createDbh(){
        try {
            self::$dbh = new PDO(Config::get('dsn'), Config::get('user'), Config::get('password'));
            // エラーの表示
            self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e){
            print('Error:'.$e -> getMessage());
            die();
        }
    }
    
    public static function getDbh(){
        return self::$dbh;
    }

    public static function disconnect(){
        self::$dbh = null;
    }
       
}

Db::createDbh();
