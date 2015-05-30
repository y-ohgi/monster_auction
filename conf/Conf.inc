<?php

class Config{
    private static $data = array (
        'dsn'=> 'mysql:dbname=login_test; host=localhost; charset=utf8;',
        'user'=> 'testuser',
        'password'=> 'bear'
    );

    public static function get($k){
        return self::$data[$k];
        // XXX:throw
    }
}
