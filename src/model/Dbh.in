<?php

require_once(dirname(__FILE__).'/../../conf/Conf.inc');

class Dbh{
    protected static $dbh = null;
    
    protected function __construct(){}

    public static function get(){
        if(static::$dbh === null){
            try {
                static::$dbh = new PDO(Config::get('dsn'), Config::get('user'), Config::get('password'));
                // エラーの表示
                static::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e){
                print('Error:'.$e -> getMessage());
                die();
            }
        }
        return static::$dbh;
    }

    public static function disconnect(){
        if(static::$dbh){
            static::$dbh = null;
        }
    }
}

