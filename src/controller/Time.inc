<?php

require_once(dirname(__FILE__).'/../../lib/Carbon/Carbon.php');
use Carbon\Carbon;

class Time{
    // 何時間までルームを生かすか
    private static $closelimit = 24;
    // 何秒までユーザーを生存とみなすか
    private static $activelimit = 30;

    public static function getRoom(){
        $closetime = Carbon::now()->subHours(static::$closelimit);
        return $closetime;
    }
    public static function getActive(){
        //$activetime = Carbon::now()->subSeconds($activelimit);
        $activetime = Carbon::now()->subMinutes(static::$activelimit); // 一時的にn分にする
        return $activetime;
    }
    public static function getNow(){
        $now = Carbon::now();
        return $now;
    }

    // timestampをunixスタンプへ変更
    public static function s($time){
        return strtotime($time);
    }

}