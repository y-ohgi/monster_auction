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
}

Util::showError();