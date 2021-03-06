<?php
require_once(ROOT_DIR . "common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;

class Page{
    protected static $response = array();

    // responseするデータ配列の初期設定
    public static function setResponse($ary){
        static::$response = $ary;
    }

    // $responseのvalueに値を設定
    public static function incResponse(){
        $i = 0;
        foreach(static::$response as $key=>$value){
            static::$response[$key] = func_get_arg($i);
            $i++;
        }
    }

    // 現在のresponseデータを返す
    public static function getResponse(){
        return static::$response;
    }

    // responseに配列を追加
    public static function mergeResponse($ary){
        static::$response = array_merge(self::$response, $ary);
    }

    // 処理を終了
    //  引数にはresponseのkey順
    public static function complete(){
        if(func_num_args() === 1){
            static::$response = array("status"=> func_get_arg(0) );
        }else{
            $i = 0;
            foreach(static::$response as $key=>$value){
                static::$response[$key] = func_get_arg($i);
                $i++;

            }
        }

        Dbh::disconnect();

        //header("Content-Type: application/json; charset=utf-8");
        print(json_encode(static::$response, JSON_UNESCAPED_UNICODE));
    }
}