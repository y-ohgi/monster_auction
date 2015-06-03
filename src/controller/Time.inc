<?php

require_once(dirname(__FILE__).'/../../lib/Carbon/Carbon.php');
use Carbon\Carbon;

class Time{
    // 何時間までルームを生かすか
    private static $closelimit_ = 24;
    // 何秒までユーザーを生存とみなすか
    private static $activelimit_ = 30;
    // (ルーム作成から)何秒後からオークションを始めるか
    private static $auctionstart_ = 10;
    // オークションの開催時間
    private static $auctionstart_ = 30;

    public static function getNow(){
        $now = Carbon::now();
        return $now;
    }
    public static function getRoom(){
        $closetime = Carbon::now()->subHours(static::$closelimit_);
        return $closetime;
    }
    public static function getActive(){
        //$activetime = Carbon::now()->subSeconds($activelimit);
        $activetime = Carbon::now()->subMinutes(static::$activelimit_); // 一時的にn分にする
        return $activetime;
    }
    public static function getAuctionStart(){
        return static::$auctionstart_;
    }
    public static function getAuctionTime(){
        return static::$auctiontime_;
    }
    
    

    // timestampをunixスタンプへ変更
    public static function s($time){
        return strtotime($time);
    }

}