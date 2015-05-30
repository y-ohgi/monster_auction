<?php

class Util{
    private function __construct(){}

    public static function showError(){
        ini_set('display_errors', '1');
        error_reporting(E_ALL);
    }

    // htmlのエスケープ
    public static function h($str){
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    // timestampをunixスタンプへ変更
    public static function s($time){
        return strtotime($time);
    }

    // 処理を終了する
    // XXX: 別クラスに分けたほうが？
    public static function complete(){
        //if(Db::disconnect){}

        exit();
    }
    
}

Util::showError();